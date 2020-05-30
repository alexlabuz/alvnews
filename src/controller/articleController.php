<?php
// Permet d'affichage de l'article
function afficheArticleController($twig, $db){
	if(!isset($_GET["id"])){
		header("Location:./");
		exit;
	}
	$form = array();
	$form["url"] = url();

	$article = new Article($db);
	$commentaire = new Comment($db);
	$enregistre = new Enregistre($db);
	$unArticle = $article->selectById($_GET["id"]);

	if($unArticle == null){
		$form["errorMessage"] = "Toutes nos excuses mais l'article que vous souhaitez voir n'éxiste pas ou à était supprimé";
		$unArticle["titre"] = "Erreur"; // Pour l'affichage dans la <title>
	}

	$listCommentaire = $commentaire->selectByArticle($_GET["id"]);
	$form["nbCommentaires"] = count($listCommentaire);

	if(isset($_SESSION["id"]) && $enregistre->selectByUserArticle($_SESSION["id"], $_GET["id"]) != null){	
		$form["enrgistre"] = true;
	}

	echo $twig->render("article.html.twig", array("form" => $form, "article" => $unArticle, "commentaires" => $listCommentaire));
}

// Permet d'ajouter ou modifier un article
function editorController($twig, $db){
	$form = array();
	$user = new User($db);
	$unUtilisateur = $user->selectById($_SESSION["id"]);
	$article = new Article($db);

	// Appui sur le bouton d'envoie d'article
	if(isset($_POST["btEnvoyer"])){
		$inputTitre = $_POST["titre"];
		$inputDescription = $_POST["description"];
		$image = null;
		$inputArticle = $_POST["article"];
		$inputTheme = $_POST["theme"];
		$inputVisible = 0;
		if(isset($_POST["visible"])){
			$inputVisible = true;
		}
		$idUser = $unUtilisateur["id"];
		$error = false;

		$upload = new Upload(["jpg","jpeg", "JPG", "png", "PNG"], "images/article", 2000000);
		$fichier = $upload->enregistrer("image");
		
		if($inputTitre != null && $inputTheme != null){

			// Vérifie si il s'agit d'un nouveau article ou si c'est une mise à jour
			if(!isset($_POST["idArticleUpdate"])){
				// Envoie de l'image
				if($fichier["nom"] != null){
					$image = $fichier["nom"];
				}
				// Création d'un nouvel article
				$exec = $article->insert($inputTitre, $inputDescription, $image, $inputArticle, $inputVisible, $inputTheme, $idUser);
			}else{
				// Envoie de l'image
				$donnees = $article->selectById($_POST["idArticleUpdate"]);
				if($fichier["nom"] != null){
					/* Supprimer l'ancienne photo de profil */
					if(file_exists("images/article/".$donnees["image"])){
						unlink("images/article/".$donnees["image"]); 
					}
					$image = $fichier["nom"];
				}else{
					$image = $donnees["image"];
				}
				// Mise à jour de l'article existant
				$exec = $article->update($inputTitre, $inputDescription, $image, $inputArticle, $inputVisible, $inputTheme, $_POST["idArticleUpdate"]);
			}
			if(!$exec){$error = true;}

			if($fichier["message"] != null){$error = true;} // Si il y a une erreur dans l'envoie de fichier
		}else{$error = true;}
		
		if($error){
			if(!isset($_POST["idArticleUpdate"])){
				header("Location:?page=editor&code=1");exit;
			}else{
				header("Location:?page=editor&id=".$_POST["idArticleUpdate"]."&code=1");exit;
			}
		}

		header("Location:?page=home"); // Succée de l'envoie
		exit;
	}

	$unArticle = null;
	if(isset($_GET["id"])){
		$unArticle = $article->selectById($_GET["id"]);
		if($unArticle == null){
			$form["errorMessage"] = "L'article n'existe pas";
		}elseif($unArticle["idUtilisateur"] != $unUtilisateur["id"] && $unUtilisateur["role"] != 3){
			$form["errorMessage"] = "Mais cet article ne vous appartient pas dis-donc !";
		}
	}

	if(isset($_GET["code"]) && $_GET["code"] == 1){
		$form["errorMessage"] = "Erreur dans l'envoie de l'article";
	}
	// Affichage du menu déroulant des thèmes
	$theme = new Theme($db);
	$form["theme"] = $theme->select();

	$form["nofooter"] = true;
	echo $twig->render("editor.html.twig", array("form" => $form, "article" => $unArticle));
}

// Permet de supprimer un article
function deleteArticleController($twig, $db){
	$cocher = $_POST["cocher"];
	if(!empty($cocher)){
		$article = new Article($db);
		$code = 0;

		foreach ($cocher as $id){
			$unArticle = $article->selectById($id);
			// Nous véifions si l'article appartiens bien à l'utilisateur ou si il est admin
			if($unArticle["idUtilisateur"] == $_SESSION["id"] || $_SESSION["role"] == 3){
				$exec = $article->delete($id);
				if(!$exec){
					$code = 1;
				}
				/* Supprimer l'ancienne photo de profil */
				if(file_exists("images/article/".$unArticle["image"])){
					unlink("images/article/".$unArticle["image"]); 
				}
			}else{$code = 1;}
		}
	}else{$code = null;}

	// Redirige vers la page précedente
	header("Location: $_SERVER[HTTP_REFERER]&code=".$code);
	exit;
}

// Permet la gestion administrative des articles
function gestionArticleController($twig, $db){
	$form = array();
	$article = new Article($db);

	$page = 0;
	if(isset($_GET["min"])){
		$page = $_GET["min"];
	}
	
	$max = 10; // Maximum d'article à afficher par page
	$min = $page * $max;

	$nbEntree = $article->selectCount(0)["nombre"]; // Récupère tout les articles de la table
	$articleList = $article->select($min, $max, 0);

	$form["nbDePage"] = ceil($nbEntree/$max);
	$form["numeroPage"] = $page;

	echo $twig->render("gestionArticle.html.twig", array("form" => $form, "articles" => $articleList));
}

// Retourne l'url actuellement ouverte
function url(){
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
	$url = "https"; 
	else
	$url = "http"; 
	
	// Ajoutez // à l'URL.
	$url .= "://"; 
	
	// Ajoutez l'hôte (nom de domaine, ip) à l'URL.
	$url .= $_SERVER['HTTP_HOST']; 
	
	// Ajouter l'emplacement de la ressource demandée à l'URL
	$url .= $_SERVER['REQUEST_URI']; 
		
	// Afficher l'URL
	return $url; 
}