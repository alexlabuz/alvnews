<?php
// Controleur permettant l'inscription du client
function inscriptionController($twig, $db){
	if(isset($_SESSION["id"])){
		header("Location:?page=profil");
		exit;
	}

	$form = array();
	if(isset($_POST["btInscrire"])){
		$email = $_POST["email"];
		$nom = $_POST["nom"];
		$password = $_POST["password"];
		$password2 = $_POST["password2"];
		$code = 5; // code de réussite
		$idGenere = uniqid();

		$utilisateur = new User($db);

		if($utilisateur->connect($email)){
			$code = 3;
		}else if($email == null || $nom == null || $password == null){
			$code = 2;
		}else if($password != $password2){
			$code = 1;
		}else{
			$utilisateur = new User($db);
			$exec = $utilisateur->insert($email, $nom, password_hash($password, PASSWORD_DEFAULT), $idGenere);
			if(!$exec){
				$code = 2;
			}else{
				envoieMailVerif($email, $db);
			}
		}

		// Si l'inscritption à échoué on envoie dans l'url le code d'erreur
		header("Location:?page=inscription&code=".$code);
		exit;
	}

	// Code erreur renvoyé dans le GET
	$message[1] = "Les 2 mots de passe ne correspondent pas";
	$message[2] = "Une erreur s'est produite, veuillez réésayer";
	$message[3] = "Le mail saisi existe déjà";

	if(isset($_GET["code"]) && isset($message[$_GET["code"]])){
		$form["message"] = $message[$_GET["code"]];
	}
	
	$form["nonavbar"] = true;
	$form["nofooter"] = true;
	echo $twig->render("inscription.html.twig", array("form" => $form));
}

// Controleur permettant la connexion du client
function connexionController($twig, $db){
	if(isset($_SESSION["id"])){
		header("Location:?page=profil");
		exit;
	}

	$form = array();
	if(isset($_POST["btConnecter"])){
		$email = $_POST["email"];
		$password = $_POST["password"];
		$resteConnecte = false;
		if(isset($_POST['resteConnecte'])){
			$resteConnecte = true;
		}
		$code = 1;

		$utilisateur = new User($db);
		$unUtilisateur = $utilisateur->connect($email);

		if($unUtilisateur){
			if(password_verify($password, $unUtilisateur['mdp'])){
				// Vérifie si le compte est actif
				if($unUtilisateur["valide"] == 1){
					$_SESSION["id"] = $unUtilisateur["id"];
					$_SESSION["nom"] = $unUtilisateur["nom"];
					$_SESSION["role"] = $unUtilisateur["role"];
					if($resteConnecte){
						setcookie('id_user', $unUtilisateur["id"], time() + 365*24*3600);
					}
					$utilisateur->updateIdGenere("", $unUtilisateur["id"]); // Vide le champ "idGenere"
					header("Location:./");
					exit;
				}else{
					envoieMailVerif($unUtilisateur["email"], $db);
					$code = 2;
				}
			}
		}
 
		// Si la connexion à échoué on envoie dans l'url l'erreur
		header("Location:?page=connexion&code=".$code);
		exit;
	}

	// Code erreur renvoyé dans le GET
	$message[-1] = "Votre mot de passe à bien était changé";
	$message[0] = "Votre inscription est terminée, vous pouvez dès à présent vous connecter";
	$message[1] = "Les identifients sont incorrects";
	$message[2] = "Votre compte n'est pas actif, nous vous avons envoyé un mail d'activation";
	
	if(isset($_GET["code"]) && isset($message[$_GET["code"]])){
		$form["message"] = $message[$_GET["code"]];
	}

	$form["nonavbar"] = true;
	$form["nofooter"] = true;
	echo $twig->render("connexion.html.twig", array("form" => $form));
}

// Permet d'activer un compte utilisateur
function validationController($twig, $db){
	// Vérifie si les paramètre sont passé dans l'url
	if(isset($_GET["email"]) && isset($_GET["idgenere"])){
		$user = new User($db);
		$unUser = $user->connect($_GET["email"]);
		// Vérifie si l'utilisateur existre
		if($unUser){
			// Vérifie si sont profil n'est pas encore actif et si l'idgenre corresponds
			if($unUser["valide"] == 0 && ($_GET["idgenere"] == $unUser["idGenere"])){
				$exec = $user->activeProfil($unUser["id"]);
				// Si succées de requête
				if($exec){
					$user->updateIdGenere("", $unUser["id"]);
					header("Location:?page=connexion&code=0");
					exit;
				}
				
			}
		}

	}
	header("Location:./");
	exit;
}

// Déconnecte l'utilisateur
function deconnexionController($twig, $db){
	session_unset();
 	session_destroy();
 	setcookie('id_user', "");
 	header("Location:index.php?singout=1");
}

