// Permet l'affichage du formulaire (file) pour l'image
window.addEventListener("load",function(){
	bsCustomFileInput.init();
});

var h1Title = document.getElementById("article_title");
var inputTitle = document.getElementById("titre");
var inputArticle = document.getElementById("article");

// Change le text du titre de la balise H1 a en temps réel
inputTitle.addEventListener("input", function(){
	h1Title.textContent = this.value;
	if(this.value.length <= 0){
		h1Title.textContent = "Nouvel article";
	}
});

inputArticle.addEventListener("input", function () {analyseMot(this.value);});

function analyseMot(text) {
	// Affiche le nombre de caractères
	document.getElementById("nbStr").textContent = text.length;

	// Calcul le nombre de mots
	var tableMots = text.split(" ");
	var nbMots = 0;
	tableMots.forEach(m => {
		if(m.length > 0){
			nbMots++;
		}
	});

	//Affiche le nombre de mots
	document.getElementById("nbWords").textContent = nbMots;
}

analyseMot(inputArticle.value);