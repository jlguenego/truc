<?php
	function dd_get_colname($f_name) {
		$result =  preg_replace("#^(.*)_id$#", "id_$1", $f_name);
		debug("dd_get_colname($f_name)=".$result);
		return $result;
	}

	function dd_get_classname($type) {
		return ucfirst(strtolower($type));
	}

	//$g_dd = new DataDictionary();
	//$type = $g_dd->add_type("advertisement");
	//$type->add_field();

	$g_dd = array(
		"advertisement" => array(
			array("content_h", "html"),
			array("event_id", "event"),
		),
		"guest" => array(
			array("email", "mail"),
		),
		"interaction" => array(
			array("guest_id", "guest"),
			array("advertisement_id", "advertisement"),
		),
		"event" => array(
			array("title", "string"),
		),
	);
?>