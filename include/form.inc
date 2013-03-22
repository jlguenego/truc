<?php

	function form_event_valid($update = FALSE) {
		if (is_null_or_empty($_GET["title"])) {
			throw new Exception("Event must have a title");
		}
		if (!check_date($_GET["happening_t"])) {
			throw new Exception("The given date is wrong");
		}
		if (!check_date($_GET["confirmation_t"])) {
			throw new Exception("Please choose a date for your event");
		}
		if (!check_date($_GET["open_t"])) {
			throw new Exception("The participation open date is wrong");
		}

		foreach ($_GET as $value) {
			if (is_null_or_empty($value)) {
				throw new Exception("Please fill all fields: " . sprint_r($_GET));
			}
		}

		foreach ($_GET['rates'] as $rate) {
			if (is_null_or_empty($rate)
				|| (!is_null_or_empty($rate) && !is_number($rate))) {

				throw new Exception("Please enter a number for the rates");
			}
		}

		foreach ($_GET['tax_rates'] as $tax_rate) {
			if (is_null_or_empty($tax_rate)
				|| (!is_null_or_empty($tax_rate) && !is_number($tax_rate))) {

				throw new Exception("Please enter a number for the tax rates");
			}
		}
		foreach ($_GET['labels'] as $label) {
			if (is_null_or_empty($label)) {
				throw new Exception("Please enter a label for each rate");
			}
		}
	}
?>