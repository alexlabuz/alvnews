<?php

function getPage($db){
	$listPage = array();

	/**
	 * ---> ROLE <---
	 * 0 : accessible par tous
	 * 1 : accessible par les clients connecté
	 * 2 : accessible par les modérateurs connecté
	 * 3 : accessible par les administrateurs connecté
	 */

	$listPage['home'] = "homeController;0";
	$listPage['search'] = "searchController;0";
	$listPage['article'] = "afficheArticleController;0";
	$listPage['theme'] = "listeThemeController;0";
	$listPage['inscription'] = "inscriptionController;0";
	$listPage['connexion'] = "connexionController;0";
	$listPage['validation'] = "validationController;0";
	$listPage['deconnexion'] = "deconnexionController;1";
	$listPage['profil'] = "profilController;1";
	$listPage['updateUser'] = "updateUserController;1";
	$listPage['updatePassword'] = "updatePasswordController;1";
	$listPage["deleteUser"] = "deleteUserController;1";
	$listPage["gestionUser"] = "gestionUserController;3";
	$listPage["forgotPassword"] = "forgotPasswordController;0";
	$listPage["updateForgotMdp"] = "updateForgotMdpController;0";
	$listPage["gestionTheme"] = "themeController;3";
	$listPage["editor"] = "editorController;2";
	$listPage["deleteArticle"] = "deleteArticleController;2";
	$listPage["gestionArticle"] = "gestionArticleController;3";
	$listPage["addComment"] = "addCommentController;1";
	$listPage["removeComment"] = "removeCommentController;1";
	$listPage["enregistre"] = "addRemoveEnregistreController;1";
	$listPage['suggestion'] = "suggestionController;0";
	$listPage['sujets'] = "homeSujetController;0";
	$listPage['viewSujet'] = "viewSujetController;0";
	$listPage['addSujet'] = "addSujetController;1";
	$listPage['removeSujet'] = "removeSujetController;1";
	$listPage['addReponse'] = "addReponseController;1";
	$listPage['404'] = "error404Controller;0";
	$listPage['maintenance'] = "maintenanceController;1";
	
	if($db != null){
		if(!isset($_GET["page"])){
			$page = "home";
		}elseif(!isset($listPage[$_GET["page"]])){
			$page = "404"; // ou home
		}else{
			$page = $_GET["page"];
		}
	}else{
		$page = "maintenance";
	}

	$explose = explode(";", $listPage[$page]);
	$controller = $explose[0];
	$role = $explose[1];

	if($role != 0){
		if(isset($_SESSION["id"])){
			if($_SESSION["role"] >= $role){
				$content = $controller;
			}else{
				$content = "homeController";
			}
		}else{
			$content = "homeController";
		}
	}else{
		$content = $controller;
	}

	return $content;
}