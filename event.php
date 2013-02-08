<?php
	require_once("include/event.inc");
	require_once("include/user.inc");
	
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
		$author = get_user($event["author_id"]);
		$content .= "By ".strtoupper($author["lastname"])." ".ucfirst($author["name"])."<br/>";
		$content .= date("d M Y", $event["event_date"])."<br/>";
		$content .= html_entity_decode($event["content"]);
	} else {
		foreach (list_events() as $event) {
			$content .= date("d M Y", $event["event_date"]).": <a href=\"event.php?id=".$event["id"]."\">".$event["title"]."</a><br/>";
		}
	}
	$content .= "</html>";
	echo $content;
?>