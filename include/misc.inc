<?php
	require_once("include/constants.inc");
	
	session_start();
	
	function is_logged() {
		return isset($_SESSION['login']);
	}
	
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
			echo "<p>[DEBUG] $msg</p>";
		}
	}
	
	function create_id() {
		return rand(0, 10) * date("ymd") * date("Hi");
	}
	
	function check($var) {
		$var = mysql_escape_string($var);
		$var = htmlentities($var, 0, "UTF-8");
		// TODO: to implement
		return $var;
	}
	
	function redirect_to($path) {
		header('Location: ' . $path);
		header('Cache-Control: no-cache');
	}
?>