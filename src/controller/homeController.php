<?php

function homeController($twig, $db){
	$form = array();
	$article = new Article($db);

	$list = $article->select(0, 15);

	echo $twig->render("home.html.twig", array("form" => $form, "list" => $list));
}

function afficheArticleController($twig, $db){
	$form = array();

	$article = new Article($db);
	$unArticle = $article->selectById($_GET["id"]);

	if($unArticle == null){
		$form["errorMessage"] = "Toutes nos excuse mais l'article que vous souhaitez voir n'éxiste pas ou à etait supprimé";
	}

	echo $twig->render("article.html.twig", array("form" => $form, "article" => $unArticle));
}

function error404Controller($twig, $db){
	echo $twig->render("404.html.twig", array());
}

function maintenanceController($twig, $db){
	echo "Maintenance";
	//echo $twig->render("maintenance.html.twig", array());
}