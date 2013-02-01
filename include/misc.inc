<?php
	require_once("include/constants.inc");
	function is_installed() {
		if (!file_exists(SETTINGS_INI)) {
			return false;
		}
		return true;
	}
	
	function println($txt) {
		echo $txt . "<br/>";
	}
	
	function debug($msg) {
		global $g_debug;
		if ($g_debug) {
			echo "<p>DEBUG: $msg</p>";
		}
	}
	
	function check($var) {
		return $var;
	}
	
	function redirect_to($path) {
		header('Location: ' . $path);
		header('Cache-Control: no-cache');
	}
?>