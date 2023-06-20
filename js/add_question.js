let counter = 1;
let case_num;
window.onload = function(){
	document.querySelector("#question_add_btn").addEventListener("click", lisa);
	document.querySelector("#question_remove_btn").addEventListener("click", eemalda);
	console.log("Töötab");

	let x = document.getElementById("x").value;
	let i = 0;
	let interval = setInterval(function() {
	    if (i < x) {
	        document.getElementById("question_add_btn").click();
	        i++;
	    } else {
	        clearInterval(interval);
	    }
	}, 200);
}

function lisa(e) {
	counter += 1;
	case_num = e.target.dataset.case;

	let input1 = document.createElement("input");
	input1.type = "text";
	input1.id = "kysimus"+counter;
	input1.name = "kysimus"+counter;
	input1.placeholder = "küsimus";
	//input1.value = "$question"+counter;

	let br = document.createElement("br");

	let input2 = document.createElement("input");
	input2.type = "text";
	input2.id = "lahendus"+counter;
	input2.name = "lahendus"+counter;
	input2.placeholder = "lahendus (valikuline)";
	//input2.value = "$solution"+counter;

	let parent = document.getElementById("myDiv");
	parent.appendChild(input1);
	parent.appendChild(br);
	parent.appendChild(input2);

	read_question();
}

function read_question() {
	let webRequest = new XMLHttpRequest();
	//oleme valmis eduks ja kui asjad toimivad, siis jälgime, kas õnnestus
	webRequest.onreadystatechange = function(){
		if(this.readyState == 4 && this.status == 200){
			//kõik, mida teha, kui tuli vastus
			received_data = this.responseText.split(";");
			if (this.responseText != "") {
				document.querySelector("#kysimus"+counter).value = received_data[0];
				document.querySelector("#lahendus"+counter).value = received_data[1];
			}
			//console.log(this.responseText);
		}
	};
	//paneme tööle
	//    store_photorating.php?photo=33&rating=4
	webRequest.open("GET", "read_question.php?case=" + case_num + "&jk=" + counter, true);
	webRequest.send();
}

function eemalda() {
	if (counter > 0) {
		counter -= 1
	}

	/*let previous = counter+1
	document.getElementById('kysimus'+previous).value = '';
	document.getElementById('lahendus'+previous).value = '';
	console.log("kadus kysimus/lahendus"+previous);*/

	const list = document.getElementById("myDiv");
	list.removeChild(list.lastElementChild);
	list.removeChild(list.lastElementChild);
	list.removeChild(list.lastElementChild);
}