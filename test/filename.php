<?php
	$_SESSION["locale"] = "fr";

	function i18n_filename($filename) {
		$result = preg_replace("/([.].*?)$/", ".".$_SESSION["locale"]."$1", $filename);
		if (file_exists($result)) {
			return $result;
		}
		return $filename;
	}

	$str = "toto/truc.html";

	echo i18n_filename($str);
?>