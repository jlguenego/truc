<?php
	require_once("include/constants.inc");
	
	session_start();
	
	function is_null_or_empty(&$var) {
		if (!isset($var)) {
			return TRUE;
		}
		if ($var == NULL) {
			return TRUE;
		}
		if ($var == "") {
			return TRUE;
		}
		return FALSE;
	}
	
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
		return rand(1, 10) * date("ymd") * date("Hi");
	}
	
	function check($var) {
		$var = mysql_escape_string($var);
		$var = htmlentities($var, 0, "UTF-8");
		// TODO: to implement
		return $var;
	}
	
	function check_mail($mail) {
		return preg_match("#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#", $mail);
	}
	
	function check_date($date) {
		if (!preg_match("#^[0-3][\d]\.[0-1][\d]\.[\d]{2}$#", $date)) {
			return FALSE;
		}
		list($day, $month, $year) = explode(".", $date);
		if ($month > 12) {
			return FALSE;
		}
		if (($month == "01" || $month == "03" || $month == "05"|| $month == "07"
			|| $month == "08" || $month == "10" || $month == "12") 
			&& $day > 31) {
			return FALSE;
		} elseif (($month == "04" || $month == "06"|| $month == "09" 
			|| $month == "11") && $day > 30) {
			return FALSE;
		} elseif ((is_bisextil($year) && $day > 29) 
			|| (!is_bisextil($year) && $day > 28)) {
			return FALSE;
		}
		return TRUE;
	}
	
	function is_bisextil($year) {
		if ($year == "00" && ($year % 400) == 0) {
			return TRUE;
		}
		if (($year % 4) == 0) {
			return TRUE;
		}
		return FALSE;
	}
	
	function redirect_to($path) {
		header('Location: ' . $path);
		header('Cache-Control: no-cache');
	}
	
	function is_number($var) {
		return preg_match("#^[\d]+$#", $var);
	}
?>