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
		$code = 5; // code de réussite

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
	
	$form["nonavbar"] = true;
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
		header("Location:?page=connexion&code=1");
		exit;
	}

	if(isset($_GET["code"])){
		$form["error"] = $_GET["code"];
	}

	$form["nonavbar"] = true;
	echo $twig->render("connexion.html.twig", array("form" => $form));
}

// Déconnecte l'utilisateur
function deconnexionController($twig, $db){
	session_unset();
 	session_destroy();
 	setcookie('id_user', "");
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

	$article = new Article($db);
	$form["listArticle"] = $article->selectByUser($donnees["id"]);
	$form["nbArticle"] = count($form["listArticle"]);

	$enregistre = new Enregistre($db);
	$form["listEnregistre"] = $enregistre->selectByUser($donnees["id"]);

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
		$image = $donnees["image"]; // Si aucune image sélectionné on remet l'ancienne
		$code = 0; // Code de réussite

		$upload = new Upload(["jpg", "png", "JPG", "PNG"], "images/profil", 2000000);
		$fichier = $upload->enregistrer("image");

		if($fichier["nom"] != null){
			/* Supprimer l'ancienne photo de profil */
			if(file_exists("images/profil/".$donnees["image"])){
				unlink("images/profil/".$donnees["image"]); 
			}
			$image = $fichier["nom"];
		}

		if(($utilisateur->connect($email)) && ($email != $donnees["email"])){
			$code = 2;
		}else if($nom == null || $email == null){
			$code = 3;
		}else{
			$exec = $utilisateur->update($email, $nom, $image, $donnees["role"], $_SESSION['id']);
			if(!$exec){
				$code = 3;
			}else{
				// Réussite de la demande
				$_SESSION["nom"] = $nom;
				$_SESSION["email"] = $email;
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
		$utilisateur = new User($db); // Récupère l'utilisateur pour la véfication du mdp
		$unUtilisateur = $utilisateur->selectById($_SESSION["id"]);

		if(password_verify($_POST["password"], $unUtilisateur['mdp'])){
				$exec = $utilisateur->delete($_SESSION["id"]);
				if(!$exec){
					$code = 3;
				}else{
					session_unset();
					session_destroy();
					setcookie('id_user', "", time() + 365*24*3600);
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
	$utilisateur = new User($db);
	$unUtilisateur = $utilisateur->selectById($_SESSION["id"]);

	if(isset($_POST['btValider'])){
		$oldPassword = $_POST["oldPassword"];
		$password = $_POST["password"];
		$password2 = $_POST["password2"];
		$code = null;

		if(!password_verify($oldPassword, $unUtilisateur["mdp"])){
			$code = 3;
		}else if($password != $password2){
			$code = 2;
		}else{
			$exec = $utilisateur->updateMdp(password_hash($password, PASSWORD_DEFAULT), $_SESSION["id"]);
			if(!$exec){
				$code = 1;
			}else{
				header("Location:?page=profil&code=0");
				exit;
			}
		}

		header("Location:?page=updatePassword&code=". $code);
		exit;
	}

	$message[1] = "Echec lors de la demande";
	$message[2] = "Les 2 mot de passe sont différent, veuillez réésayer";
	$message[3] = "Le mot de passe actuel est incorect";

	if(isset($_GET["code"]) && isset($message[$_GET["code"]])){
		$form["message"] = $message[$_GET["code"]];
	}

	echo $twig->render("updatePassword.html.twig", array("form" => $form));
}

// Permet la gestion administrative des utilisateurs
function gestionUserController($twig, $db){
	$form = array();
	if(!isset($_SESSION["id"]) || $_SESSION["role"] != 3){
		header("Location:?page=home");
		exit;
	}
	$user = new User($db);

	if(isset($_POST["btModifier"])){
		$idUser = $_POST["idUser"];
		$role = $_POST["role"];
		$nom = $_POST["nom"];
		$error = 1;

		$unUser = $user->selectById($idUser);

		if($nom != null && $role != null){
			$exec = $user->update($unUser["email"], $nom, $unUser["image"], $role, $idUser);
			if($exec){
				$error = 0;
			}
		}

		header("Location:?page=gestionUser&id=".$idUser."&error=".$error);
		exit;
	}

	if(isset($_POST["btSupprimer"])){
		$error = 1;
		$exec = $user->delete($_POST["idUser"]);
		if($exec){
			$error = 0;
		}

		header("Location:?page=gestionUser&error=".$error);
		exit;
	}

	if(isset($_GET["id"])){
		$form["user"] = $user->selectById($_GET["id"]);
		if($form["user"] == null || $form["user"]["role"] == 3){
			header("Location:?page=gestionUser");
			exit;
		}
	}else{
		$page = 0;
		if(isset($_GET["min"])){
			$page = $_GET["min"];
		}
		$min = $page * 30;
		$max = 30;
		$form["users"] = $user->select($min, $max);
	}

	if(isset($_GET["error"])){
		$form["error"] = $_GET["error"];
	}

	echo $twig->render("gestionUser.html.twig", array("form" => $form));
}