<?php

function addRemoveEnregistreController($twig, $db){
	if(isset($_GET["id"])){
		$enregistre = new Enregistre($db);
		$unEnregistre = $enregistre->selectByUserArticle($_SESSION["id"], $_GET["id"]);

		// Vérifie si l'article à déjà était enregistré
		if($unEnregistre == null){
			// Si ce n'est pas le cas on j'ajoute
			$exec = $enregistre->insert($_GET["id"], $_SESSION["id"]);
		}else{
			// Sinon on le supprime
			$exec = $enregistre->delete($unEnregistre["id"]);
		}
		// Redirige vers la page précedente
		header ("Location: $_SERVER[HTTP_REFERER]" );
	}
}