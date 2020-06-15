var form = document.querySelector(".jsAnimateLoadForm");
var htmlButton = "<div class=\"spinner-border spinner-border-sm mb-1\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>";

form.addEventListener("submit", function(){
	// Sélection le bouton avec le type submit
	form.querySelector("button[type=\"submit\"]").innerHTML = htmlButton;
});