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
		if ($author['login'] == $_SESSION['login']) {
			$content .= "<a href=\"editevent.php?id=".$event["id"]."\">Edit event</a><br/>";
			$content .= "<a href=\"deleteevent.php?id=".$event["id"]."\">Delete event</a><br/>";
		} else {
			$content .= "By ".strtoupper($author["lastname"])." ".ucfirst($author["name"])."<br/>";
		}
		$content .= date("d M Y", $event["event_date"])."<br/>";
		if ($event["nbr_person_wanted"] > $event["nbr_person_registered"]) {
			$content .= $event["nbr_person_registered"]."/".$event["nbr_person_wanted"]." persons registered<br/>";
		} else {
			$content .= "Will append. Enough persons have registered.";
		}
		$content .= html_entity_decode($event["content"]);
	} else {
		foreach (list_events() as $event) {
			$content .= date("d M Y", $event["event_date"]).": 
				<a href=\"event.php?id=".$event["id"]."\">".$event["title"]."</a><br/>";
		}
	}
	$content .= "</html>";
	echo $content;
?>