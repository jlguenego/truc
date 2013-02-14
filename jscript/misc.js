var counter = 1;
var limit = 5;
function addRate(divName){
	if (counter == limit)  {
        alert("You have reached the limit of adding " + counter + " rates");
    } else {
		counter++;
		var newdiv = document.createElement('div');
		var id = new Date().getTime();
		newdiv.setAttribute('id', id);
		newdiv.innerHTML =
				"<table>" +
					"<tr>" +
						"<td>Rate<td>" +
						"<td>" +
							"<table>" +
								"<tr>" + 
									"<td>Label</td>" + 
									"<td><input type=\"text\" name=\"labels[]\"></td>" +
								"</tr>" +
								"<tr>" +
									"<td>Amount</td>" +
									"<td><input type=\"number\" name=\"rates[]\"></td>" +
								"</tr>" +
							"</table>" +
						"</td>" +
						"<td>" + 
							"<input type=\"button\" value=\"Remove\" onClick=\"removeRate('" + id + "', 'rates');\">" + 
						"</td>" +
					"</tr>" +
				"</table>";
		document.getElementById(divName).appendChild(newdiv);
	}
}

function removeRate(el, parent) {
	if (counter > 1) {
		counter--;
		removeElement(el, parent);
	}
}

function removeElement(el, parent) {
	var d = document.getElementById(parent);
	var olddiv = document.getElementById(el);
	d.removeChild(olddiv);
}