// Affiche le profil de l'utilisateur
function profilController($twig, $db){
	$form = array();

	$utilisateur = new User($db);
	$donnees = $utilisateur->selectById($_SESSION["id"]);

	// Tableau qui contiens les nom textuel des roles dans l'ordre
	$form["textRole"] = ["Utilisateur", "Modérateur", "Administrateur"];

	$article = new Article($db);
	$form["listArticle"] = $article->selectByUser($donnees["id"]);
	$form["nbArticle"] = count($form["listArticle"]);

	$enregistre = new Enregistre($db);
	$form["listEnregistre"] = $enregistre->selectByUser($donnees["id"]);
	$form["nbEnregistre"] = count($form["listEnregistre"]);

	$sujet = new ForumSujet($db);
	$form["listSujet"] = $sujet->selectByUser($donnees["id"]);
	$form["nbSujet"] = count($form["listSujet"]);

	echo $twig->render("profil.html.twig", array("form" => $form, "user" => $donnees));
}

// Met à jour le profil de l'utilisateur
function updateUserController($twig, $db){
	$form = array();

	$upload = new File("images/profil/");

	$utilisateur = new User($db);
	$donnees = $utilisateur->selectById($_SESSION["id"]);

	if(isset($_POST['btUpdate'])){
		$email = $_POST["email"];	
		$nom = $_POST["nom"];
		$image = $donnees["image"];

		$code = 0; // Code de réussite

		if(($utilisateur->connect($email)) && ($email != $donnees["email"])){
			// Si le mot de passe existe déjà et si l'email n'est pas le même
			$code = 2;
		}else if($nom == null || $email == null){
			// Si des champs ne sont pas remplie
			$code = 3;
		}else{
			$file = $upload->save("image", $donnees["id"], null, null);
			if($file["name"] != null){
				$image = $file["name"];
			}elseif($file["errorMessage"] != null){
				$code = 5;
			}

			if($code == 0){
				// Met à jour l'utilisateur
				$exec = $utilisateur->update($email, $nom, $image, $donnees["role"], $_SESSION['id']);
				if(!$exec){
					$code = 3;
				}else{
					// Réussite de la demande
					$_SESSION["nom"] = $nom;
					$_SESSION["email"] = $email;
				}
			}
		}

		if(isset($code)){
			return header("Location:?page=updateUser&code=".$code);
		}else{
			return header("Location:?page=updateUser");
		}
	}

	if(isset($_POST["btSupprimeImage"])){
		$code = 1;
		$exec = $utilisateur->update($donnees["email"], $donnees["nom"], null,$donnees["role"], $_SESSION["id"]);
		if($exec){
			if($upload->remove($donnees["image"])){
				$code = 0;
			}
		}
		
		header("Location:?page=updateUser&code=".$code);
		exit;
	}

	// Code erreur renvoyé dans le GET
	$message[0] = "Les modifications ont bien étais pris en compte";
	$message[2] = "Le mail saisi existe déjà";
	$message[3] = "Une erreur s'est produite, veuillez réésayer";
	$message[4] = "Le mot de passe est incorrect, veuillez réessayer";
	$message[5] = "Une erreur s'est produite au niveau de l'image";

	if(isset($_GET["code"]) && isset($message[$_GET["code"]])){
		$form["message"] = $message[$_GET["code"]];
	}

	echo $twig->render("updateUser.html.twig", array("form" => $form, "user" => $donnees));
}

// Supprime le profil de l'utilisateur
function deleteUserController($twig, $db){
	if(isset($_POST["btSupprimer"])){
		$utilisateur = new User($db); // Récupère l'utilisateur pour la véfication du mdp
		$unUtilisateur = $utilisateur->selectById($_SESSION["id"]);

		if(password_verify($_POST["password"], $unUtilisateur['mdp'])){
				$exec = $utilisateur->delete($_SESSION["id"]);
				if(!$exec){
					$code = 3;
				}else{
					session_unset();
					session_destroy();
					setcookie('id_user', "", time() + 365*24*3600);
					/* Supprimer l'ancienne photo de profil */
					if(file_exists("images/profil/".$unUtilisateur["image"])){
						unlink("images/profil/".$unUtilisateur["image"]); 
					}
				}
		}else{
			$code = 4;
		}
		// Si l'inscritption à échoué on envoie dans l'url le code d'erreur
		header("Location:?page=updateUser&code=".$code);
		exit;
	}
}

// Met à jour le mot de passe de l'utilisateur
function updatePasswordController($twig, $db){
	if(!isset($_SESSION["id"])){
		header("Location:?page=connexion");
		exit;
	}
	$form = array();
	$utilisateur = new User($db);
	$unUtilisateur = $utilisateur->selectById($_SESSION["id"]);

	if(isset($_POST['btValider'])){
		$oldPassword = $_POST["oldPassword"];
		$password = $_POST["password"];
		$password2 = $_POST["password2"];
		$code = null;

		if(!password_verify($oldPassword, $unUtilisateur["mdp"])){
			$code = 3;
		}else if($password != $password2){
			$code = 2;
		}else{
			$exec = $utilisateur->updateMdp(password_hash($password, PASSWORD_DEFAULT), $_SESSION["id"]);
			if(!$exec){
				$code = 1;
			}else{
				header("Location:?page=profil&code=0");
				exit;
			}
		}

		header("Location:?page=updatePassword&code=". $code);
		exit;
	}

	$message[1] = "Echec lors de la demande";
	$message[2] = "Les 2 mots de passe ne correspondent pas";
	$message[3] = "Le mot de passe actuel est incorect";

	if(isset($_GET["code"]) && isset($message[$_GET["code"]])){
		$form["message"] = $message[$_GET["code"]];
	}

	echo $twig->render("updatePassword.html.twig", array("form" => $form));
}

