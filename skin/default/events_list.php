<?php
	require_once("include/event.inc");
	require_once("include/user.inc");
	require_once("include/rate.inc");
	
	$content = <<<EOF
<html>
	<head>
		<title>Events lists</title>
	</head>
	<a href="index.php">Go back to index<a/><br/><br/>
EOF;
	$content .= "<ul>\n";
		foreach (list_events() as $event) {
			$content .= "<li>";
			$content .= date("d M Y", $event["event_date"]).": ";
			$content .= "<a href=\"?action=retrieve&amp;type=event&amp;id=".$event['id']."\">".$event['title']."</a>";
			$content .= "</li>\n";
		}
		$content .= "</ul>\n";
	$content .= "</html>";
	echo $content;
?>