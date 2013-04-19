<?php
	function format_ucfirst($array) {
		return ucfirst($array[1]);
	}

	function format_ucwords($str) {
		return preg_replace_callback('/([\w]+)/', "format_ucfirst", $str);
	}

	function format_firstname($firstname) {
		return format_ucwords(mb_strtolower($firstname, "UTF-8"));
	}

	function format_lastname($lastname) {
		return mb_strtoupper($lastname, "UTF-8");
	}

	function format_participate_button($event) {
		$participate = _t("Book tickets");
		if ($event->is_confirmed()) {
			$participate = _t("Buy tickets");
		}
		return $participate;
	}

	function format_date($date, $format = "%Y-%m-%d") {
		setlocale(LC_TIME, 'fr_FR.UTF-8');
		return strftime("%A, %d %B %Y", s2t($date, $format));
	}
?>