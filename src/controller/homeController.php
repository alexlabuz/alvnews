<?php

function homeController($twig){
	echo $twig->render("home.html.twig", array());
}

function error404Controller($twig){
	echo $twig->render("404.html.twig", array());
}

function maintenanceController($twig){
	echo "Maintenance";
	//echo $twig->render("maintenance.html.twig", array());
}