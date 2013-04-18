<?php
	$devis = $g_display["devis"];
	$event = $g_display["event"];
?>
<p>
	<?php echo $devis->label; ?>
	<table class="evt_table">
		<tr>
			<th class="th_left">{{Billing entity}}</th>
			<td><?php echo $devis->username; ?></td>
		</tr>
		<tr>
			<th class="th_left">{{Billing address}}</th>
			<td><?php echo $devis->address; ?></td>
		</tr>
	</table>
	<br/>
	<table class="evt_table">
		<tr>
			<th>{{Event}}</th>
			<th>{{Rate name}}</th>
			<th>{{Amount}}</th>
			<th>{{Tax rate}}</th>
<?php
	if ($event->type == EVENT_TYPE_ANONYMOUS) {
?>
			<th>{{Quantity}}</th>
			<th>{{Total}}</th>
<?php
	}
?>
			<th>{{Total taxes}}</th>
			<th>{{Total due}}</th>
<?php
	if ($event->type == EVENT_TYPE_NOMINATIVE) {
?>
			<th>{{Title}}</th>
			<th>{{Firstname}}</th>
			<th>{{Lastname}}</th>
<?php
	}

	$i = 0;
	foreach ($devis->items as $item) {
?>
		<tr>
			<td><a href="?action=retrieve&amp;type=event&amp;id=<?php echo $event->id ?>"><?php echo $item->event_name; ?></a></td>
			<td><?php echo $item->event_rate_name; ?></td>
			<td class="evt_curr"><?php echo $item->event_rate_amount; ?>€</td>
			<td class="evt_curr"><?php echo $item->event_rate_tax; ?>%</td>
<?php
		if ($event->type == EVENT_TYPE_ANONYMOUS) {
?>
			<td class="evt_curr"><?php echo $item->quantity; ?></td>
			<td class="evt_curr"><?php echo $item->total_ht; ?>€</td>
<?php
		}
?>
			<td class="evt_curr"><?php echo $item->total_tax; ?>€</td>
			<td class="evt_curr"><?php echo $item->total_ttc; ?>€</td>
<?php
		if ($event->type == EVENT_TYPE_NOMINATIVE) {
?>
			<td><?php echo $item->attendee_title; ?></td>
			<td><?php echo $item->attendee_firstname; ?></td>
			<td><?php echo $item->attendee_lastname; ?></td>
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
			<th class="th_left">{{Total}}</th>
			<td class="evt_curr"><?php echo $devis->total_ht; ?>€</td>
		</tr>
		<tr>
			<th class="th_left">{{Total taxes}}</th>
			<td class="evt_curr"><?php echo $devis->total_tax; ?>€</td>
		</tr>
		<tr>
			<th class="th_left">{{Total due}}</th>
			<td class="evt_curr"><b><?php echo $devis->total_ttc; ?>€</b></td>
		</tr>
	</table>
</p>