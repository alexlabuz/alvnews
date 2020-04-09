<?php
// Initialisation TWIG
require_once("../lib/vendor/autoload.php");
$loader = new \Twig\Loader\FilesystemLoader("../src/view/");
$twig = new \Twig\Environment($loader, []);

// MVC
require_once("../src/controller/_controller.php");
require_once("../src/model/_model.php");

// Route
require_once("../config/route.php");

// Connection bd
require_once("../config/parametres.php");
require_once("../config/connect.php");
$db = connect($config);

$content = getPage($db);
$content($twig, $db);