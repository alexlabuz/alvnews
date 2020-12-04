<h1>alLavi news</h1>

<h2>Présentation</h2>
<p>alLavi news est comme son nom l’indique, un site web d’actualité qui présente des articles (de divers sujets) rédigés par des modérateurs du site, il est possible à n’importe quel utilisateur connecté de commenter les articles ou de les mettre dans une liste afin de leur permettre de les regarder plus tard.</p>

<h2>Les types d’utilisateurs</h2>
<p>La liste ci-dessous liste ce que les utilisateurs peuvent faire sur le site selon leur type de profil :</p>
<nav>
	<ul>
	<h3>Client</h3>
		<li>Créer un profil sur le site avec un pseudo et une photo de profil.</li>
		<li>Commenter des articles grâce au système de commentaire (les commentaires sont publics).</li>
	</ul>
	<ul>
	<h3>Modérateur</h3>
		<li>Tout ce que peut faire le <b>client</b>.</li>
		<li>Peut ajouter un article sur le site grâce à l’éditeur.</li>
		<li>Peut modifier ou supprimer ses propres articles</li>
	</ul>
	<ul>
	<h3>Administrateur</h3>
		<li>Tout ce que peut faire le <b>client</b> et le <b>modérateur</b>.</li>
		<li>Peut modifier ou supprimer les articles des autres personnes.</li>
		<li>Peut ajouter ou supprimer des catégories d’article.</li>
		<li>Tout ce que peut faire le client.</li>
		<li>Peut modifier ou bannir un utilisateur du site.</li>
		<li>Peut faire passer un <b>client</b> en <b>modérateur</b> ou inversement.</li>
	</ul>
</nav>

<h2>Configuration</h2>
<ul>
	<li>Importer le fichier "alvnews.sql" dans votre mysql</li>
	<li>Allez dans "config/" et créer un fichier "parametres.php" et configurer la base de données du site avec ce code :<br>
	<code>&lsaquo;?php<br>
	$config["server"] = "localhost";<br>
	$config["login"] = "your_db_login";<br>
	$config["password"] = "your_db_password";<br>
	$config["db"] = "alvnews";<br>
	?></code></li>
	<li>Donnez les droits d'écriture au répertoire "article" et "profil" dans "public/images/"</li>
</ul>
<p>Vous pouvez utiliser/modifier alLavi news dès à prèsent</p>