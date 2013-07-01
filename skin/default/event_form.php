<?php
	global $g_tax_rates;

	$scenario = $g_display["scenario"];
	$event = $g_display["event"];
	$tickets = $g_display["tickets"];
	$user = $g_display["user"];

	$f = new Form();
	$button_text = "";
	$f->cancel = true;
	if ($scenario == "create") {
		$f->cancel_url = ""; // Main menu
		$button_text = _t("Create");
		$f->title = _t("Event creation");
	} else {
		$f->cancel_url = "?action=retrieve&amp;type=event&amp;id=".$event->id;
		$button_text = _t("Update");
		$f->title = _t("Event edition");
	}
	$f->action = $g_display["form_action"];
	$f->method = "POST";
	$f->add_raw_html(<<<EOF
	<h1>{{Describe your event}}</h1>
	<div class="form_section">
EOF
);
	$item = $f->add_text(_t("[event]Title"), "title", default_value("title", $event->title),
		_t("Conference, or meeting name."));
	$item->other_attr = ' size="60" maxlength="255"';
	$item = $f->add_text(_t("Organizer name"), "organizer_name", default_value("organizer_name", $event->organizer_name),
		_t("The entity that is responsible for organizing the event."));
	$item->other_attr = ' size="60" maxlength="255"';
	$item = $f->add_text(_t("Organizer phone (optional but recommended)"), "phone",
		default_value("phone", $event->phone, $user->phone),
		_t("Contact phone not published. Used ONLY for support purpose."));
	$item->is_optional = true;
	if (!$event->has_accountancy_activity()) {
		$item = $f->add_number(_t("Required funding (Euro)"), "funding_needed",
			default_value("funding_needed", $event->funding_needed),
			_t("Minimum amount of fund needed to organize the event. Please indicate 0 if no requirement."));
		$item->other_attr = ' step="0.01" min="0"';
	} else {
		$f->add_hidden("funding_needed", $event->funding_needed);
	}
	$item = $f->add_text(_t("Event date"), "happening_t",
		default_value("happening_t", $event->happening_t),
		_t("Date at which starts the event (Format: YYYY-MM-DD)."));
	$item->other_attr = 'autocomplete="off"';
	if (!$event->is_confirmed()) {
		$f->add_checkbox(_t("This event is confirmed."), "is_confirmed",
			"", _t("help_checkbox_confirmation"));
	}
	$item = $f->add_text(_t("Confirmation date"), "confirmation_t",
		default_value("confirmation_t", $event->confirmation_t),
		_t("Maximum date at which the event will be confirmed or cancelled (Format: YYYY-MM-DD)."));
	$item->other_attr = 'autocomplete="off"';

	$location = Address::get_from_id($event->billing_address_id);
	$item = $f->add_textarea(_t("Event place"), "location_address",
		default_value("location_address", $location->address),
		_t("Name of the place where will occur the event. Please indicate an accurate address (street, street no, city, zip, state, country)"));
	$item->other_attr = 'class="addresspicker"';

	$billing_address = Address::get_from_id($event->location_address_id);
	$item = $f->add_textarea(_t("Billing address"), "billing_address",
		default_value("billing_address", $billing_address->address),
		_t("Address where the bill will be sent."));
	$item->other_attr = 'class="addresspicker"';
	$item = $f->add_text(_t("Web site (optional)"), "link", default_value("link", $event->link),
		_t("Official event web site (if any)."));
	$item->other_attr = 'size="60" maxlength="255"';
	$item->is_optional = true;
	$item = $f->add_textarea(_t("Short description"), "short_description",
		default_value("short_description", $event->short_description),
		_t("Enter a short description of the event. (HTML editor)"));
	$item->other_attr = 'class="apply_tinymce"';
	$item = $f->add_textarea(_t("Long description"), "long_description",
		default_value("long_description", $event->long_description),
		_t("Enter a long description of the event. (HTML editor)"));
	$item->other_attr = 'class="apply_tinymce" width="200px"';
	$f->add_hidden("id", $event->id);
	$f->add_hidden("event_type", $event->type);
	$f->add_raw_html(<<<EOF
	</div>
	<h1>{{Create tickets and define their price}}</h1>
	<div class="form_section">
EOF
);
	if (!$event->has_accountancy_activity()) {
		$checked = "";
		if ($event->type == EVENT_TYPE_NOMINATIVE) {
			$checked = " checked";
		}
		$f->add_checkbox(_t("I want to know the name of my attendees"), "event_type_checkbox", $checked, "");
	}
	$f->add_raw_html(<<<EOF
<table id="tickets" class="evt_rate">
</table>
<a href="JavaScript:addRate('tickets');">{{Add another ticket rate}}</a><br/><br/>
EOF
);
	if ($scenario == "create") {
		$f->add_checkbox(_t("I have read and agree to the <a href=\"info/terms\">Terms and Conditions</a>"), "confirm",
			"", _t("You have to check this to continue."));
	}
	$f->add_raw_html(<<<EOF
	</div>
EOF
);
	$f->add_submit($button_text);
	$_SESSION["form"] = $f;
	//echo $f->html();
