<?php
// Controleur qui récupère les articles de la page d'accueil
function homeController($twig, $db){
	$form = array();
	$article = new Article($db);

	$page = 0;
	if(isset($_GET["min"]) && is_numeric($_GET["min"])){
		$page = $_GET["min"];
	}
	
	$max = 10; // Maximum d'article à afficher par page
	$min = $page * $max;
	
	$nbEntree = $article->selectCount(1)["nombre"]; // Récupère tout les articles visibles
	$form["nbDePage"] = ceil($nbEntree/$max);
	$form["numeroPage"] = $page;

	if($page < 0 || $page >= $form["nbDePage"]+1){
		return header("Location:./");
	}

	$articleList = $article->select($min, $max, 1);

	// Lorsque l'utilisateur est rediriger sur la page d'accueil après s'être deconnecter
	if(isset($_GET["singout"])){
		$form["message"] = "Vous avez bien été déconnecté.";
	}

	echo $twig->render("home.html.twig", array("form" => $form, "article" => $articleList));
}

// Controleur de la page recherche
function searchController($twig, $db){
	if(isset($_POST["btRecherche"])){
		header("Location:?page=search&search=".$_POST["search"]);
		exit;
	}
	$form = array();

	$form["title"] = "Recherche";
	if(!empty($_GET["search"])){
		$search = $_GET["search"];
		$form["title"] = $search;

		$article = new Article($db);

		$page = 0;
		if(isset($_GET["min"]) && is_numeric($_GET["min"])){
			$page = $_GET["min"];
		}

		$max = 5;
		$min = $page * $max;

		$nbEntree = $article->searchCount($search)["nombre"];
		$form["nbDePage"] = ceil($nbEntree / $max);
		$form["numeroPage"] = $page;

		if($page < 0 || $page >= $form["nbDePage"]){
			$min = 0;
		}

		$form["resultat"] = $article->search($search, $min, $max);
		
		$form["search"] = $search;
	}

	$form["nofooter"] = true;
	echo $twig->render("search.html.twig", array("form"=>$form));
}

// Génére le resultat de la recherche en JSON pour la recherche instané
function suggestionController($twig, $db){
	$form = array();
	if(!empty($_GET["search"])){
		$article = new Article($db);
		$form["resultat"] = $article->search($_GET["search"], 0, 5);

		echo json_encode($form["resultat"]);
	}
}

function error404Controller($twig, $db){
	echo $twig->render("404.html.twig", array());
}

function maintenanceController($twig, $db){
	echo $twig->render("maintenance.html.twig", array());
}