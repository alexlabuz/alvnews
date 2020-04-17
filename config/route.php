<?php

function getPage($db){
	$listPage = array();

	$listPage['home'] = "homeController";
	$listPage['article'] = "afficheArticleController";
	$listPage['inscription'] = "inscriptionController";
	$listPage['connexion'] = "connexionController";
	$listPage['deconnexion'] = "deconnexionController";
	$listPage['profil'] = "profilController";
	$listPage['updateUser'] = "updateUserController";
	$listPage['updatePassword'] = "updatePasswordController";
	$listPage["deleteUser"] = "deleteUserController";
	$listPage["gestionUser"] = "gestionUserController";
	$listPage["gestionTheme"] = "themeController";
	$listPage["updateTheme"] = "updateThemeController";
	$listPage["editor"] = "editorController";
	$listPage["deleteArticle"] = "removeArticleController";
	$listPage["gestionArticle"] = "gestionArticleController";
	$listPage["addComment"] = "addCommentController";
	$listPage["removeComment"] = "removeCommentController";
	$listPage["enregistre"] = "addRemoveEnregistreController";
	$listPage['404'] = "error404Controller";
	$listPage['maintenance'] = "maintenanceController";
	
	if($db != null){
		if(isset($_GET["page"])){
			if(isset($listPage[$_GET["page"]])){
				$page = $listPage[$_GET["page"]];
			}else{
				$page = $listPage["404"];
			}
		}else{
			$page = $listPage["home"];
		}
	}else{
		$page = $listPage["maintenance"];
	}

	return $page;
}