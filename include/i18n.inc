<?php
	function i18n_menu() {
		global $g_locales, $g_default_locale;
		$array_link = array();
		foreach ($g_locales as $locale) {
			if ($locale == $_SESSION["locale"]) {
				$array_link[] = "$locale";
			} else {
				$array_link[] = <<<EOF
<a href="?action=set_locale&amp;locale=$locale">$locale</a>
EOF;
			}
		}
		$array_locale = "[&nbsp;" . strtolower(join("&nbsp;|&nbsp;", $array_link)) . "&nbsp;]";
		$result = <<<EOF
$array_locale
EOF;
		echo $result;
	}

	function i18n_init() {
		global $g_default_locale;
		global $g_info_msg;
		if (is_null_or_empty($_SESSION["locale"])) {
			$_SESSION["locale"] = i18n_guess_locale();
		}
		if (is_null_or_empty($_SESSION["locale"])) {
			$_SESSION["locale"] = $g_default_locale;
		}
		i18n_load();
	}

	function i18n_parse($str) {
		$str = preg_replace_callback('/[{][{](.*?)[}][}]/', "i18n_gettext", $str);
		return $str;
	}

	function i18n_gettext($array) {
		return _t($array[1]);
	}

	function _t($msg) {
		global $g_i18n;

		if (isset($g_i18n[$msg])) {
			return $g_i18n[$msg];
		}
		return preg_replace("/^\[.*?\]/", "", $msg);
	}

	function i18n_load() {
		global $g_i18n;

		$filename = BASE_DIR."/locale/".$_SESSION["locale"]."/messages.txt";
		if (!file_exists($filename)) {
			return;
		}
		$a = file($filename);
		$a = preg_grep("/^\s*#/", $a, PREG_GREP_INVERT);
		$str = join("", $a);
		$g_i18n = json_decode($str, TRUE);
		debug("g_i18n=".sprint_r($g_i18n));
	}

	function i18n_guess_locale() {
		return i18n_preffered_browser_language();
	}

	function i18n_preffered_browser_language()
	{
		$result = "";
		$max_coef = 0;
		$langs = explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
		foreach($langs as $lang)
		{
			$array = explode(";",$lang);
			$codelang = $array[0];
			$coef = null;
			if (count($array) > 1) {
				$coef = $array[1];
			}
			if($coef == null) $coef = 1;
			if($coef > $max_coef)
			{
				$result = substr($codelang,0,2);
				$max_coef = $coef;
			}
		}
		return $result;
	}

	function i18n_filename($filename) {
		$result = preg_replace("/([.].*?)$/", ".".$_SESSION["locale"]."$1", $filename);
		if (file_exists($result)) {
			return $result;
		}
		return $filename;
	}

	function i18n_get_os_locale() {
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			if ($_SESSION["locale"] == "fr") {
				return 'fra';
			}
		} else {
			if ($_SESSION["locale"] == "fr") {
				return 'fr_FR';
			}
		}
		return $_SESSION["locale"];
	}
?>