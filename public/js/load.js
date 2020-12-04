window.addEventListener("load", function(){
	document.querySelector(".navbar-nav").removeChild(document.querySelector(".spinner-border"));
});

// Active tout les tooltips du site
$('[data-toggle="tooltip"]').tooltip('enable');