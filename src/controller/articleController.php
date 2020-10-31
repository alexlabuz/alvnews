<?php
// Permet l'affichage de l'article
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
	$form["errorMessage"] = null;

	$upload = new File("images/article");
	$user = new User($db);
	$unUtilisateur = $user->selectById($_SESSION["id"]);

	$article = new Article($db);
	
	// Vérifie si un article précisé dans l'url
	$unArticle = null;
	if(isset($_GET["id"])){
		$art = $article->selectById($_GET["id"]);

		if($art != null && ($art["idUtilisateur"] == $unUtilisateur["id"] || $unUtilisateur["role"] == 3)){
			$unArticle = $art;
		}else{
			$form["errorMessage"] = "Erreur : l'article n'existe pas ou vous n'avez pas les droits nécessaires";
		}
	}

	// Appui sur le bouton d'envoie d'article
	if(isset($_POST["btEnvoyer"])){
		$error = ($form["errorMessage"] != null);

		$titre = $_POST["titre"];
		$description = $_POST["description"];
		$image = null;
		$contenu = $_POST["contenu"];
		$visible = 0;
		$idTheme = $_POST["theme"];
		$idUser = $unUtilisateur["id"];

		if(isset($_POST["visible"])){
			$visible = 1;
		}

		if($unArticle != null){
			$image = $unArticle["image"];
		}

		// Vérifie si les champ ont était remplie et si il n'y a pas de code javascript sur la page
		if($titre != null && $idTheme != null && !$error && !strrpos(strtolower($contenu), "<script")){

			// Vérifie si un fichier à était envoyé
			if($unArticle == null){
				// Si il s'agit d'un nouvel article on récupère l'état de l'auto increment pour connaître l'id
				$idAutoIncrement = $article->showStatus()["Auto_increment"];
				$file = $upload->save("image", $idAutoIncrement, null, null);
			}else{
				$file = $upload->save("image", $unArticle['id'], null, null);
			}
			if($file["name"] != null){
				$image = $file["name"];
			}
			$error = ($file["errorMessage"] != null);

			// Envoie l'article
			if(!$error){
				// Vérifie si il s'agit d'un nouveau article ou si c'est une mise à jour
				if($unArticle == null){
					// Création d'un nouvel article
					$exec = $article->insert($titre, $description, $image, $contenu, $visible, $idTheme, $idUser);
				}else{
					// Mise à jour de l'article existant
					$exec = $article->update($titre, $description, $image, $contenu, $visible, $idTheme, $unArticle["id"]);
				}
				if(!$exec){$error = true;}
			}

		}else{
			$error = true;
		}
		if($error){
			return header("Location:?page=editor&code=1");
		}

		return header("Location:?page=home"); // Succée de l'envoie
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