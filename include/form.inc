<?php
	function form_event_valid($update = FALSE) {
		debug("About to validate form");

		foreach ($_GET as $key => $value) {
			if (!$f->has_item($key)) {
				continue;
			}
			if ($f->is_optional($key)) {
				continue;
			}
			if (is_null_or_empty($value)) {
				if (is_null_or_empty($_SESSION["form"])) {
					throw new Exception("Please fill all fields");
				}
				$f = $_SESSION["form"];
				throw new Exception("Please fill all fields: " . $f->get_label($key));
			}
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
		unset($_SESSION["form"]);
	}
?>