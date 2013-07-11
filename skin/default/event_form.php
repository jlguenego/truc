<?php
	global $g_tax_rates;

	$scenario = $g_display["scenario"];
	$event = $g_display["event"];
	$tickets = $g_display["tickets"];
	$discounts = $g_display["discounts"];
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
	$item = $f->add_text(_t("Organizer name"), "organizer_name", default_value("organizer_name", $event->organizer_name, $user->get_company_name()),
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
	$item = $f->add_text(_t("VAT indentification number"), "vat",
		default_value("vat", $event->vat, $user->vat),
		_t("For EU company only."));
	$item->is_optional = true;
	if (!$event->is_confirmed()) {
		$f->add_checkbox(_t("This event is confirmed."), "is_confirmed",
			"", _t("help_checkbox_confirmation"));
	}
	$item = $f->add_text(_t("Confirmation date"), "confirmation_t",
		default_value("confirmation_t", $event->confirmation_t),
		_t("Maximum date at which the event will be confirmed or cancelled (Format: YYYY-MM-DD)."));
	$item->other_attr = 'autocomplete="off"';

	$location = Address::get_from_id($event->location_address_id);

	$placeholder = _t('Street no, Street, Zip City, etc.');

	$item = $f->add_textarea(_t("Event address"), "location_address",
		default_value("location_address", $location->address),
		_t("Address of the place where will occur the event. Please indicate an accurate address (street, street no, city, zip, state, country)"));
	$item->other_attr = 'class="addresspicker" data-addresspickeroptions=\'{"showBlockMap": false}\' placeholder="'.$placeholder.'"';

	$billing_address = Address::get_from_id($event->billing_address_id);
	$item = $f->add_textarea(_t("Billing address"), "billing_address",
		default_value("billing_address", $billing_address->address, $user->address()),
		_t("Address of the organizer. Please indicate an accurate address (street, street no, city, zip, state, country)"));
	$item->other_attr = 'class="addresspicker" data-addresspickeroptions=\'{"showBlockMap": false}\' placeholder="'.$placeholder.'"';
	$item = $f->add_text(_t("Web site (optional)"), "link", default_value("link", $event->link),
		_t("Official event web site (if any)."));
	$item->other_attr = 'size="60" maxlength="255"';
	$item->is_optional = true;
	$item = $f->add_textarea(_t("Short description"), "short_description",
		default_value("short_description", $event->short_description),
		_t("Enter a short description of the event. (HTML editor)"));
	$item->other_attr = 'style="width: 100%;" rows="5"';
	$item = $f->add_textarea(_t("Long description"), "long_description",
		default_value("long_description", $event->long_description),
		_t("Enter a long description of the event. (HTML editor)"));
	$item->other_attr = 'class="apply_tinymce" width="200px"';
	$f->add_hidden("id", $event->id);
	$f->add_hidden("event_type", $event->type);
	$f->add_raw_html(<<<EOF
	</div>
	<h1>{{Create tickets}}</h1>
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
<a href="JavaScript:eb_add_rate('tickets');">{{Add another ticket rate}}</a><br/><br/>
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
				<td><?php echo $f->get_element('vat'); ?></td>
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
	?>
	</div>
	<h1>{{Create tickets}}</h1>
	<div class="form_section">
<?php
	if (!$event->has_accountancy_activity()) {
		echo $f->get_element('event_type_checkbox');
	}
	echo $f->get_element('event_type');
?>
		<a class="evt_button evt_btn_small" href="JavaScript:eb_add_rate('tickets');">
			{{Add a paying ticket}}
		</a>
		<a class="evt_button evt_btn_small" href="JavaScript:eb_add_free_ticket('tickets');">
			{{Add a free ticket}}
		</a>
		<br/><br/>

		<table id="tickets" class="evt_rate">
		</table>
	</div>
	<h1>{{Optional: Create discount}}</h1>
	<div class="form_section">
		<a class="evt_button evt_btn_small" href="JavaScript:eb_add_fixed_discount('discounts');">
			{{Add a fixed discount}}
		</a>
		<a class="evt_button evt_btn_small" href="JavaScript:eb_add_percentage_discount('discounts');">
			{{Add a percentage discount}}
		</a>
		<a href="#" onclick="javscript:eb_show_dialog('discount_help_dialog');return false;">Help</a>
		<br/><br/>

		<table id="discounts" class="evt_rate evt_discount">
		</table>
	</div>
	<?php
		if ($scenario == "create") {
			echo $f->get_element('confirm');
		}
	?>
	<?php echo $f->get_element('form_submit_button');?>
</form>

<table id="default_ticket" style="display: none;">
	<tr data-name="basic">
		<td>
			<label>{{Ticket name}}</label><br/>
			<input type="text" name="ticket_name_a[]" value="" />
		</td>
		<td>
			<label>{{Quantity}}</label><br/>
			<input class="evt_rate_qty" type="number" name="ticket_quantity_a[]" value="" min="0" />
		</td>
		<td>
			<label>{{Price}}</label><br/>
			<input class="evt_rate_price" type="number" name="ticket_amount_a[]" value="" step="0.01" min="0" />&nbsp;€&nbsp;(Euro)
		</td>
		<td>
			<select name="ticket_tax_a[]">
<?php
	foreach ($g_tax_rates as $name => $rate) {
?>
			<option value="<?php echo $rate; ?>"><?php echo $name; ?></option>
<?php
}
?>
			</select>
		</td>
		<td>settings</td>
		<td>
			<input type="button" value="{{Remove}}" />
		</td>
	</tr>

	<tr data-name="advanced">
		<td colspan="7">
			<table id="table_advanced_" width="100%">
				<tr>
					<td class="form_label" width="200px">Description</td>
					<td><textarea name="ticket_description_a[]" style="resize: none; width: 100%;"></textarea></td>
				</tr>
<!--
				<tr>
					<td class="form_label">Start/End salling date</td>
					<td>
						<table>
							<tr>
								<td align="right">Sale starts at</td>
								<td><input type="text" name="ticket_start_a[]"/></td>
								<td class="form_help">{{Leave empty to start from now.}}</td>
							</tr>
							<tr>
								<td align="right">Sale ends at</td>
								<td><input type="text" name="ticket_end_a[]"/></td>
								<td class="form_help">{{Leave empty to never end.}}</td>
							</tr>
						</table>
					</td>
				</tr>
-->
			</table>
		</td>
	</tr>
</table>

<div id="discount_help_dialog" title="{{Discount help}}" style="display: none;">
	[[/etc/discount_help.html]]
</div>

<table id="default_fixed_discount" style="display: none;">
	<tr data-name="basic">
		<td>
			<label>{{Discount code}}</label><br/>
			<input type="text" name="discount_code_a[]" value="" />
		</td>
		<td>
			<label>{{Expiration date}}</label><br/>
			<input type="text" name="discount_date_a[]" value="" />
		</td>
		<td>
			<label>{{[discount]Amount}} (€)</label><br/>
			<input class="evt_rate_qty" type="number" name="discount_value_a[]" value="5" step="0.01" min="0" />
		</td>
		<td>
			<input type="hidden" name="discount_class_a[]" value="<?php echo DISCOUNT_CLASS_FIXED; ?>" />
			<input type="button" value="{{Remove}}" />
		</td>
	</tr>
</table>

<table id="default_percentage_discount" style="display: none;">
	<tr data-name="basic">
		<td>
			<label>{{Discount code}}</label><br/>
			<input type="text" name="discount_code_a[]" value="" />
		</td>
		<td>
			<label>{{Expiration date}}</label><br/>
			<input type="text" name="discount_date_a[]" value="" />
		</td>
		<td>
			<label>{{[discount]Rate}} (%)</label><br/>
			<input class="evt_rate_qty" type="number" name="discount_value_a[]" value="10" min="0" max="100" />
		</td>
		<td>
			<input type="hidden" name="discount_class_a[]" value="<?php echo DISCOUNT_CLASS_PERCENTAGE; ?>" />
			<input type="button" value="{{Remove}}" />
		</td>
	</tr>
</table>

<script>
	function eb_manage_submit() {
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

	function eb_update_form() {
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
		eb_update_form();
		eb_manage_submit();
	});
	$(document).ready(function() {
		eb_update_form();
		eb_manage_submit();
		addresspicker_init();
	});
	$("input").keyup(eb_manage_submit);
	$("input").change(function() {
		eb_update_form();
		eb_manage_submit();
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
			$is_free = 'true';
			if ($amount > 0) {
				$is_free = 'false';
			}
			echo "eb_add_rate('tickets', '$label', '$quantity', '$amount', $tax_rate, '$description', $is_free);";
			$i++;
		}
	}
?>
	setCounter(<?php echo $i; ?>);
<?php
	$i = 0;
	if ($discounts != NULL) {
		foreach ($discounts as $discount) {
			$code = $discount->code;
			$class = $discount->class;
			$expiration_t = t2s($discount->expiration_t);
			$type = 'fixed';
			$value = $discount->amount;
			if ($discount->class == DISCOUNT_CLASS_PERCENTAGE) {
				$value = $discount->percentage;
				$type = 'percentage';
			}
			echo "eb_add_".$type."_discount('discounts', '$code', '$expiration_t', $value);";
			$i++;
		}
	}
?>
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

	function eb_add_free_ticket(divName) {
		eb_add_rate(divName, '', '', '', '', '', true);
	}

	function eb_add_rate(divName, label, quantity, amount, selected_tax, description, is_free){
		label = label || "";
		amount = amount || "";
		quantity = quantity || "";
		description = description || "";
		is_free = is_free || false;
		log(selected_tax);
		if (selected_tax == undefined) {
			selected_tax = taxes[0][1];
		}
		counter++;
		var id = new Date().getTime();
		var content = $('#default_ticket').html();
		$("#" + divName).append(content);
		var basic = $("#" + divName).find('[data-name=basic]');
		var advanced = $("#" + divName).find('[data-name=advanced]');

		basic.removeAttr('data-name');
		advanced.removeAttr('data-name');

		basic.attr('id', 'basic_' + id);
		basic.find('[name*=ticket_name_a]').val(label);
		basic.find('[name*=ticket_quantity_a]').val(quantity);
		basic.find('[name*=ticket_amount_a]').val(amount);
		basic.find('[name*=ticket_tax_a]').val(selected_tax);
		basic.find('input[type=button]').click(function() {
			eb_removeRate(id);
		});

		if (is_free) {
			var cell = basic.find('[name*=ticket_amount_a]').parent();
			basic.find('[name*=ticket_tax_a]').parent().remove();
			cell.attr('colspan', '2');
			cell.html('{{Free}}');
			cell.append('<input type="hidden" name="ticket_amount_a[]" value="0" />');
			cell.append('<input type="hidden" name="ticket_tax_a[]" value="0" />');
		}

		advanced.attr('id', 'advanced_' + id);
		advanced.find('[name*=ticket_description_a]').val(description);
	}

	function eb_add_fixed_discount(divName, code, date, value){
		code = code || '';
		date = date || '';
		value = value || '5';
		var content = $('#default_fixed_discount').html();
		eb_add_discount(divName, content, code, date, value);
	}

	function eb_add_percentage_discount(divName, code, date, value) {
		code = code || '';
		date = date || '';
		value = value || '10';
		var content = $('#default_percentage_discount').html();
		eb_add_discount(divName, content, code, date, value);
	}

	function eb_add_discount(divName, content, code, date, value) {
		var id = new Date().getTime();
		$("#" + divName).append(content);
		var basic = $("#" + divName).find('[data-name=basic]');
		var advanced = $("#" + divName).find('[data-name=advanced]');

		basic.removeAttr('data-name');
		advanced.removeAttr('data-name');

		basic.attr('id', 'discount_' + id);
		basic.find('input[type=button]').click(function() {
			removeElement('discount_' + id);
		});

		basic.find('[name*=discount_date_a]').datepicker({ minDate: "+0d", dateFormat: "yy-mm-dd"});

		basic.find('[name*=discount_code_a]').val(code);
		basic.find('[name*=discount_date_a]').val(date);
		basic.find('[name*=discount_value_a]').val(value);
	}
</script>
