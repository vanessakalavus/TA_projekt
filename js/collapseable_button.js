let coll = document.getElementsByClassName("collapsible");
let i;
let click = 1;

window.onload = function() {
	document.querySelector("#save_anwser_btn").addEventListener("click", save);
	for (i = 0; i < coll.length; i++) {
		coll[i].addEventListener("click", toggleCollapsible);
	}
	const textareas = document.querySelectorAll('.question_field');
  textareas.forEach(textarea => {
    textarea.addEventListener('input', () => {
      textarea.rows = textarea.value.split('\n').length;
    });
  });
}

function toggleCollapsible() {
	this.classList.toggle("active");
	let poll = this.nextElementSibling;
	if (poll.style.display === "block") {
		poll.style.display = "none";
	} else {
		poll.style.display = "block";
	}
}

function save() {
	if (click == 1) {
		let materjal = '<?php echo $materjal ;?>'
		document.getElementById ('result').style.display = "block";
		click = 2;
	} else {
		document.getElementById("result").innerHTML = "<p>Täname, et sooritasite enesetesti! Soovi korral võite nüüd oma vastused edaspidiseks alla laadida.</p><button type='button' id='download_btn' name='download_btn'>Lae vastused alla</button>";
		let element = document.getElementById("save_anwser_btn");
		element.remove();
		document.querySelector('#download_btn').addEventListener("click", download);
		var elements = document.querySelectorAll('.question_field');
		for (var i = 0; i < elements.length; i++) {
			elements[i].disabled = true;
		}	
	}
}

function download() {
	window.print();
}