<?php
	header("Content-Type: text/plain");
	define("BASE_DIR", dirname(__FILE__));

	require_once(BASE_DIR . "/include/constants.inc");
	require_once(BASE_DIR . "/include/globals.inc");
	require_once(BASE_DIR . "/include/misc.inc");
	require_once(BASE_DIR . "/include/dd.inc");
	require_once(BASE_DIR . "/include/mail.inc");

	// execute some task if any.
	Task::run(TASK_RUNNING_DURATION);
	$type = "task";
	$classname = Record::get_classname($type);
	$remaining_tasks = $classname::get_progression($type, $_POST["event_id"]);
	echo join(",", $remaining_tasks);
?>