// Permet la gestion administrative des utilisateurs
function gestionUserController($twig, $db){
	$form = array();
	if(!isset($_SESSION["id"]) || $_SESSION["role"] != 3){
		header("Location:?page=home");
		exit;
	}
	$user = new User($db);

	if(isset($_POST["btModifier"])){
		$idUser = $_POST["idUser"];
		$role = $_POST["role"];
		$nom = $_POST["nom"];
		$error = 1;

		$unUser = $user->selectById($idUser);

		if($nom != null && $role != null){
			$exec = $user->update($unUser["email"], $nom, $unUser["image"], $role, $idUser);
			if($exec){
				$error = 0;
			}
		}

		header("Location:?page=gestionUser&id=".$idUser."&error=".$error);
		exit;
	}

	if(isset($_POST["btSupprimer"])){
		$error = 1;
		$exec = $user->delete($_POST["idUser"]);
		if($exec){
			$error = 0;
		}

		header("Location:?page=gestionUser&error=".$error);
		exit;
	}

	if(isset($_GET["id"])){
		// Si un id d'utilisateur est passé dans l'url on affiche les info de celui-ci
		$form["user"] = $user->selectById($_GET["id"]);
		if($form["user"] == null || $form["user"]["role"] == 3){
			header("Location:?page=gestionUser");
			exit;
		}
	}else{
		// Si aucun utilisateur n'est passé dans l'url on affiche la liste des utilisateur
		$page = 0;
		if(isset($_GET["min"])){
			$page = $_GET["min"];
		}
		$max = 20;
		$min = $page * $max;

		$nbEntree = $user->selectCount();
		$form["users"] = $user->select($min, $max);

		$form["nbDePage"] = ceil($nbEntree["nombre"] / $max);
		$form["numeroPage"] = $page;
	}

	if(isset($_GET["error"])){
		$form["error"] = $_GET["error"];
	}

	// Tableau qui contiens les nom textuel des roles dans l'ordre
	$form["textRole"] = ["Client", "Modérateur", "Administrateur"];

	echo $twig->render("gestionUser.html.twig", array("form" => $form));
}

// Permet la saisie et l'envoie d'un mail lors de l'oublie d'un mot de passe
function forgotPasswordController($twig, $db){
	$form = array();

	if(isset($_POST["btValider"])){
		$email = $_POST["email"];
		$code = 0;
		$idGenere = uniqid();

		$user = new User($db);
		$unUser = $user->connect($email);

		if($unUser == null){
			$code = 1;
		}else{
			$user->updateIdGenere($idGenere, $unUser["id"]);
			envoieMailMdp($unUser["email"], $db);
		}

		header("location: ?page=forgotPassword&code=".$code);
		exit;
	}

	$message[0] = "Un mail vous a était envoyé afin de changer votre mot de passe";
	$message[1] = "Le mail ne correspond à aucun compte";

	if(isset($_GET["code"]) && isset($message[$_GET["code"]])){
		$form["message"] = $message[$_GET["code"]];
	}

	$form["nonavbar"] = true;
	$form["nofooter"] = true;
	echo $twig->render("forgotPassword.html.twig", array("form" => $form));
}

// Permet de changer le mot de passe en cas d'oublie
function updateForgotMdpController($twig, $db){
	if(isset($_GET["email"]) && isset($_GET["idgenere"])){
		$user = new User($db);
		$unUser = $user->connect($_GET["email"]);

		if($unUser != null && ($unUser["idGenere"] == $_GET["idgenere"])){
			$form = array();

			if(isset($_POST["btUpdate"])){
				$password = $_POST["password"];
				$password2 = $_POST["password2"];
				$code = 1;

				if($password == $password2){
					$exec = $user->updateMdp(password_hash($password, PASSWORD_DEFAULT), $unUser["id"]);
					if($exec){
						$user->updateIdGenere("", $unUser["id"]);
						header("Location:?page=connexion&code=-1");
						exit;
					}
				}

				header("location:?page=updateForgotMdp&email=".$_GET["email"]."&idgenere=".$_GET["idgenere"]."&code=".$code);
				exit;
			}
			
			if(isset($_GET["code"])){
				$form["message"] = "Les 2 mots de passes ne corresponde pas, veuillez réesayer";
			}

			echo $twig->render("updateForgotMdp.html.twig", array("form" => $form, "user" => $unUser));
		}else{
			header("Location:./");
			exit;
		}
	}else{
		header("Location:./");
		exit;
	}
}