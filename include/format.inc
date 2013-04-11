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


?>