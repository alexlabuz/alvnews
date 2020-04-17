<?php

function homeController($twig, $db){
	$article = new Article($db);

	$page = 0;
	if(isset($_GET["min"])){
		$page = $_GET["min"];
	}
	$min = $page * 10;
	$max = 10;

	$list = $article->select($min, $max, 1);

	echo $twig->render("home.html.twig", array("list" => $list));
}

function error404Controller($twig, $db){
	echo $twig->render("404.html.twig", array());
}

function maintenanceController($twig, $db){
	echo "Maintenance";
	//echo $twig->render("maintenance.html.twig", array());
}