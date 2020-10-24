var btUpdateReponse = document.getElementsByClassName("btUpdateReponseJS");
var divReponse = document.getElementsByClassName("divReponseJS");
var editorOpen = false;

for(i = 0; i < btUpdateReponse.length; i++){
	btUpdateReponse[i].addEventListener("click", function(i){
		var div = document.getAttribute("reponse-id", this.getAttribute("reponse-id"));
		openInput(div);
	});
}

function openInput(element){
	var text = element.childNodes[1].textContent;

	var form = document.createElement("form");
	form.action = "";
	form.method = "POST";

	var inputReponse=document.createElement("textarea");
	inputReponse.type="text";
	inputReponse.name="reponse";
	inputReponse.rows="3";
	inputReponse.textContent = text;
	inputReponse.className = "form-control form-control-sm"
	form.appendChild(inputReponse);

	var btSubmit = document.createElement("button");
	btSubmit.type = "submit"
	btSubmit.name = "btEnvoyer"
	btSubmit.textContent = "Envoyer"
	btSubmit.className = "btn btn-outline-primary btn-sm";
	form.appendChild(btSubmit);

	element.innerHTML = "";
	element.appendChild(form);
}

function closeInput(element, text) {
	element.innerHTML = text;
}