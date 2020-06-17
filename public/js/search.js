var searchInput = document.querySelector(".input-search");
var suggestion = document.querySelector(".suggestion");

searchInput.addEventListener("input", function(e){
	var saisie  = e.target.value;
	if(saisie.length > 0){
		suggestion.style.display = "block";
		ajaxGet("?page=suggestion&search="+ saisie, function(r){
			suggestion.innerHTML = "";
			var nbArticle = 0;
			JSON.parse(r).forEach(e => {
				var link = document.createElement("a");
					link.className = "list-group-item";
					link.href = "?page=article&id=" + e.id;
					link.textContent = e.titre;
				suggestion.appendChild(link);

				nbArticle++;
			});
			// Si le nombre d'article est >= à 5 nous indiquons qu'il peut y avoir plus d'articles
			if(nbArticle >= 5){
				var link = document.createElement("li");
				link.className = "list-group-item list-group-item-secondary";
				link.innerHTML = "Pour plus de résultats cliquez sur <b>Rechercher</b>";
				suggestion.appendChild(link);
			}
		});
	}else{
		suggestion.removeAttribute("style");
	}
});

document.querySelector("body").addEventListener("click", function(){
	suggestion.removeAttribute("style");
});