<?php
	require_once("include/event.inc");
	
	$event = NULL;
	if (isset($_GET["id"])) {
		$event = get_event($_GET["id"]);
	}
	$content = <<<EOF
<html>
	<head>
		<title>Events lists</title>
	</head>
	<a href="index.php">Go back to index<a/><br/><br/>
EOF;
	if ($event != NULL) {
		$content .= html_entity_decode($event["content"]);
	} else {
		foreach (list_events() as $event) {
			$content .= "<a href=\"event.php?id=".$event["id"]."\">".$event["title"]."</a><br/>";
		}
	}
	$content .= "</html>";
	echo $content;
?>