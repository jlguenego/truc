<?php
	$scenario = $g_display["scenario"];
	$event = $g_display["event"];
	$rates = $g_display["rates"];

	$f = new Form();
	$button_text = "";
	$f->cancel = true;
	if ($scenario == "create") {
		$button_text = "Create";
		$f->title = "Event creation ";
	} else {
		$button_text = "Update";
		$f->title = "Event edition ";
	}
	$f->action = $g_display["form_action"];
	$f->method = "POST";
	$item = $f->add_text("Title", "title", default_value("title", $event->title),
		"Conference, or meeting name.");
	$item->other_attr = 'size="60" maxlength="255"';
	$item = $f->add_text("Organizer name", "organizer_name", default_value("organizer_name", $event->organizer_name),
		"The entity that is responsible for organizing the event.");
	$item->other_attr = 'size="60" maxlength="255"';
	if (!$event->has_accountancy_activity()) {
		$f->add_number("Required funding", "funding_needed",
			default_value("funding_needed", $event->funding_needed),
			"Minimum amount of fund needed to organize the event. Please indicate 0 if no requirement.");
	}
	$f->add_text("Event starting date", "happening_t",
		default_value("happening_t", $event->happening_t),
		"Date at which starts the event (Format: YYYY-MM-DD).");
	$f->add_text("Confirmation date", "confirmation_t",
		default_value("confirmation_t", $event->confirmation_t),
		"Maximum date at which the event will be confirmed or cancelled (Format: YYYY-MM-DD).");
	$f->add_text("Ticket Sale opening start date", "open_t",
		default_value("open_t", $event->open_t),
		"Date at which starts the ticket reservation or sale (Format: YYYY-MM-DD).");
	$item = $f->add_text("Event place", "location",
		default_value("location", $event->location),
		"Name of the place where will occur the event. Please indicate an accurate address (street, street no, city, zip, state, country)");
	$item->other_attr = 'size="60" maxlength="255"';
	$item = $f->add_text("Web site", "link", default_value("link", $event->link),
		"Official event web site (if any).");
	$item->other_attr = 'size="60" maxlength="255"';
	$f->add_textarea("Short description", "short_description",
		default_value("short_description", $event->short_description),
		"Enter a short description of the event. (HTML editor)");
	$item = $f->add_textarea("Long description", "long_description",
		default_value("long_description", $event->long_description),
		"Enter a long description of the event. (HTML editor)");
	$item->other_attr = 'width="200px"';

	if (!$event->has_accountancy_activity()) {
		$selected = "";
		if ($event->type == EVENT_TYPE_ANONYMOUS) {
			$selected = " selected";
		}
		$options = "<option value=\"".EVENT_TYPE_ANONYMOUS."\"${selected}>Anonymous</option>";
		$selected = "";
		if ($event->type == EVENT_TYPE_NOMINATIVE) {
			$selected = " selected";
		}
		$options .= "<option value=\"".EVENT_TYPE_NOMINATIVE."\"${selected}>Nominative</option>";
		$selected = "";
		if ($event->type == EVENT_TYPE_FREE_NOMINATIVE) {
			$selected = " selected";
		}
		//$options .= "<option value=\"".EVENT_TYPE_FREE_NOMINATIVE."\"${selected}>Free</option>";

		$f->add_select("Select your event type", "event_type", $options,
			"For a nominative event, the participant name will be indicated on ticket.");
	}
	$f->add_hidden("id", $event->id);
	$f->add_raw_html(<<<EOF
<div id="rates">
</div>
<a href="JavaScript:addRate('rates');">Add another ticket rate</a><br/><br/>
EOF
);
	if ($scenario == "create") {
		$f->add_checkbox("I have read and agree to the <a href=\"\">Terms and Conditions</a>", "confirm",
			"", "You have to check this to continue.");
	}
	$f->add_submit($button_text);
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

	tinyMCE.init({
	        // General options
	        mode : "textareas",
	        theme : "advanced",
	        plugins : "lists,spellchecker,advhr,preview",

	        // Theme options
	        theme_advanced_buttons1 : "fontsizeselect,|,bold,italic,underline,|,bullist,numlist,|,undo,redo,|,link,unlink,anchor,image,|,copy,cut,paste,|,code,|,preview,",
	        theme_advanced_toolbar_location : "top",
	        theme_advanced_toolbar_align : "left",
	        theme_advanced_statusbar_location : "bottom",
	        theme_advanced_resizing : true,

	        // Skin options
	        skin : "o2k7",
	        skin_variant : "silver",

	        // Example content CSS (should be your site CSS)
	        content_css : "css/example.css",

	        // Drop lists for link/image/media/template dialogs
	        template_external_list_url : "js/template_list.js",
	        external_link_list_url : "js/link_list.js",
	        external_image_list_url : "js/image_list.js",
	        media_external_list_url : "js/media_list.js",

	        // Replace values for the template plugin
	        template_replace_values : {
	                username : "Some User",
	                staffid : "991234"
	        }
	});
</script>
