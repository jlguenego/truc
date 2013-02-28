<?php
	require_once("include/user.inc");
	$event = $g_display["event"];
	$author = $g_display["author"];
?>
<a href="index.php">Go back to index<a/><br/><br/>
<?php
	echo "<h1>".$event["title"]."</h1>";
	if ($author['login'] == $_SESSION['login'] || is_admin()) {
?>
		<a href="?action=get_form&amp;type=event&amp;id=<?php echo $event["id"] ?>">Edit event</a><br/>
		<a href="?action=delete&amp;type=event&amp;id=<?php echo $event["id"] ?>">Delete event</a><br/>
<?php
		echo $event["nbr_person_registered"]."/".$event["nbr_person_wanted"]." persons registered<br/>";
	} else {
?>
		By <?php echo $author["lastname"]." ".$author["firstname"] ?><br/>
<?php
	}
	if (time() >= $event["event_date"]) {
		if ($event["nbr_person_wanted"] > $event["nbr_person_registered"]) {
?>
		This event has already happened.<br/>
<?php
		}
	} else if ($event["nbr_person_wanted"] > $event["nbr_person_registered"]) {
?>
		This event needs to be confirmed to happen. More people are wanted.<br/>
<?php
	} else {
?>
		Will append. Enough persons have registered.<br/>
<?php
	}
	echo date("d M Y", $event["event_date"])."<br/>";
?>
	<a href="?action=participate&amp;id=<?php echo $_GET["id"] ?>">Participate</a><br/>
	<h3>Rates for this events</h3>
	<table border="1px">
		<tr>
			<th>Categories</th>
			<th>Rates</th>
		</tr>
<?php
	foreach (events_rates($event['id']) as $rate) {
		$label = $rate['label'];
		$rate = $rate['amount'];
?>
		<tr>
			<td><?php echo $label ?></td>
			<td><?php echo $rate ?></td>
		</tr>
<?php
	}
?>
	</table>
<?php
	echo html_entity_decode($event["content"]);
?>