?>

<form class="form" action="<?php echo $g_display["form_action"]; ?>" method="POST">
	<div class="evt_title"><p><?php echo $f->title; ?></p></div>
	<h1>{{Define your event}}</h1>
	<div class="form_section">
		<table width="700" style="margin: 0px auto;">
			<tr>
				<td><?php echo $f->get_element('title'); ?></td>
				<td width="700">&nbsp;</td>
				<td><?php echo $f->get_element('organizer_name'); ?></td>
			</tr>
			<tr>
				<td><?php echo $f->get_element('happening_t'); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo $f->get_element('location_address'); ?></td>
				<td>&nbsp;</td>
				<td>
					<?php echo $f->get_element('billing_address'); ?>
<?php
				if (!$event->is_confirmed()) {
?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $f->get_element('is_confirmed'); ?>
				</td>
				<td>&nbsp;</td>
				<td><?php echo $f->get_element('confirmation_t'); ?></td>
			</tr>
<?php
				} else {
?>
					<input type="hidden" name="confirmation_t" value="<?php echo $event->confirmation_t; ?>" />
					<input type="hidden" name="is_confirmed" value="" />
				</td>
			</tr>
<?php
				}
?>
			<tr>
				<td><?php echo $f->get_element('funding_needed'); ?></td>
				<td>&nbsp;</td>
				<td><?php echo $f->get_element('phone'); ?></td>
			</tr>
			<tr>
				<td><?php echo $f->get_element('link'); ?></td>
			</tr>
		</table>
	</div>
	<h1>{{Detail your event}}</h1>
	<div class="form_section">
	<?php
		echo $f->get_element('short_description');
		echo $f->get_element('long_description');
		echo $f->get_element('id');
		echo $f->get_element('event_type');
	?>
	</div>
	<h1>{{Create tickets and define their price}}</h1>
	<div class="form_section">
		<table id="tickets" class="evt_rate">
		</table>
		<a href="JavaScript:addRate('tickets');">{{Add another ticket rate}}</a><br/><br/>
	</div>
	<?php
		if ($scenario == "create") {
			echo $f->get_element('confirm');
		}
	?>
	<?php echo $f->get_element('form_submit_button');?>
</form>

<script>
	function manage_submit() {
		var test = true;
		log("manage submit");
		if ($("input[name=confirm]").length > 0) {
			log("confirm="+$("input[name=confirm]").attr("checked"));
			test &= $("input[name=confirm]:checked").length > 0;
		}
		test &= $("input[name=title]").val().length > 0;
		test &= $("input[name=organizer_name]").val().length > 0;
		test &= $("input[name=happening_t]").val().length > 0;
		test &= $("input[name=confirmation_t]").val().length > 0
				|| $("input[name=is_confirmed]:checked").length > 0;
		test &= $("textarea[name=location_address]").val().length > 0;
		test &= $("textarea[name=billing_address]").val().length > 0;

		if (test) {
			$("input[type=submit]").removeAttr("disabled");
		} else {
			$("input[type=submit]").attr("disabled", true);
		}
	}

	function update_form() {
		log("update_form");
		log("date=" + $("#happening_t").val());
		log("is_confirmed="+$("input[name=is_confirmed]:checked").length);

		if ($("#happening_t").val() == "" || $("input[name=is_confirmed]:checked").length > 0) {
			$("#confirmation_t").val("");
			$( "#confirmation_t" ).attr("disabled", "");
		} else {
			$( "#confirmation_t" ).removeAttr("disabled");
		}
		$( "#confirmation_t" ).datepicker('option', 'maxDate', $("#happening_t").val());

		$( "#happening_t" ).datepicker({ minDate: "+0d", dateFormat: "yy-mm-dd"});
		$( "#confirmation_t" ).datepicker({ minDate: "+0d", dateFormat: "yy-mm-dd"});
	}

	$("form").change(function() {
		update_form();
		manage_submit();
	});
	$(document).ready(function() {
		update_form();
		manage_submit();
		addresspicker_init();
	});
	$("input").keyup(manage_submit);
	$("input").change(function() {
		update_form();
		manage_submit();
	});



	// Combobox for the tax rates.
	taxes = new Array(
	<?php
		$is_first = TRUE;
		foreach ($g_tax_rates as $name => $rate) {
			if ($is_first) {
				$is_first = FALSE;
			} else {
				echo ',';
			}
			echo "new Array('"._t($name)."', '${rate}')";
		}
	?>
	);
