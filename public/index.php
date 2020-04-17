<?php
session_start();

// Initialisation TWIG
require_once("../lib/vendor/autoload.php");
$loader = new \Twig\Loader\FilesystemLoader("../src/view/");
$twig = new \Twig\Environment($loader, []);
$twig->addGlobal("session", $_SESSION);
$twig->addGlobal("get", $_GET);

// MVC
require_once("../src/controller/_controller.php");
require_once("../src/model/_model.php");

// Route
require_once("../config/route.php");

// Connection BD
require_once("../config/parametres.php");
require_once("../config/connect.php");
$db = connect($config);

// Reconnecte l'utilisateur grace aux cookies
if(isset($_COOKIE["id_user"]) && !isset($_SESSION["id"])){
	$utilisateur = new User($db);
	$unUtilisateur = $utilisateur->selectById($_COOKIE["id_user"]);
	if($unUtilisateur != null){
		$_SESSION["id"] = $unUtilisateur["id"];
		$_SESSION["nom"] = $unUtilisateur["nom"];
		$_SESSION["role"] = $unUtilisateur["role"];
		header("Location:./");
	}else{
		setcookie('id_user', "");
	}
}

$content = getPage($db);
$content($twig, $db);