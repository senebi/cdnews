const links=document.querySelectorAll(".needsconfirm");
for(let i=0; i<links.length; i++){
	links[i].addEventListener("click", doConfirm, false);
}

function doConfirm(event){
	let operation=this.innerText;
	if(!confirm("Biztos vagy a következő műveletben?\n"+operation))
		event.preventDefault();
}