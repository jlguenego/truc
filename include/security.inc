<?php
	function security_html_injection() {
		$array = array(
			"long_description",
			"short_description",
			"content_h",
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
		$str = strip_tags($str, $allowed_tag);
		$str = purify_html($str);
		$str = strip_tags($str, $allowed_tag);
		if (get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		debug("String=".$str);
		return $str;
	}

	function purify_html($str) {
		libxml_use_internal_errors(true);
		$doc = new DOMdocument();
		$doc->loadHTML('<?xml encoding="UTF-8">'.$str);
		return $doc->saveHTML();
	}
?>