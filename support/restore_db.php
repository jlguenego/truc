<?php
	define("BASE_DIR", dirname(dirname(__FILE__)));
	require_once(BASE_DIR . "/include/constants.inc");
	require_once(BASE_DIR . "/include/globals.inc");
	require_once(BASE_DIR . "/include/misc.inc");
	require_once(BASE_DIR . "/include/db.inc");

	$dirname = str_replace("\\", "/", BASE_DIR."/support/backup");

	db_set_constraints(false);
	foreach (ls($dirname) as $file) {
		//if ($file == 'event_guest.sql') {
		//	continue;
		//}
		db_execute_file($dirname."/".$file);
	}
	db_set_constraints(true);
?>