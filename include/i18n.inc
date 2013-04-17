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
			$_SESSION["locale"] = $g_default_locale;
			$g_info_msg = "Bienvenue pour la 1ere fois.";
		}
	}

	function i18n_file($file) {
		return $file;
	}
	
	function i18n_parse($file) {
		$i18n_file = i18n_file($file);
		if (!file_exists($i18n_file)) {
			$str = file_get_contents($file);
			file_put_contents($i18n_file , str_replace("", "", $str));
		}
		return $i18n_file;
	}
?>