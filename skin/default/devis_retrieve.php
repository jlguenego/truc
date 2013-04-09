<?php
	$devis = $g_display["devis"];
	$event = $g_display["event"];
?>
<p>
	<?php echo $devis->label; ?>
	<table class="evt_table">
		<tr>
			<th>Billing entity:</th>
			<td><?php echo $devis->username; ?></td>
		</tr>
		<tr>
			<th>Billing address: </th>
			<td><?php echo $devis->address; ?></td>
		</tr>
	</table>
	<br/>
	<table class="evt_table">
		<tr>
			<th>Event</th>
			<th>Rate name</th>
			<th>Amount HT</th>
			<th>Rate tax</th>
<?php
	if ($event->type == EVENT_TYPE_ANONYMOUS) {
?>
			<th>Quantity</th>
			<th>Total</th>
<?php
	}
?>
			<th>Total taxes</th>
			<th>Total due</th>
<?php
	if ($event->type == EVENT_TYPE_NOMINATIVE) {
?>
			<th>Title</th>
			<th>Firsname</th>
			<th>Lastname</th>
<?php
	}

	$i = 0;
	foreach ($devis->items as $item) {
?>
		<tr>
			<td><a href="?action=retrieve&amp;type=event&amp;id=<?php echo $event->id ?>"><?php echo $item->event_name; ?></a></td>
			<td><?php echo $item->event_rate_name; ?></td>
			<td><?php echo $item->event_rate_amount; ?>€</td>
			<td><?php echo $item->event_rate_tax; ?>%</td>
<?php
		if ($event->type == EVENT_TYPE_ANONYMOUS) {
?>
			<td><?php echo $item->quantity; ?></td>
			<td><?php echo $item->total_ht; ?>€</td>
<?php
		}
?>
			<td><?php echo $item->total_tax; ?>€</td>
			<td><?php echo $item->total_ttc; ?>€</td>
<?php
		if ($event->type == EVENT_TYPE_NOMINATIVE) {
?>
			<td><?php echo $item->participant_title; ?></td>
			<td><?php echo $item->participant_firstname; ?></td>
			<td><?php echo $item->participant_lastname; ?></td>
<?php
		}
?>
		</tr>
<?php
		$i++;
	}
?>
	</table>
	<br/>
	<table class="evt_table">
		<tr>
			<th>Total</th>
			<td><?php echo $devis->total_ht; ?>€</td>
		</tr>
		<tr>
			<th>Total taxes</th>
			<td><?php echo $devis->total_tax; ?>€</td>
		</tr>
		<tr>
			<th>Total due</th>
			<td><b><?php echo $devis->total_ttc; ?>€</b></td>
		</tr>
	</table>
</p>