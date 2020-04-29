//Réception de données
function ajaxGet(url, callback){
	var req = new XMLHttpRequest();

	req.open("GET", url);

	req.addEventListener("load", function () {
		if(req.status >= 200 && req.status < 400){
            // Appelle la fonction callback en lui passant la réponse de la requête
			callback(req.responseText);
		}else{
			// Echec du traitement de la requête
			console.log(req.status + " " + req.statusText + " " + url);
		}
	});

	req.addEventListener("error", function () {
    	// La requête n'a pas réussi à atteindre le serveur
    	console.log("Erreur réseau avec l'URL " + url);
	});

	req.send(null);
}

//Envoie de données
function ajaxPost(url, data, callback, isJson){
	var req = new XMLHttpRequest();

	req.open("POST", url);

	req.addEventListener("load", function () {
		if(req.status >= 200 && req.status < 400){
            // Appelle la fonction callback en lui passant la réponse de la requête
			callback(req.responseText);
			console.log("Succès de la requete");
		}else{
			// Echec du traitement de la requête
			console.log(req.status + " " + req.statusText + " " + url);
		}
	});

	req.addEventListener("error", function () {
    	// La requête n'a pas réussi à atteindre le serveur
    	console.log("Erreur réseau avec l'URL " + url);
	});

    if (isJson) {
        // Définit le contenu de la requête comme étant du JSON
        req.setRequestHeader("Content-Type", "application/json");
        // Transforme la donnée du format JSON vers le format texte avant l'envoi
        data = JSON.stringify(data);
    }
	req.send(data);
}