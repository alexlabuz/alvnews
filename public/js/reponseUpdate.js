// Récupère les <div> parents de l'affichage de réponse
var divUpdateReponse = document.getElementsByClassName("clickUpdate");

// Ajoute un écoutteur à tous ces <div>
for(i = 0; i < divUpdateReponse.length; i++){
	divUpdateReponse[i].addEventListener("dblclick", function handler(e){
		e.preventDefault();
		openInput(this);
		// On supprime l'écouteur
		this.removeEventListener("dblclick", handler);
	});
}

// Créer un formulaire avec un <textarea> pour modifier la réponse
function openInput(element){
	// Récupère le texte de la balise enfant <p>
	var text = element.childNodes[0].textContent;
	// Recupère l'id de la réponse depuis l'attribut "id-reponse"
	var id = element.getAttribute("id-reponse");
	// Supprimer la balise <p>
	element.innerHTML = "";

	// Créer le forumlaire
	var form = document.createElement("form");
	form.action = "?page=reponse";
	form.method = "POST";

	var inputReponse = document.createElement("textarea");
	inputReponse.name="reponse";
	inputReponse.rows="3";
	inputReponse.textContent = text;
	inputReponse.required = "required";
	inputReponse.className = "form-control form-control-sm mb-2";
	form.appendChild(inputReponse);

	var btSubmit = document.createElement("button");
	btSubmit.type = "submit";
	btSubmit.name = "btUpdate";
	btSubmit.value = id;
	btSubmit.textContent = "Modifier";
	btSubmit.className = "btn btn-outline-primary btn-sm mr-1";
	form.appendChild(btSubmit);
	
	var btClose = document.createElement("button");
	btClose.textContent = "Fermer";
	btClose.className = "btn btn-outline-secondary btn-sm";
	form.appendChild(btClose);

	element.appendChild(form);

	// Lors de l'appui sur le bouton "Fermé" (btClose)
	btClose.addEventListener("click", function(e){
		e.preventDefault();
		element.innerHTML = "";
		// On recréer le <p>
		var p = document.createElement("p");
		p.textContent = text;
		element.appendChild(p);

		// On recréer l'écouteur
		element.addEventListener("dblclick", function handler(){
			openInput(this);
			this.removeEventListener("dblclick", handler);
		});
	});

}