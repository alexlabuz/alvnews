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
		$image = $_POST["image"];
		$code = null;

		$utilisateur = new User($db);

		if($utilisateur->connect($email)){
			$code = 4;
		}else if($email == null || $nom == null || $password == null){
			$code = 3;
		}else if($password != $password2){
			$code = 1;
		}else{
			$utilisateur = new User($db);
			$exec = $utilisateur->insert($email, $nom, password_hash($password, PASSWORD_DEFAULT) , $image);
			if(!$exec){
				$code = 2;
			}
		}

		// Si l'inscritption à échoué on envoie dans l'url le code d'erreur
		if($code != null){
			header("Location:?page=inscription&code=".$code);
			exit;
		}
		header("Location:?page=inscription");
		exit;
	}

	// Code erreur renvoyé dans le GET
	$code[1] = "Les 2 mots de passe ne corresponde pas";
	$code[2] = "Une erreur s'est produite, veuillez réésayer";
	$code[3] = "Une erreur s'est produite lors de la saisie des données, veuillez réésayer";
	$code[4] = "Le mail saisie déjà existe déjà";

	if(isset($_GET["code"]) && isset($code[$_GET["code"]])){
		$form["message"] = $code[$_GET["code"]];
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
	// Si le bouton se connecter est actionné
	if(isset($_POST["btConnecter"])){
		$email = $_POST["email"];
		$password = $_POST["password"];
		$resteConnecte = false;
		if(isset($_POST['resteConnecte'])){
			$resteConnecte = true;
		}
		$code = null;

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
			}else{
				$code = 1;
			}
		}else{
			$code = 1;
		}
 
		// Si la connexion à échoué on envoie dans l'url le code d'erreur
		if($code != null){
			header("Location:?page=connexion&code=".$code);
			exit;
		}
		header("Location:index.php");
		exit;
	}

	// Code erreur renvoyé dans le GET
	$code[1] = "Identifiants incorrect, réésayez";

	if(isset($_GET["code"]) && isset($code[$_GET["code"]])){
		$form["message"] = $code[$_GET["code"]];
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

	echo $twig->render("profil.html.twig", array("form" => $form, "user" => $donnees));
}

// Met à jour le profil de l'utilisateur
function updateUserController($twig, $db){
	if(!isset($_SESSION["id"])){
		header("Location:?page=connexion");
		exit;
	}
	$form = array();

	if(isset($_POST['btUpdate'])){
		$email = $_POST["email"];
		$nom = $_POST["nom"];
		$image = $_POST["image"];
		$code = null;

		$utilisateur = new User($db);
		if($email == null || $nom == null){
			$code = 1;
		}else{
			$utilisateur = new User($db);
			$exec = $utilisateur->update($email, $nom, $image, $_SESSION['id']);
			if(!$exec){
				$code = 3;
			}else{
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
	$code[0] = "Les modifications ont bien étais changé";
	$code[1] = "Une erreur s'est produite lors de la saisie des données, veuillez réésayer";
	$code[2] = "Le mail saisie déjà existe déjà";
	$code[3] = "Une erreur s'est produite, veuillez réésayer";
	$code[4] = "Le mot de passe est incorrect, veuillez réésayer";

	if(isset($_GET["code"]) && isset($code[$_GET["code"]])){
		$form["message"] = $code[$_GET["code"]];
	}

	$utilisateur = new User($db);
	$donnees = $utilisateur->selectById($_SESSION["id"]);

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
		if($code != null){
			header("Location:?page=updateUser&code=".$code);
			exit;
		}
		header("Location:?page=home");
		exit;
	}
}

// Met à jour le mot de passe de l'utilisateur
function updatePasswordController($twig, $db){

}