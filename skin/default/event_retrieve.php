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
		echo $event["funding_acquired"]."€/".$event["funding_wanted"]."€ funding acquired.<br/>";
	} else {
?>
		By <?php echo $author["lastname"]." ".$author["firstname"] ?><br/>
<?php
	}
	if (time() >= $event["event_date"]) {
		if ($event["funding_wanted"] > $event["funding_acquired"]) {
?>
		This event has already happened.<br/>
<?php
		}
	} else if ($event["funding_wanted"] > $event["funding_acquired"]) {
?>
		This event needs to be confirmed to happen. More people needed.<br/>
<?php
	} else {
?>
		Will append. Enough persons have registered.<br/>
<?php
	}
	echo "Date: ".date("d M Y", $event["event_date"])."<br/>";
	echo "Location: ".$event["location"]."<br/>";
	echo "Deadline: ".date("d M Y", $event["event_deadline"])."<br/>";
?>
	<a href="?action=get_form&amp;type=participation&amp;event_id=<?php echo $_GET["id"] ?>">Participate</a><br/>
	<h3>In short</h3>
<?php
	echo "Event website: <a href=\"".$event["link"]."\">".$event["link"]."</a><br/>";
	echo $event["short_description"]."<br/><br/><br/>";
?>
	<h3>Rates for this events</h3>
	<table border="1px">
		<tr>
			<th>Categories</th>
			<th>Rates</th>
			<th>Taxes</th>
			<th>Rate TTC</th>
		</tr>
<?php
	foreach (events_rates($event['id']) as $rate) {
		$tax = $rate['tax_rate'];
		$label = $rate['label'];
		$amount_ht = curr($rate['amount']);
		$amount_ttc = curr($amount_ht * (($tax/100) + 1));
?>
		<tr>
			<td><?php echo $label ?></td>
			<td><?php echo $amount_ht ?></td>
			<td><?php echo $tax ?>%</td>
			<td><?php echo $amount_ttc ?></td>
		</tr>
<?php
	}
?>
	</table>
<?php
	echo $event["long_description"]."<br/>";
?>