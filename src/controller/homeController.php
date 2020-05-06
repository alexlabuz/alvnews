<?php

function homeController($twig, $db){
	$article = new Article($db);

	$page = 0;
	if(isset($_GET["min"])){
		$page = $_GET["min"];
	}
	$min = $page * 8;
	$max = 8;

	$articleList = $article->select($min, $max, 1);

	echo $twig->render("home.html.twig", array("article" => $articleList));
}

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
		$form["resultat"] = $article->search($search);
	}

	echo $twig->render("search.html.twig", array("form"=>$form));
}

// Génére le resultat de la recherche en JSON pour la recherche instané
function suggestionController($twig, $db){
	$form = array();
	if(!empty($_GET["search"])){
		$article = new Article($db);
		$form["resultat"] = $article->search($_GET["search"]);

		echo json_encode($form["resultat"]);
	}
}

function error404Controller($twig, $db){
	echo $twig->render("404.html.twig", array());
}

function maintenanceController($twig, $db){
	echo "Maintenance";
	//echo $twig->render("maintenance.html.twig", array());
}