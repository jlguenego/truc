<?php
	header("Content-Type: text/plain");
	define("BASE_DIR", dirname(dirname(__FILE__)));

	require_once(BASE_DIR . "/include/constants.inc");
	require_once(BASE_DIR . "/include/globals.inc");
	require_once(BASE_DIR . "/include/misc.inc");

	$result = array();
	$result['sent_data'] = $_GET;

	$event = Event::get_from_id($_GET['event_id']);
	$discount = $event->get_discount($_GET['code'], time());
	$result['is_valid'] = false;
	if ($discount != null) {
		$result['is_valid'] = true;
		$result['discount'] = $discount;
	}

	echo json_encode($discount);
?>