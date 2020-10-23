<?php

function addReponseController($twig, $db){
	if(isset($_POST["btRepondre"])){
		$content = $_POST["reponse"];
		$idUser = $_SESSION["id"];
		$idSujet = $_POST["idSujet"];

		$reponse = new ForumReponse($db);

		if(strlen($content) > 0){
			$exec = $reponse->add($content, $idUser, $idSujet);
			if($exec){
				return header("location:?page=viewSujet&id=".$idSujet);
			}else{
				echo "<a style='color:red'>Une erreur s'est produite lors de l'envoie de la réponse</a>";
			}
		}
	}else{
		return header("location:./");
	}


}