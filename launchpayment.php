<?php
	require_once("include/event.inc");
	require_once("include/user.inc");
	require_once("include/rate.inc");
	
	$content = <<<EOF
<html>
	<head>
		<title>Launch payment</title>
	</head>
	<a href="index.php">Go back to index<a/><br/><br/>
EOF;
	$content .= "<ul>\n";
	foreach (event_list() as $event) {
		debug(time()." | ".$event["event_date"]);
		if (time() <= $event["event_date"] ||
			$event["nbr_person_wanted"] > $event["nbr_person_registered"]) {
			continue;
		}
		$content .= "<li>";
		$content .= date("d M Y", $event["event_date"]).": ";
		$content .= "<a href=\"event.php?id=".$event['id']."\">".$event['title']."</a> ";
		$content .= "<a href=\"????????.php?id=".$event['id']."\">Launch payment</a>";
		$content .= "</li>\n";
	}
	$content .= "</ul>\n";
	$content .= "</html>";
	echo $content;
?>