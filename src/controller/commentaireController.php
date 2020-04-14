<?php

function addCommentController($twig, $db){
	if(isset($_POST["btEnvoyer"])){
		$commentaire = new Comment($db);

		$text = $_POST["commentaire"];
		$idUser = $_SESSION["id"];
		$idArticle = $_POST["idArticle"];
		
		if(strlen($text) > 0){
			$exec = $commentaire->insert($text, $idArticle, $idUser);
		}

		header("Location:?page=article&id=".$idArticle);
	}
}

function removeCommentController($twig, $db){
	if(isset($_POST["idCommentaire"])){
		$commentaire = new Comment($db);
		$unCommentaire = $commentaire->selectById($_POST["idCommentaire"]);
		
		$idCommentaire = $_POST["idCommentaire"];
		$idArticle = $_POST["idArticle"];

		// Vérifie si le commentaire appartien bien à l'utilisateur
		if($unCommentaire["idUtilisateur"] == $_SESSION["id"]){
			$exec = $commentaire->delete($idCommentaire);
		}

		header("Location:?page=article&id=".$idArticle);
	}
}