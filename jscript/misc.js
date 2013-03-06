var counter = 0;
var limit = 5;

function setCounter(amount) {
	counter = amount;
}

function addRate(divName, label, amount, taxe_rate){
	label = label || "";
	amount = amount || "";
	taxe_rate = taxe_rate || "";
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
									"<td><input type=\"text\" name=\"labels[]\" value=\"" + label + "\"></td>" +
								"</tr>" +
								"<tr>" +
									"<td>Amount</td>" +
									"<td><input type=\"number\" name=\"rates[]\" value=\"" + amount + "\"></td>" +
								"</tr>" +
								"<tr>" +
									"<td>Taxe</td>" +
									"<td>" +
										"<select name=\"taxe_rates[]\" \">" +
											"<option value=\"19.6\">19.6%</option>" +
											"<option value=\"0\">0%</option>" +
										"</select>" +
									"</td>" +
								"</tr>" +
							"</table>" +
						"</td>" +
						"<td><input type=\"button\" value=\"Remove\" onClick=\"removeRate('" + id + "', 'rates');\"></td>" +
					"</tr>" +
				"</table>";
		document.getElementById(divName).appendChild(newdiv);
	}
}

function removeRate(el, parent) {
	if (counter > 1) {
		counter--;
		removeElement(el, parent);
	} else {
		alert("You have to set at least one rate");
	}
}

function removeElement(el, parent) {
	var d = document.getElementById(parent);
	var olddiv = document.getElementById(el);
	d.removeChild(olddiv);
}