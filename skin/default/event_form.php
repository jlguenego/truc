<?php
	$scenario = $g_display["scenario"];
	$event = $g_display["event"];
	$rates = $g_display["rates"];

	$f = new Form();
	if ($scenario == "create") {
		$f->title = "Event creation";
	} else {
		$f->title = "Event edition";
	}
	$f->action = $g_display["form_action"];
	$f->method = "POST";
	$item = $f->add_text("Title", "title", default_value("title", $event->title),
		"Conference, or meeting name.");
	$item->other_attr = 'size="60" maxlength="255"';
	$f->add_number("Required funding", "funding_needed",
		default_value("funding_needed", $event->funding_needed),
		"Minimum amount of fund needed to organize the event.");
	$f->add_text("Event starting date", "happening_t",
		default_value("happening_t", $event->happening_t),
		"Date at which starts the event (Format: YYYY-MM-DD).");
	$f->add_text("Confirmation date", "confirmation_t",
		default_value("confirmation_t", $event->confirmation_t),
		"Maximum date at which the event will be confirmed or cancelled (Format: YYYY-MM-DD).");
	$f->add_text("Ticket Sale opening start date", "open_t",
		default_value("open_t", $event->open_t),
		"Date at which starts the ticket reservation or sale (Format: YYYY-MM-DD).");
	$f->add_text("Event place", "location",
		default_value("location", $event->location),
		"Name of the place where will occur the event.");
	$f->add_text("Link", "link", default_value("link", $event->link),
		"Official event web site (if any).");
	$f->add_textarea("Short description", "short_description",
		default_value("short_description", $event->short_description),
		"Enter a short description of the event.");
	$f->add_textarea("Long description", "long_description",
		default_value("long_description", $event->long_description),
		"Enter a long description of the event. You can use specific tag to format your text. See <a href=\"\">help</a>.");
	$f->add_checkbox("All tickets must be nominative", "nominative", "checked",
		"All tickets of an nominative event must have a participant name indicated.");
	$f->add_hidden("id", $event->id);
	$f->add_raw_html(<<<EOF
<div id="rates">
</div>
<a href="JavaScript:addRate('rates');">Add another rate</a><br/><br/>
EOF
);
	$f->add_submit("Sign in");
	echo $f->html();
?>
<script>
	function update_form() {
		console.log("update_form");
		console.log("date=" + $("#happening_t").val());

		if ($("#happening_t").val() == "") {
			$("#confirmation_t").val("");
			$( "#confirmation_t" ).attr("disabled", "");
		} else {
			$( "#confirmation_t" ).removeAttr("disabled");
		}

		if ($("#confirmation_t").val() == "") {
			$("#open_t").val("");
			$( "#open_t" ).attr("disabled", "");
		} else {
			$( "#open_t" ).removeAttr("disabled");
		}

		$( "#open_t" ).datepicker('option', 'maxDate', $("#confirmation_t").val());
		var date = $("#confirmation_t").datepicker('getDate');
		if (date) {
			date.setDate(date.getDate() - 29);
			var today = new Date();
			if (date < today) {
				date = today;
			}
		}
		$( "#open_t" ).datepicker('option', 'minDate', date);
		$( "#confirmation_t" ).datepicker('option', 'maxDate', $("#happening_t").val());

		$( "#happening_t" ).datepicker({ minDate: "+0d", dateFormat: "yy-mm-dd"});
		$( "#confirmation_t" ).datepicker({ minDate: "+0d", dateFormat: "yy-mm-dd"});
		$( "#open_t" ).datepicker({ minDate: "+0d", dateFormat: "yy-mm-dd"});
	}

	$("form").change(update_form);
	$(document).ready(update_form);


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
			echo "new Array('${name}', '${rate}')";
		}
	?>
	);
<?php
	$i = 0;
	debug("rates=".sprint_r($rates));
	if ($rates != NULL) {
		foreach ($rates as $rate) {
			$label = $rate["label"];
			$amount = $rate["amount"];
			echo "addRate('rates', '$label', '$amount');";
			$i++;
		}
	}
?>
	setCounter(<?php echo $i; ?>);
	if (getCounter() < 1) {
		console.log("No rate.");
		addRate('rates');
	}
	$("[name=title]").focus();
</script>
