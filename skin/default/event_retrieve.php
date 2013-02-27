<?php
	require_once("include/event.inc");
	require_once("include/user.inc");
	require_once("include/rate.inc");
	
	if (event_exists($_GET["id"])) {
		$event = get_event($_GET["id"]);
		$content = <<<EOF
<html>
	<head>
		<title>Events lists</title>
	</head>
	<a href="index.php">Go back to index<a/><br/><br/>
EOF;
		$author = get_user($event["author_id"]);
		if ($author['login'] == $_SESSION['login']) {
			$content .= "<a href=\"?action=update&amp;type=event&amp;id=".$event["id"]."\">Edit event</a><br/>";
			$content .= "<a href=\"?action=delete&amp;type=event&amp;id=".$event["id"]."\">Delete event</a><br/>";
		} else {
			$content .= "By ".strtoupper($author["lastname"])." ".ucfirst($author["name"])."<br/>";
		}
		$content .= date("d M Y", $event["event_date"])."<br/>";
		if (time() >= $event["event_date"]) {
			if ($event["nbr_person_wanted"] > $event["nbr_person_registered"]) {
				$content .= "Cancelled";
			}
		} else {
			if ($event["nbr_person_wanted"] > $event["nbr_person_registered"]) {
				$content .= $event["nbr_person_registered"]."/".$event["nbr_person_wanted"]." persons registered<br/>";
			} else {
				$content .= "Will append. Enough persons have registered.<br/>";
			}
			$content .= "<a href=\"?action=participate&amp;id=".$_GET["id"]."\">Participate</a><br/>";
		}
		$content .= "<h3>Rates for this events</h3>";
		$content .= "<table>";
		$content .= "<tr>";
		$content .= "<th>Categories</th>";
		$content .= "<th>Rates</th>";
		$content .= "</tr>";
		foreach (events_rates($event['id']) as $rate) {
			$label = $rate['label'];
			$rate = $rate['amount'];
			$content .= "<tr>";
			$content .= "<td>$label</td>";
			$content .= "<td>$rate</td>";
			$content .= "</tr>";
		}
		$content .= "</table>";
		$content .= html_entity_decode($event["content"]);
	}
	$content .= "</html>";
	echo $content;
?>