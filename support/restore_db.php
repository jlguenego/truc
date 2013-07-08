<?php
	define("BASE_DIR", dirname(dirname(__FILE__)));
	require_once(BASE_DIR . "/include/constants.inc");
	require_once(BASE_DIR . "/include/globals.inc");
	require_once(BASE_DIR . "/include/misc.inc");
	require_once(BASE_DIR . "/include/db.inc");

	$dirname = str_replace("\\", "/", BASE_DIR."/_backup");

	db_set_constraints(false);
	foreach (ls($dirname) as $file) {
		//if ($file == 'event_guest.sql') {
		//	continue;
		//}
		try {
			db_execute_file($dirname."/".$file);
		} catch (Exception $e) {
			echo '<pre>';
			print_r($e);
			echo '</pre>';
		}
	}
	db_set_constraints(true);
?>
<html>
	<head>
		<title>Restoration done</title>
	</head>
	<body>
		Restoration success<br/>
		<a href="../index.php">Go to index</a>
	</body>
<html>