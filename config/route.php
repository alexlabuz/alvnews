<?php

function getPage($db){
	$listPage = array();

	$listPage['home'] = "homeController";
	$listPage['inscription'] = "inscriptionController";
	$listPage['connexion'] = "connexionController";
	$listPage['deconnexion'] = "deconnexionController";
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