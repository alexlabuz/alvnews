<?php


function listeThemeController($twig, $db){
	$form = array();
	$listeTheme = array();
	$listeArticle = array();
	$theme = new Theme($db);

	if(empty($_GET["cat"])){
		$listeTheme = $theme->selectDistinct();
	}else{
		$article = new Article($db);
		$listeArticle = $article->selectByTheme($_GET["cat"]);
		$form["theme"] = $theme->selectById($_GET["cat"]);
		
		if($listeArticle == null){
			$form["errorMessage"] = "Ce thème n'existe pas";
		}
	}

	echo $twig->render("theme.html.twig", array("form" => $form, "themes" => $listeTheme, "articles" => $listeArticle));
}

// Gestion administrative des thémes (affichage en liste, ajout et suppression)
function themeController($twig, $db){
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
	
	// Mise à jour de thème
	if(isset($_POST["btModifier"])){
		$nameTheme = $_POST["nameTheme"];
		$colorTheme = $_POST["colorTheme"];
		$idTheme = $_POST["idTheme"];
		$code = 0;

		if($nameTheme != null && $colorTheme != null){
			$exec = $theme->update($nameTheme, $colorTheme, $idTheme);
			if($exec){
				header("Location:?page=gestionTheme&code=0");
				exit;
			}else{
				$code = 1;
			}
		}
		header("Location:?page=updateTheme&id=".$idTheme."&code=".$code);
		exit;
	}

	// Suppresion de thème(s)
	if(isset($_POST["btEfface"])){
		$coche = $_POST["coche"];
		$code = 0;

		if(!empty($coche)){
			foreach($coche as $id){
				$exec = $theme->delete($id);
				if(!$exec){
					$code = 1;
				}
			}
		}
		header("Location:?page=gestionTheme&code=". $code);
		exit;
	}

	$listType = array();

	// On vérifie si un id de thème est passé en paramètre
	if(isset($_GET["id"])){
		// Si oui on propose de le modifier
		$form["theme"] = $theme->selectById($_GET["id"]);
		// On vérifie si l'id existe dans la db
		if(!$form["theme"]){
			header("Location:?page=gestionTheme");
			exit;
		}
	}else{
		// Affichage des thèmes
		$listType = $theme->select();
		$form["nbDeTheme"] = count($listType);
	}
		
	$message[0] = "Votre demande a été effectuée avec succés";
	$message[1] = "Une erreur s'est produite, veuillez réésayer";
	$message[2] = "Le thème existe déjà";

	if(isset($_GET["code"]) && isset($message[$_GET["code"]])){
		$form["message"] = $message[$_GET["code"]];
	}

	echo $twig->render("gestionTheme.html.twig", array("form" => $form, "listeType" => $listType));
}