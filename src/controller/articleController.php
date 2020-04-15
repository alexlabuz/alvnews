<?php

// Permet d'afficher l'article au public
function afficheArticleController($twig, $db){
	if(!isset($_GET["id"])){
		header("Location:./");
		exit;
	}
	$form = array();

	$article = new Article($db);
	$commentaire = new Comment($db);
	$unArticle = $article->selectById($_GET["id"]);

	if($unArticle == null){
		$form["errorMessage"] = "Toutes nos excuses mais l'article que vous souhaitez voir n'éxiste pas ou à etait supprimé";
		$unArticle["titre"] = "Erreur"; // Pour l'affichage dans la <title>
	}

	$listCommentaire = $commentaire->selectByArticle($_GET["id"]);
	echo $twig->render("article.html.twig", array("form" => $form, "article" => $unArticle, "commentaires" => $listCommentaire));
}

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

	// Appui sur le bouton d'envoie d'article
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
		$error = false;

		// Vérifie si il s'agit d'un nouveau article ou si c'est une mise à jour
		if($inputTitre != null && $inputTheme != null){
			if(!isset($_POST["idArticleUpdate"])){
				// Création d'un nouvel article
				$exec = $article->insert($inputTitre, $inputDescription, $inputImage, $inputArticle, $inputVisible, $inputTheme, $idUser);
			}else{
				// Mise à jour de l'article existant
				$exec = $article->update($inputTitre, $inputDescription, $inputImage, $inputArticle, $inputVisible, $inputTheme, $_POST["idArticleUpdate"]);
			}
			if(!$exec){$error = true;}
		}else{$error = true;}
		
		if($error){
			if(!isset($_POST["idArticleUpdate"])){
				header("Location:?page=editor&code=1");exit;
			}else{
				header("Location:?page=editor&id=".$_POST["idArticleUpdate"]."&code=1");exit;
			}
		}

		header("Location:?page=home"); // Succée de l'envoie
		exit;
	}

	$list = array();
	if(isset($_GET["id"])){
		$list = $article->selectById($_GET["id"]);
		if($list == null){
			$form["errorMessage"] = "L'article n'existe pas";
		}elseif($list["idUtilisateur"] != $unUtilisateur["id"] && $unUtilisateur["role"] != 3){
			$form["errorMessage"] = "Mais cet article ne vous appartient pas dis-donc !";
		}
	}

	if(isset($_GET["code"]) && $_GET["code"] == 1){
		$form["errorMessage"] = "Erreur dans l'envoie de l'article";
	}
	// Affichage du menu déroulant des thèmes
	$theme = new Theme($db);
	$form["theme"] = $theme->select();

	echo $twig->render("editor.html.twig", array("form" => $form, "article" => $list));
}

function removeArticleController($twig, $db){
	if(!isset($_SESSION["id"]) || !isset($_GET["id"])){
		header("Location:?page=home");
		exit;
	}

	$article = new Article($db);
	$unArticle = $article->selectById($_GET["id"]);
	$code = 1;

	// Nous véifions si l'article appartiens bien à l'utilisateur ou si il est admin
	if($unArticle["idUtilisateur"] == $_SESSION["id"] ||  $_SESSION["role"] == 3){
		$exec = $article->delete($_GET["id"]);
		if($exec){
			$code = 0;
		}
		
	}

	header("Location:?page=profil&code=". $code);
	exit;
}