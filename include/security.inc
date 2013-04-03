<?php
	function security_html_injection() {
		$array = array(
			"long_description",
			"short_description",
		);
		foreach ($_GET as $key => $value) {
			if (is_array($_GET[$key])) {
				continue;
			}
			if (in_array($key, $array)) {
				$_GET[$key] = security_handle_html_injection($value, ALLOWED_TAGS);
			} else {
				$_GET[$key] = strip_tags($value);
			}
		}
	}

	function security_handle_html_injection($str, $allowed_tag = "") {
		global $g_mysqli;
		debug(sprint_r($g_mysqli));
		$str = strip_tags($str, $allowed_tag);
		$str = purify_html($str);
		$str = mysqli_real_escape_string($g_mysqli, $str);
		debug("String=".$str);
		return $str;
	}
?>