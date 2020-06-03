var form = document.querySelector(".jsAnimateLoadForm");
var htmlButton = "<a><div class=\"spinner-border spinner-border-sm mb-1\" role=\"status\"><span class=\"sr-only\">Loading...</span></div>"

form.addEventListener("submit", function(){
	document.getElementById("validBt").innerHTML = htmlButton;
});