<?php

// Controlleur qui Permet d'ajouter, de mettre à jour ou de supprimer des réponses
function reponseController($twig, $db){

	$reponse = new ForumReponse($db);
	$sujet = new ForumSujet($db);
	$user = new User($db);

	// Ajout de réponse
	if(isset($_POST["btRepondre"])){
		$content = $_POST["reponse"];
		$idUser = $_SESSION["id"];
		$idSujet = $_POST["idSujet"];

		$unSujet = $sujet->selectById($idSujet);

		if(strlen($content) > 0 && $unSujet["ouvert"] == 1){
			$exec = $reponse->add($content, $idUser, $idSujet);
			if(!$exec){
				echo "<a style='color:red'>Une erreur s'est produite lors de l'envoie de la réponse</a>";
				exit;
			}
		}

		return header("location:?page=viewSujet&id=".$idSujet);
	}

	// Mise à jour de réponse
	else if(isset($_POST["btUpdate"])){
		$content = $_POST["reponse"];
		$idReponse = $_POST["btUpdate"];

		$uneReponse = $reponse->selectById($idReponse);
		$unSujet = $sujet->selectById($uneReponse["idSujet"]);
		$unUser = $user->selectById($_SESSION["id"]);

		if(($uneReponse["idUser"] == $_SESSION["id"] || $unUser["role"] == 3) && $unSujet["ouvert"] == 1){
			$exec = $reponse->update($content, $idReponse);
			if(!$exec){
				echo "<a style='color:red'>Une erreur s'est produite lors de la mise à jour de la réponse</a>";
				exit;
			}

		}

		return header('Location:'.$_SERVER['HTTP_REFERER']);
	}

	// Supression de réponse
	else if(isset($_GET["id"])){
		$idReponse = $_GET["id"];

		$uneReponse = $reponse->selectById($idReponse);
		$unSujet = $sujet->selectById($uneReponse["idSujet"]);
		$unUser = $user->selectById($_SESSION["id"]);

		if(($uneReponse["idUser"] == $_SESSION["id"] || $unUser["role"] == 3) && $unSujet["ouvert"] == 1){
			$exec = $reponse->delete($idReponse);
			if(!$exec){
				echo "<a style='color:red'>Une erreur s'est produite lors de la supression de la réponse</a>";
				exit;
			}
		}
		
		return header('Location:'.$_SERVER['HTTP_REFERER']);
	}

	else{
		return header("location:./");
	}

}