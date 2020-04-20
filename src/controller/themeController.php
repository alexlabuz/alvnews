<?php

// Gestion administrative des thémes (affichage en liste, ajout et suppression)
function themeController($twig, $db){
	if(!isset($_SESSION["id"]) || $_SESSION["role"] != 3){
		header("Location:?page=home");
		exit;
	}
	$form = array();
	$theme = new Theme($db);

	// Ajout de thème
	if(isset($_POST["btAjouter"])){
		$nameType = $_POST["nameTheme"];
		$colorType = $_POST["colorTheme"];
		$code = 0;

		if($nameType == null || $colorType == null){
			$code = 1;
		}elseif($theme->selectByName($nameType)){
			$code = 2;
		}else{
			$exec = $theme->insert($nameType, $colorType);
			if(!$exec){
				$code = 1;
			}
		}

		header("Location:?page=gestionTheme&code=". $code);
		exit;
	}

	// Suppresion de thème(s)
	if(isset($_POST["btEfface"])){
		$coche = $_POST["coche"];
		$code = null;

		if(!empty($coche)){
			foreach($coche as $id){
				$exec = $theme->delete($id);
				if($exec){
					$code = 0;
				}else{
					$code = 1;
				}
			}
		}

		header("Location:?page=gestionTheme&code=". $code);
		exit;
	}

	// Affichage des thème
	$listType = $theme->select();

	$message[0] = "Votre demande à était effectuée avec succée";
	$message[1] = "Une erreur s'est produite, veuillez réésayer";
	$message[2] = "Le type existe déjà";

	if(isset($_GET["code"]) && isset($message[$_GET["code"]])){
		$form["message"] = $message[$_GET["code"]];
	}

	echo $twig->render("gestionTheme.html.twig", array("form" => $form, "listeType" => $listType));
}

// Permet de mettre à jour une thème
function updateThemeController($twig, $db){
	if(!isset($_SESSION["id"]) || $_SESSION["role"] != 3){
		header("Location:?page=home");
		exit;
	}
	$form = array();
	$theme = new Theme($db);

	// Mise à jour de thème
	if(isset($_POST["btAjouter"])){
		$nameTheme = $_POST["nameTheme"];
		$colorTheme = $_POST["colorTheme"];
		$idTheme = $_POST["idTheme"];

		if($nameTheme != null && $colorTheme != null){
			$exec = $theme->update($nameTheme, $colorTheme, $idTheme);
			if($exec){
				header("Location:?page=gestionTheme&code=0");
				exit;
			}
		}

		header("Location:?page=updateTheme&id=".$idTheme."&error=true");
		exit;
	}

	$unTheme = $theme->selectById($_GET["id"]);
	// On vérifie si l'id est saisie et si il existe dans la db
	if(!isset($_GET["id"]) || !$unTheme){
		header("Location:?page=gestionTheme");
		exit;
	}

	if(isset($_GET["error"])){
		$form["error"] = $_GET["error"];
	}
	
	echo $twig->render("updateTheme.html.twig", array("form" => $form, "theme" => $unTheme));
}