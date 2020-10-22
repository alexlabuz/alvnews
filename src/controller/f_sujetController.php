<?php

function addSujetController($twig, $db){
	$form = array();

	$sujet = new ForumSujet($db);
	$user = new User($db);
	$donneesUser = $user->selectById($_SESSION["id"]);

	// Lors de l'appui sur le bouton envoyer
	if(isset($_POST['btEnvoyer'])){
		$title = $_POST["title"];
		$content = $_POST["content"];
		$idUser = $donneesUser["id"];

		$exec = $sujet->ajout($title, $content, $idUser);
		if(!$exec){
			return header("location:?page=addSujet&code=1");
		}

		return header("location:?page=profil");
	}

	// En cas d'erreur
	if(isset($_GET["code"])){
		$form["errorMessage"] = "Echec d'envoie du sujet";
	}

	$form["nofooter"] = 0;

	echo $twig->render("addSujet.html.twig", array("form" => $form));
}