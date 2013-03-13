var counter = 0;
var limit = 5;

function setCounter(amount) {
	counter = amount;
}

function addRate(divName, label, amount){
	label = label || "";
	amount = amount || "";
	if (counter == limit)  {
        alert("You have reached the limit of adding " + counter + " rates");
    } else {
		counter++;
		var id = new Date().getTime();
		$("#" + divName).append("<div id=\"" + id + "\"></div>");
		var content =
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
										"<select name=\"tax_rates[]\" \">";
		for (var i = 0; i < taxes.length; i++) {
			content += 				"<option value=\"" + taxes[i][1] + "\">" + taxes[i][0] + "</option>";
		}
		content +=				"</select>" +
									"</td>" +
								"</tr>" +
							"</table>" +
						"</td>" +
						"<td id=\"remove_" + id + "\"></td>" +
					"</tr>" +
				"</table>";
		$("#" + id).html(content);
		sync_remove_button(divName);
	}
}

function sync_remove_button(divName) {
	if (counter > 1) {
		$("#" + divName).children("div[id]").each(add_remove_button);
	} else {
		$("#" + divName).children("div[id]").each(delete_remove_button);
	}
}

function delete_remove_button() {
	$("#remove_" + $(this).attr("id")).html("");
}

function add_remove_button() {
	$("#remove_" + $(this).attr("id")).html("<input type=\"button\" value=\"Remove\" onClick=\"removeRate('" + $(this).attr("id") + "', 'rates');\">");
}

function removeRate(el, parent) {
	if (counter > 1) {
		counter--;
		removeElement(el, parent);
		sync_remove_button(parent);
	} else {
		alert("You have to set at least one rate");
	}
}

function removeElement(el, parent) {
	$("#" + parent).children("#" + el).remove();
}

function eb_sync_amount() {
	var amount = Math.abs($(this).val());
	$(this).val(amount);

	var id = $(this).attr('id');
	var unit_price = $('#unit_price_' + id).html();
	var total_ht = amount * unit_price;
	$('#total_ht_' + id).html(total_ht.toFixed(2));

	var tax_rate = $('#tax_rate_' + id).html();
	var tax_amount = (tax_rate/100) * total_ht;
	$('#tax_amount_' + id).html(tax_amount.toFixed(2));

	$('#ttc_' + id).html((tax_amount + total_ht).toFixed(2));

	var sub_total = 0;
	for (i = 0; i < rate_nbr; i++) {
		var current_ttc = $('#total_ht_' + i).html();
		sub_total = parseFloat(sub_total) + parseFloat(current_ttc);
	}
	$('#sub_total').html(sub_total.toFixed(2));

	update_total(tax_rate);
}

function update_total(tax_rate) {
	for (i = 0; i < taxes.length; i++) {
		var tax = taxes[i][0];
		var id = taxes[i][1];
		if (tax == tax_rate) {
			var sub_total = 0;
			for (i = 0; i < rate_nbr; i++) {
				var tax_rate2 = $('#tax_rate_' + i).html();
				if (tax_rate2 == tax) {
					var current_total = $('#total_ht_' + i).html();
					sub_total = parseFloat(sub_total) + parseFloat(current_total);
				}
			}
			$('#tax_base_' + id).html(sub_total.toFixed(2));
			sub_total *= (tax/100);
			$('#tax_total_' + id).html(sub_total.toFixed(2));
		}
	}

	sub_total = 0;
	for (i = 0; i < tax_nbr; i++) {
		var current_total = $('#tax_total_' + i).html();
		sub_total = parseFloat(sub_total) + parseFloat(current_total);
	}
	$('#tax_total').html(sub_total.toFixed(2));
	var total = parseFloat($('#sub_total').html()) + parseFloat($('#tax_total').html());
	$('#total_due').html('<b>' + total.toFixed(2) + '</b>');
}

function eb_curr(m) {
	return parseFloat(m).toFixed(2);
}