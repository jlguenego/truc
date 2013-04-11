<?php
	$scenario = $g_display["scenario"];
	$event = $g_display["event"];
	$rates = $g_display["rates"];
	$user = $g_display["user"];

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
	$item->other_attr = ' size="60" maxlength="255"';
	$item = $f->add_text("Organizer name", "organizer_name", default_value("organizer_name", $event->organizer_name),
		"The entity that is responsible for organizing the event.");
	$item->other_attr = ' size="60" maxlength="255"';
	$item = $f->add_text("Organizer phone (optional but recommended)", "phone",
		default_value("phone", $event->phone, $user->phone),
		"Contact phone not published. Used only for support purpose.");
	$item->is_optional = true;
	if (!$event->has_accountancy_activity()) {
		$item = $f->add_number("Required funding (Euros)", "funding_needed",
			default_value("funding_needed", $event->funding_needed),
			"Minimum amount of fund needed to organize the event. Please indicate 0 if no requirement.");
		$item->other_attr = ' step="0.01" min="0"';
	} else {
		$f->add_hidden("funding_needed", $event->funding_needed);
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
	$f->add_hidden("id", $event->id);
	$f->add_hidden("event_type", $event->type);
	$f->add_raw_html(<<<EOF
	<span class="form_h1">Create tickets and define their price</span><br/>
EOF
);
	if (!$event->has_accountancy_activity()) {
		$checked = "";
		if ($event->type == EVENT_TYPE_NOMINATIVE) {
			$checked = " checked";
		}
		$f->add_checkbox("I want to know the name of my attendees", "event_type_checkbox", $checked, "");
	}
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
	var scenerio = '<?php echo $scenario; ?>';
	$("input[type=submit]").ready(manage_submit());

	function manage_submit() {

	}

	function update_form() {
		log("update_form");
		log("date=" + $("#happening_t").val());

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
			$tax_rate = $rate["tax_rate"];
			echo "addRate('rates', '$label', '$amount', $tax_rate);";
			$i++;
		}
	}
?>
	setCounter(<?php echo $i; ?>);
	if (getCounter() < 1) {
		log("No rate.");
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
	        theme_advanced_path : false,

	        // Skin options
	        skin : "o2k7",
	        skin_variant : "silver",

	        // Example content CSS (should be your site CSS)
	        //content_css : "css/example.css",

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

	$('form').submit(function() {
		if ($("input[name=event_type_checkbox]:checked").length > 0) {
			$("input[name=event_type]").attr("value", <?php echo EVENT_TYPE_NOMINATIVE; ?>);
		} else {
			$("input[name=event_type]").attr("value", <?php echo EVENT_TYPE_ANONYMOUS; ?>);
		}
	});
</script>
