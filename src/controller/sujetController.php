<?php

// Page d'acceuil des sujets qui afficher la liste des sujets ouverts
function homeSujetController($twig, $db){
	$form = array();
	$sujet = new ForumSujet($db);
	$sujets = $sujet->select(1, 0, 12);

	echo $twig->render("sujetView/sujets.html.twig", array("form" => $form, "sujets" => $sujets));
}

// Permet d'afficher un sujet
function viewSujetController($twig, $db){
	// Si aucun id est passé en paramètre en renvoie l'utilisateur vers la page d'accueil
	if(!isset($_GET["id"])){
		return header("location:./");
	}

	$form = array();
	$reponses = array();
	$sujet = new ForumSujet($db);
	$unSujet = $sujet->selectById($_GET["id"]);


	if($unSujet != null){

		// Si le bouton "fermer le sujet" à était cliqué
		if(isset($_GET["close"]) && $_GET["close"] == true){

			if($unSujet["idUser"] == $_SESSION["id"]){
				$sujet->updateOuvert(0, $unSujet["id"]);
			}

			return header("location:?page=viewSujet&id=".$_GET["id"]);
		}

		// Si l'utilisateur n'a pas d'image de profil on lui met celle par défaut
		if($unSujet["userImage"] == null){
			$unSujet["userImage"] = "images/default/profil.png";
		}else{
			$unSujet["userImage"] = "images/profil/".$unSujet["userImage"];
		}

		$reponse = new ForumReponse($db);
		$reponses = $reponse->selectBySujet($_GET["id"]);
		$form["nb_reponse"] = count($reponses);

	}else{
		$form["errorMessage"] = "Toutes nos excuses mais le sujet que vous souhaitez voir n'éxiste pas ou à était supprimé";
		$unSujet["titre"] = "Erreur"; // Pour l'affichage dans la <title>
	}

	echo $twig->render("sujetView/viewSujet.html.twig", array("form" => $form, "sujet" => $unSujet, "reponses" => $reponses));
}

// Permet d'ajouter un nouveau sujet
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

		$exec = $sujet->add($title, $content, $idUser);
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

	echo $twig->render("sujetView/addSujet.html.twig", array("form" => $form));
}

// Supprimer les sujets cocher sur le profil
function removeSujetController($twig, $db){
	$sujet = new ForumSujet($db);
	$user = new User($db);
	$unUser = $user->selectById($_SESSION["id"]);
	$code = 0;

	$cocher = $_POST["cocher"];
	// Vérifie si des sujets ont était coché
	if(!empty($cocher)){
		foreach ($cocher as $id) {
			$unSujet = $sujet->selectById($id);
			// Vérifie si le sujet appartien à l'utilisateur ou si il est administrateur
			if($unSujet["idUser"] == $unUser["id"] || $unUser["role"] == 3){
				$exec = $sujet->delete($id);
				if(!$exec){
					$code = 1;
				}
			}else{
				$code = 1;
			}
		}
	}else{
		$code = null;
	}

	// Redirige vers la page précedente
	return header("Location: $_SERVER[HTTP_REFERER]&code=".$code);	
}