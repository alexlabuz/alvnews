<?php

// Controleur permettant l'inscription du client
function inscriptionController($twig, $db){
	$form = array();

	// Si le bouton s'inscrire est actionné
	if(isset($_POST["btInscrire"])){
		$email = $_POST["email"];
		$nom = $_POST["nom"];
		$password = $_POST["password"];
		$password2 = $_POST["password2"];
		$image = $_POST["image"];
		$code = null;

		if($password != $password2){
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

	if(isset($_GET["code"]) && isset($code[$_GET["code"]])){
		$form["message"] = $code[$_GET["code"]];
	}
	
	echo $twig->render("inscription.html.twig", array("form" => $form));
}

function connexionController($twig, $db){
	$form = array();

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
					setcookie('id_login', $unUtilisateur["id"], time() + 365*24*3600);
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

function deconnexionController($twig, $db){
	session_unset();
 	session_destroy();
 	setcookie('id_login', "", time() + 365*24*3600);
 	header("Location:index.php");
}
