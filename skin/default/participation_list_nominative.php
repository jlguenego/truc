<div class="evt_title"><p>{{Attendee list}}</p></div>
{{Event name}}: <?php echo "<a href=\"?action=retrieve&amp;type=event&amp;id=".$g_display["event"]->id."\">".$g_display["event"]->title."</a>"; ?><br/>
{{Organizer}}: <?php echo $g_display["event"]->organizer_name; ?>
<table class="evt_table">
	<tr>
		<th>{{Label}}</th>
		<th>{{Ticket name}}</th>
		<th>{{Ticket amount}}</th>
		<th>{{Tax}}</th>
		<th>{{Ticket total due}}</th>
		<th>{{Title}}</th>
		<th>{{Firstname}}</th>
		<th>{{Lastname}}</th>
	</tr>
<?php
	foreach ($g_display["participations"] as $participation) {
		list($item, $devis) = $participation;
?>
	<tr>
		<td><?php echo $devis->label; ?></td>
		<td><?php echo $item->event_rate_name; ?></td>
		<td class="evt_curr"><?php echo $item->total_ht; ?>€</td>
		<td class="evt_curr"><?php echo $item->total_tax; ?>%</td>
		<td class="evt_curr"><?php echo $item->total_ttc; ?>€</td>
		<td><?php echo $item->attendee_title; ?></td>
		<td><?php echo $item->attendee_firstname; ?></td>
		<td><?php echo $item->attendee_lastname; ?></td>
	</tr>
<?php
	}
?>
</table>