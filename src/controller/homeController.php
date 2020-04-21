<?php

function homeController($twig, $db){
	$article = new Article($db);

	$page = 0;
	if(isset($_GET["min"])){
		$page = $_GET["min"];
	}
	$min = $page * 9;
	$max = 9;

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
		$form["resultat"] = $article->search($search, $search);
	}

	echo $twig->render("search.html.twig", array("form"=>$form));
}

function error404Controller($twig, $db){
	echo $twig->render("404.html.twig", array());
}

function maintenanceController($twig, $db){
	echo "Maintenance";
	//echo $twig->render("maintenance.html.twig", array());
}