<?php
	$i = 0;
	debug("tickets=".sprint_r($tickets));
	if ($tickets != NULL) {
		foreach ($tickets as $ticket) {
			$label = $ticket->name;
			$amount = $ticket->amount;
			$tax_rate = $ticket->tax_rate;
			$quantity = $ticket->max_quantity;
			$description = $ticket->description;
			echo "addRate('tickets', '$label', '$quantity', '$amount', $tax_rate, '$description');";
			$i++;
		}
	}
?>
	setCounter(<?php echo $i; ?>);
	if (getCounter() < 1) {
		log("No rate.");
		addRate('tickets');
	}
	$("[name=title]").focus();

	eb_tiny_mce_on();

	$('input[name=event_type_checkbox]').change(function() {
		log("checkbox changed");
		if ($("input[name=event_type_checkbox]:checked").length > 0) {
			log("checkbox is checked");
			$("input[name=event_type]").attr("value", <?php echo EVENT_TYPE_NOMINATIVE; ?>);
		} else {
			log("checkbox is not checked");
			$("input[name=event_type]").attr("value", <?php echo EVENT_TYPE_ANONYMOUS; ?>);
		}
	});

	function addRate(divName, label, quantity, amount, selected_tax, description){
		label = label || "";
		amount = amount || "";
		quantity = quantity || "";
		description = description || "";
		log(selected_tax);
		if (selected_tax == undefined) {
			selected_tax = taxes[0][1];
		}
		counter++;
		var id = new Date().getTime();
		$("#" + divName).append("<tr id=\"" + id + "\"></tr>");
		var content =
					"<td>" +
						"<input type=\"text\" name=\"ticket_name_a[]\" value=\"" + label + "\" placeholder=\"{{Ticket name}}\"/>" +
					"</td>" +
					"<td>" +
						"<input class=\"evt_rate_qty\" type=\"number\" name=\"ticket_quantity_a[]\" value=\""+quantity+"\" min=\"0\" placeholder=\"{{Quantity}}\">" +
					"</td>" +
					"<td>" +
						"<input class=\"evt_rate_price\" type=\"number\" name=\"ticket_amount_a[]\" value=\"" + amount + "\" step=\"0.01\" min=\"0\" placeholder=\"{{Price}}\"/>&nbsp;€&nbsp;(Euro)" +
						"</td>" +
					"<td>" +
						"<select name=\"ticket_tax_a[]\" \">";
		for (var i = 0; i < taxes.length; i++) {
			var selected = "";
			log("selected_tax=" + selected_tax);
			log("taxes[i][1]=" + taxes[i][1]);
			if (taxes[i][1] == selected_tax) {
				selected = "selected";
			}
			content += 			"<option value=\"" + taxes[i][1] + "\" " + selected + ">" + taxes[i][0] + "</option>";
		}
		content +=		"</select>" +
					"</td>" +
					"<td>settings</td>" +
					"<td id=\"remove_" + id + "\"></td>";
		$("#" + id).html(content);
		$("#" + divName).append("<tr id=\"advanced_" + id + "\"></tr>");
		var content =
					"<td colspan=\"7\">" +
						"<table id=\"table_advanced_" + id + "\" width=\"100%\">" +
							"<tr>" +
								"<td class=\"form_label\" width=\"200px\">Description</td>" +
								"<td><textarea name=\"ticket_description_a[]\" style=\"resize: none; width: 100%;\">"+description+"</textarea></td>" +
							"</tr>" +
//							"<tr>" +
//								"<td class=\"form_label\">Start/End salling date</td>" +
//								"<td>" +
//									"<table>" +
//										"<tr>" +
//											"<td align=\"right\">Sale starts at</td>" +
//											"<td><input type=\"text\" name=\"ticket_start_a[]\"/></td>" +
//											"<td class=\"form_help\">{{Leave empty to start from now.}}</td>" +
//										"</tr>" +
//										"<tr>" +
//											"<td align=\"right\">Sale ends at</td>" +
//											"<td><input type=\"text\" name=\"ticket_end_a[]\"/></td>" +
//											"<td class=\"form_help\">{{Leave empty to never end.}}</td>" +
//										"</tr>" +
//									"</table>" +
//								"</td>" +
//							"</tr>" +
						"</table>" +
					"</td>";
		$("#advanced_" + id).html(content);
		$("#" + id).find("[name*=ticket_name_a]").focus();
		sync_remove_button(divName);
	}
</script>
