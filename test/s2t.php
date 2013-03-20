<?php
	define("BASE_DIR", "..");
	require_once(BASE_DIR . "/include/misc.inc");
	require_once(BASE_DIR . "/include/layout.inc");
	require_once(BASE_DIR . "/include/globals.inc");

	$date = "2013-09-22";
	$timestamp = s2t($date, "%Y-%m-%d");
	echo $date . " => " . $timestamp . "<br/>";
	echo date('Y-m-d', $timestamp);
	layout_trace();
?>