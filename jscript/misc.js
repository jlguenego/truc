var counter = 0;

function setCounter(amount) {
	counter = amount;
}

function getCounter() {
	return counter;
}

function log(msg) {
	if (window.console) console.log(msg);
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
	$("#remove_" + $(this).attr("id")).html("<input type=\"button\" value=\"Remove\" onClick=\"removeRate('" + $(this).attr("id") + "', 'tickets');\">");
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
			var total = sub_total;
			sub_total *= (tax/100);
			$('#tax_total_' + id).html(sub_total.toFixed(2));
			total += sub_total;
			$('#tax_total_due_' + id).html(total.toFixed(2));
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

function eb_sync_hash(txt_field_name, hidden_field_name) {
	eb_handle_sync_hash(txt_field_name, hidden_field_name);

	$('input[name='+txt_field_name+']').keyup(function() {
		eb_handle_sync_hash(txt_field_name, hidden_field_name);
	});
}

function eb_handle_sync_hash(txt_field_name, hidden_field_name) {
	if ($('input[name='+txt_field_name+']').val().length > 0) {
		var hash = CryptoJS.SHA1($('input[name='+txt_field_name+']').val() + hash_salt);
		$('input[name='+hidden_field_name+']').val(""+hash);
	} else {
		$('input[name='+hidden_field_name+']').val(null);
	}
}

function eb_unpublish() {
	$("form[name=unpublish]").submit(function() {
		$("#dialog").dialog({
			modal: true,
			buttons: {
				Ok: function() {
					var content = $("#dialog_textarea").val();
					console.log("content="+content);
					$("input[name=reason]").val(content);
					//$(this).dialog("close");
					$(this).dialog("destroy").remove();
					$("form[name=unpublish]").submit();
				}
			}
	    });
		//stop submit
		var content = $("input[name=reason]").val();
		if (!content) {
		    return false;
		}
	});
}