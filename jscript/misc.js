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
	log("divName=" + divName);
	if (counter > 1) {
		$("#" + divName).find("[id*=remove_]").each(add_remove_button);
	} else {
		$("#" + divName).find("[id*=remove_]").each(delete_remove_button);
	}
}

function delete_remove_button() {
	log("Delete remove button from: " + $(this).attr("id"));
	$("#" + $(this).attr("id")).html("");
}

function add_remove_button() {
	log("Add remove button to: " + $(this).attr("id"));
	$("#" + $(this).attr("id")).html(
		"<input type=\"button\" value=\"Remove\" onClick=\"removeRate('"
		+ $(this).closest('tr').attr("id") + "', 'tickets');\">");
}

function removeRate(el, parent) {
	if (counter > 1) {
		counter--;
		removeElement(el, parent);
		removeElement("advanced_" + el, parent);
		sync_remove_button(parent);
	} else {
		alert("You have to set at least one rate");
	}
}

function removeElement(el, parent) {
	$("#" + parent).find("#" + el).remove();
}

function eb_sync_amount() {
	var amount = Math.abs($(this).val());
	$(this).val(amount);

	var id = $(this).attr('data-id');
	var max_quantity = $(this).attr('max');
	if (amount > max_quantity) {
		amount = max_quantity;
		$(this).val(max_quantity);
	}
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
	eb_sync_next_button();
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
		var h = eb_hash($('input[name='+txt_field_name+']').val() + hash_salt);
		$('input[name='+hidden_field_name+']').val(""+h);
	} else {
		$('input[name='+hidden_field_name+']').val(null);
	}
}

function eb_hash(obj) {
	return CryptoJS.SHA1(obj);
}

function eb_get_cnonce() {
	var timestamp = new Date().getTime()
	return eb_hash(""+Math.random()+timestamp);
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

function eb_tiny_mce_on() {
	tinymce.init({
        mode : "specific_textareas",
        editor_selector : "apply_tinymce",
	    plugins: [
	        "advlist autolink lists link image charmap print preview anchor",
	        "searchreplace visualblocks code",
	        "insertdatetime media table contextmenu paste"
	    ],
	    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",

	    //statusbar : false
	    //resize: true
	});
	//tinyMCE.init({
	//        // General options
	//        mode : "specific_textareas",
	//        editor_selector : "apply_tinymce",
	//        theme : "advanced",
	//        plugins : "lists,spellchecker,advhr,preview",
    //
	//        // Theme options
	//        theme_advanced_buttons1 : "fontsizeselect,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,undo,redo,|,link,unlink,anchor,image,|,copy,cut,paste,|,code,|,preview,",
	//        theme_advanced_toolbar_location : "top",
	//        theme_advanced_toolbar_align : "left",
	//        theme_advanced_statusbar_location : "bottom",
	//        theme_advanced_resizing : true,
	//        theme_advanced_path : false,
    //
	//        // Skin options
	//        skin : "o2k7",
	//        skin_variant : "silver",
    //
	//        // Example content CSS (should be your site CSS)
	//        //content_css : "css/example.css",
    //
	//        // Drop lists for link/image/media/template dialogs
	//        template_external_list_url : "js/template_list.js",
	//        external_link_list_url : "js/link_list.js",
	//        external_image_list_url : "js/image_list.js",
	//        media_external_list_url : "js/media_list.js",
    //
	//        // Replace values for the template plugin
	//        template_replace_values : {
	//                username : "Some User",
	//                staffid : "991234"
	//        }
	//});
}

function eb_show_html(id) {
	$("#"+id).attr("title", "Details");
	var dialog_w = 500;
	var dialog_h = 300;
	$("#"+id).dialog({
		modal: true,
		width: 1000,
		buttons: {
			Ok: function() {
				$(this).dialog("close");
			}
		}
    });
}

function eb_execute_global_action(type, name, label) {
	$("#dialog_" + name).attr("title", type + " : " + label);
	$("#dialog_" + name).dialog({
		modal: true,
		buttons: {
			Cancel: function() {
				$(this).dialog("close");
			},
			Ok: function() {
				$("form[name=form_execute_global_action_" + name + "]").submit();
				$(this).dialog("close");
			}
		}
    });
}

function eb_execute_grouped_action(type, name, label) {
	var id_array = new Array();
	$("input.record:checked").each(function(){
		id_array.push($(this).attr("name"));
	});
	$("form[name=grouped_action]").attr("action",
		"?action=" + name + "&type=" + type + "&grouped=y");
	var content = "";
	for (var i = 0; i < id_array.length; i++) {
		id = id_array[i];
		content += '<input type="hidden" name="ids[]" value="' + id + '" />';
	}
	$("form[name=grouped_action]").html(content);
	$("form[name=grouped_action]").submit();
	//var id_list = id_array.join("_");
	//window.location = "?action=" + name + "&type=" + type + "&grouped=y&ids=" + id_list;
}

var response = '';
function eb_execute_tasks(event_id) {
	log("eb_execute_tasks");

	$.ajax({
		url:"task.php",
		type:"POST",
		data:{
			event_id: event_id,
		},
		success:function(data) {
			response = data;
		},
		error: function (xhr,status,error) {
           log("Status: " + status);
           log("Error: " + error);
           log("xhr: " + xhr.readyState);
        },
		statusCode: {
			404: function() {
				log("page not found");
			}
		}
	});
	return response;
}

function addresspicker_init() {
	$('.addresspicker').each(function() {
		var str = $(this).attr('data-addresspickeroptions');
		log('str='+str);
		var options = {};
		if (str) {
			options = $.parseJSON(str);
		}
		var addresspicker = $(this).textarea_addresspicker(options);
	});
}