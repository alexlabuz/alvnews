<?php

// Permet d'ajouter ou modifier un article
function editorController($twig, $db){
	if(!isset($_SESSION["id"]) || $_SESSION["role"] == 1){
		header("Location:?page=home");
		exit;
	}
	$form = array();
	$article = new Article($db);
	$user = new User($db);
	$unUtilisateur = $user->selectById($_SESSION["id"]);

	if(isset($_POST["btEnvoyer"])){
		$inputTitre = $_POST["titre"];
		$inputDescription = $_POST["description"];
		$inputImage = null;
		$inputArticle = $_POST["article"];
		$inputTheme = $_POST["theme"];
		$inputVisible = 0;
		if(isset($_POST["visible"])){
			$inputVisible = true;
		}
		$idUser = $unUtilisateur["id"];
		$code = 0;

		if($inputTitre == null || $inputTheme == null){
			$code = 1;
		}else{
			if(!isset($_POST["idArticleUpdate"])){
				// Création d'un nouvel article
				$exec = $article->insert($inputTitre, $inputDescription, $inputImage, $inputArticle, $inputVisible, $inputTheme, $idUser);
				if(!$exec){
					$code = 1;
				}
				header("Location:?page=editor&code=".$code);
				exit;
			}else{
				// Mise à jour de l'article existant
				$exec = $article->update($inputTitre, $inputDescription, $inputImage, $inputArticle, $inputVisible, $inputTheme, $_POST["idArticleUpdate"]);
				if(!$exec){
					$code = 1;
				}
				header("Location:?page=editor&id=".$_POST["idArticleUpdate"]."&code=".$code);
				exit;
			}
		}
		
		header("Location:./");
		exit;
	}

	$list = array();
	if(isset($_GET["id"])){
		$list = $article->selectById($_GET["id"]);
		if($list == null){
			$form["errorMessage"] = "L'article n'existe pas";
		}elseif($list["idUtilisateur"] != $unUtilisateur["id"]){
			$form["errorMessage"] = "Mais cet article ne vous appartient pas dis-donc !";
		}
	}

	$theme = new Theme($db);
	$form["theme"] = $theme->select();

	echo $twig->render("editor.html.twig", array("form" => $form, "article" => $list));
}

function removeArticleController($twig, $db){
	if(!isset($_SESSION["id"]) || !isset($_GET["id"])){
		header("Location:?page=home");
		exit;
	}
	$code = 0;

	$article = new Article($db);
	$unArticle = $article->selectById($_GET["id"]);

	if($unArticle["idUtilisateur"] == $_SESSION["id"]){
		$exec = $article->delete($_GET["id"]);
		if(!$exec){
			$code = 1;
		}
	}else{
		$code = 1;
	}

	header("Location:?page=profil&code=". $code);
	exit;
}