<?php

// Controleur permettant l'inscription du client
function inscriptionController($twig, $db){
	if(isset($_SESSION["id"])){
		header("Location:?page=profil");
		exit;
	}

	$form = array();
	if(isset($_POST["btInscrire"])){
		$email = $_POST["email"];
		$nom = $_POST["nom"];
		$password = $_POST["password"];
		$password2 = $_POST["password2"];
		$code = null;

		$utilisateur = new User($db);

		if($utilisateur->connect($email)){
			$code = 3;
		}else if($email == null || $nom == null || $password == null){
			$code = 2;
		}else if($password != $password2){
			$code = 1;
		}else{
			$utilisateur = new User($db);
			$exec = $utilisateur->insert($email, $nom, password_hash($password, PASSWORD_DEFAULT));
			if(!$exec){
				$code = 2;
			}
		}

		// Si l'inscritption à échoué on envoie dans l'url le code d'erreur
		header("Location:?page=inscription&code=".$code);
		exit;
	}

	// Code erreur renvoyé dans le GET
	$message[1] = "Les 2 mots de passe ne corresponde pas";
	$message[2] = "Une erreur s'est produite, veuillez réésayer";
	$message[3] = "Le mail saisie déjà existe déjà";

	if(isset($_GET["code"]) && isset($message[$_GET["code"]])){
		$form["message"] = $message[$_GET["code"]];
	}
	
	echo $twig->render("inscription.html.twig", array("form" => $form));
}

// Controleur permettant la connexion du client
function connexionController($twig, $db){
	if(isset($_SESSION["id"])){
		header("Location:?page=profil");
		exit;
	}

	$form = array();
	if(isset($_POST["btConnecter"])){
		$email = $_POST["email"];
		$password = $_POST["password"];
		$resteConnecte = false;
		if(isset($_POST['resteConnecte'])){
			$resteConnecte = true;
		}

		$utilisateur = new User($db);
		$unUtilisateur = $utilisateur->connect($email);

		if($unUtilisateur){
			if(password_verify($password, $unUtilisateur['mdp'])){
				$_SESSION["id"] = $unUtilisateur["id"];
				$_SESSION["nom"] = $unUtilisateur["nom"];
				$_SESSION["email"] = $unUtilisateur["email"];
				$_SESSION["role"] = $unUtilisateur["role"];
				if($resteConnecte){
					setcookie('id_user', $unUtilisateur["id"], time() + 365*24*3600);
				}
				header("Location:?page=profil");
				exit;
			}
		}
 
		// Si la connexion à échoué on envoie dans l'url l'erreur
		header("Location:?page=connexion&error=true");
		exit;
	}

	if(isset($_GET["error"])){
		$form["error"] = $_GET["error"];
	}

	echo $twig->render("connexion.html.twig", array("form" => $form));
}

// Déconnecte l'utilisateur
function deconnexionController($twig, $db){
	session_unset();
 	session_destroy();
 	setcookie('id_user', "", time() + 365*24*3600);
 	header("Location:index.php");
}

// Affiche le profil de l'utilisateur
function profilController($twig, $db){
	if(!isset($_SESSION["id"])){
		header("Location:?page=connexion");
		exit;
	}
	$form = array();

	$utilisateur = new User($db);
	$donnees = $utilisateur->selectById($_SESSION["id"]);

	switch($donnees["role"]){
		case 1:
			$form["roleText"] = "Utilisateur";
		break;
		case 2:
			$form["roleText"] = "Modérateur";
		break;
		case 3:
			$form["roleText"] = "Administrateur";
		break;
	}

	// Code erreur renvoyé dans le GET
	$message[0] = "Les modifications ont bien étais changé";

	if(isset($_GET["code"]) && isset($message[$_GET["code"]])){
		$form["message"] = $message[$_GET["code"]];
	}

	echo $twig->render("profil.html.twig", array("form" => $form, "user" => $donnees));
}

// Met à jour le profil de l'utilisateur
function updateUserController($twig, $db){
	if(!isset($_SESSION["id"])){
		header("Location:?page=connexion");
		exit;
	}
	$form = array();

	$utilisateur = new User($db);
	$donnees = $utilisateur->selectById($_SESSION["id"]);

	if(isset($_POST['btUpdate'])){
		$email = $_POST["email"];	
		$nom = $_POST["nom"];
		$image = $_POST["image"];
		$code = null;

		$utilisateur = new User($db);
		if($utilisateur->connect($email)){
			$code = 2;
		}else if($nom == null){
			$code = 3;
		}else{
			$utilisateur = new User($db);
			if(empty($email)){
				$email = $donnees["email"];
			}
			$exec = $utilisateur->update($email, $nom, "images/img.jpg", $donnees["role"], $_SESSION['id']);
			if(!$exec){
				$code = 3;
			}else{
				// Réussite de la demande
				$_SESSION["nom"] = $nom;
				$_SESSION["email"] = $email;
				$code = 0;
			}
		}

		// Si l'inscritption à échoué on envoie dans l'url le code d'erreur
		header("Location:?page=updateUser&code=".$code);
		exit;
	}

	// Code erreur renvoyé dans le GET
	$message[0] = "Les modifications ont bien étais changé";
	$message[2] = "Le mail saisie déjà existe déjà";
	$message[3] = "Une erreur s'est produite, veuillez réésayer";
	$message[4] = "Le mot de passe est incorrect, veuillez réésayer";

	if(isset($_GET["code"]) && isset($message[$_GET["code"]])){
		$form["message"] = $message[$_GET["code"]];
	}

	echo $twig->render("updateUser.html.twig", array("form" => $form, "user" => $donnees));
}

// Supprime le profil de l'utilisateur
function deleteUserController($twig, $db){
	if(isset($_POST["btSupprimer"])){
		$utilisateur = new User($db);
		$unUtilisateur = $utilisateur->selectById($_SESSION["id"]);

		if(password_verify($_POST["password"], $unUtilisateur['mdp'])){
			$exec = $utilisateur->delete($_SESSION["id"]);
			session_unset();
			session_destroy();
			setcookie('id_user', "", time() + 365*24*3600);
			if(!$exec){
				$code = 3;
			}
		}else{
			$code = 4;
		}
		
		// Si l'inscritption à échoué on envoie dans l'url le code d'erreur
		header("Location:?page=updateUser&code=".$code);
		exit;
	}
}

// Met à jour le mot de passe de l'utilisateur
function updatePasswordController($twig, $db){
	if(!isset($_SESSION["id"])){
		header("Location:?page=connexion");
		exit;
	}
	$form = array();

	if(isset($_POST['btValider'])){
		$password = $_POST["password"];
		$password2 = $_POST["password2"];
		$code = null;

		if($password == $password2){
			$utilisateur = new User($db);
			$exec = $utilisateur->updateMdp(password_hash($password, PASSWORD_DEFAULT), $_SESSION["id"]);
			if(!$exec){
				$code = 1;
			}else{
				$code = 0;
				header("Location:?page=profil&code=". $code);
				exit;
			}
		}else{
			$code = 2;
		}

		header("Location:?page=updatePassword&code=". $code);
		exit;
	}

	$message[1] = "Echec lors de la demande";
	$message[2] = "Les 2 mot de passe sont différent, veuillez réésayer";

	if(isset($_GET["code"]) && isset($message[$_GET["code"]])){
		$form["message"] = $message[$_GET["code"]];
	}

	echo $twig->render("updatePassword.html.twig", array("form" => $form));
}