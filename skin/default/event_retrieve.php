<?php
	require_once("include/user.inc");
	require_once("include/misc.inc");
	
	$event = $g_display["event"];
	$author = $g_display["author"];
?>
<a href="index.php">Go back to index<a/><br/><br/>
<?php
	echo "<h1>".$event->title."</h1>";
	if (user_can_administrate_event($event)) {
		if ($event->is_published()) {
?>
			This event is published.<br/>
<?php
		} else {
?>
			This event is not published.<br/>
<?php
		}
?>
		<a href="?action=get_form&amp;type=event&amp;id=<?php echo $event->id ?>">Edit event</a><br/>
		<a href="?action=delete&amp;type=event&amp;id=<?php echo $event->id ?>">Delete event</a><br/>
<?php
		echo $event->funding_acquired."€/".$event->funding_wanted."€ funding acquired.<br/>";
	} else {
?>
		By <?php echo $author["lastname"]." ".$author["firstname"] ?><br/>
<?php
	}
	if (time() >= s2t($event->event_date, '%Y-%m-%d')) {
?>
		This event has already happened.<br/>
<?php
	} else if ($event->is_confirmed()) {
?>
		Will append. Enough persons have registered.<br/>
<?php
	} else if ($event->is_cancelled()) {
?>
		This event is cancelled.<br/>
<?php
	} else {
?>
		This event needs to be confirmed to happen. More people needed.<br/>
<?php
	}
	debug("event->event_date=".$event->event_date);
	echo "Date: ".$event->event_date."<br/>";
	echo "Location: ".$event->location."<br/>";
	echo "Deadline: ".$event->event_deadline."<br/>";
?>
	<a href="?action=get_form&amp;type=participation&amp;event_id=<?php echo $event->id ?>">Participate</a><br/>
	<h3>In short</h3>
<?php
	echo "Event website: <a href=\"".$event->link."\">".$event->link."</a><br/>";
	echo $event->short_description."<br/><br/><br/>";
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
	foreach (events_rates($event->id) as $rate) {
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
	echo $event->long_description."<br/>";
?>