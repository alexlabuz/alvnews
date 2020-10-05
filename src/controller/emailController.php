<?php
// Permet d'envoyer un mail d'activation de profil
function envoieMailVerif($email, $db){
	$user = new User($db);
	$unUser = $user->connect($email);

	if($unUser && $unUser["valide"] == 0){
		$serveur = $_SERVER["HTTP_HOST"];
		$page = $_SERVER["SCRIPT_NAME"];
		$idGenere = $unUser["idGenere"];
		$lien = "http://$serveur$page?page=validation&email=$email&idgenere=$idGenere";

		$objet = "Confirmer votre inscription - alLavi news";
		$message = "
		<html>
		<head></head>
		<body>
			<h1>alLavi news</h1>
			<p>Bienvenue sur allavi news, <a href=\"$lien\">cliquez ici</a> pour valider votre profil,
			ou allez sur le lien ci-dessous</p>
			<a href=\"".$lien."\">$lien</a>
			<p>Bonne journée</p>
		</body>
		</html>
		";

		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/html; charset=utf-8";
		mail($email, $objet, $message, implode("\n", $headers));
	}
}

// Permet d'envoyer un mail de changement de mot de passz
function envoieMailMdp($email, $db){
	$user = new User($db);
	$unUser = $user->connect($email);

	if($unUser != null){
		$serveur = $_SERVER["HTTP_HOST"];
		$page = $_SERVER["SCRIPT_NAME"];
		$idGenere = $unUser["idGenere"];
		$lien = "http://$serveur$page?page=updateForgotMdp&email=$email&idgenere=$idGenere";

		$objet = "Changement de mot de passe - alLavi news";
		$message = "
		<html>
		<head></head>
		<body>
			<h1>alLavi news</h1>
			<p>Afin de pouvoir changer de mot passe <a href=\"$lien\">cliquez ici</a>,
			ou allez sur le lien ci-dessous</p>
			<a href=\"".$lien."\">$lien</a>
			<p>Bonne journée</p>
		</body>
		</html>
		";

		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/html; charset=utf-8";
		mail($email, $objet, $message, implode("\n", $headers));
	}
}