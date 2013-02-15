<?php
	require_once("include/event.inc");
	require_once("include/user.inc");
	require_once("include/rate.inc");
	
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
			$content .= "<a href=\"participate.php?id=".$_GET["id"]."\">Participate</a><br/>";
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
	} else {
		$content .= "<ul>\n";
		foreach (list_events() as $event) {
			$content .= "<li>";
			$content .= date("d M Y", $event["event_date"]).": ";
			$content .= "<a href=\"event.php?id=".$event['id']."\">".$event['title']."</a>";
			$content .= "</li>\n";
		}
		$content .= "</ul>\n";
	}
	$content .= "</html>";
	echo $content;
?>