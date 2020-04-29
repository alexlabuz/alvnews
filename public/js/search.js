var searchInput = document.querySelector(".input-search");
var suggestion = document.querySelector(".suggestion");

searchInput.addEventListener("input", function(e){
	var saisie  = e.target.value;
	if(saisie.length > 0){
		suggestion.style.display = "block";
		ajaxGet("?page=suggestion&search="+ saisie, function(r){
				suggestion.innerHTML = "";
				var nb = 0;
				JSON.parse(r).forEach(e => {
					if(nb < 5){
					var link = document.createElement("a");
					link.href = "?page=article&id=" + e.id;

					var li = document.createElement("li");
					li.className = "list-group-item";
					li.textContent = e.titre;
					link.appendChild(li);

					suggestion.appendChild(link)
					nb++;
					}
				});
		});
	}else{
		suggestion.removeAttribute("style");
	}

});

document.querySelector("body").addEventListener("click", function(){
	suggestion.removeAttribute("style");
});