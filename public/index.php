<?php
session_start();

// Initialisation TWIG
require_once("../lib/vendor/autoload.php");
$loader = new \Twig\Loader\FilesystemLoader("../src/view/");
$twig = new \Twig\Environment($loader, []);
$twig->addGlobal("session", $_SESSION);

// MVC
require_once("../src/controller/_controller.php");
require_once("../src/model/_model.php");

// Route
require_once("../config/route.php");

// Connection BD
require_once("../config/parametres.php");
require_once("../config/connect.php");
$db = connect($config);

if(isset($_COOKIE["id_user"]) && !isset($_SESSION["id"])){
	$utilisateur = new User($db);
	$unUtilisateur = $utilisateur->selectById($_COOKIE["id_user"]);
	$_SESSION["id"] = $unUtilisateur["id"];
	$_SESSION["nom"] = $unUtilisateur["nom"];
	$_SESSION["email"] = $unUtilisateur["email"];
	$_SESSION["role"] = $unUtilisateur["role"];
	header("Location:./");
}

$content = getPage($db);
$content($twig, $db);