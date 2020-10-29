window.addEventListener("load",function(){
	bsCustomFileInput.init();
});

var h1Title = document.getElementById("article_title");
var inputTitle = document.getElementById("titre");
var inputArticle = document.getElementById("article");

inputTitle.addEventListener("input", function(){
	h1Title.textContent = this.value;
	if(this.value.length <= 0){
		h1Title.textContent = "Nouvel article";
	}
});

inputArticle.addEventListener("input", function () {
	document.getElementById("nbStr").textContent = inputArticle.value.length;

	var tableMots = inputArticle.value.split(" ");
	var nbMots = 0;
	tableMots.forEach(m => {
		if(m.length > 0){
			nbMots++;
		}
	});

	document.getElementById("nbWords").textContent = nbMots;
});

document.getElementById("nbStr").textContent = inputArticle.value.length;

var tableMots = inputArticle.value.split(" ");
var nbMots = 0;
tableMots.forEach(m => {
	if(m.length > 0){
		nbMots++;
	}
});

document.getElementById("nbWords").textContent = nbMots;