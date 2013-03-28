<span class="evt_title">Participant list</span>
Event name: <?php echo $g_display["event"]->title; ?><br/>
Organizer: <?php echo $g_display["event"]->organizer_name; ?>
<table class="evt_table">
	<tr>
		<th>Label</th>
		<th>Ticket name</th>
		<th>Ticket amount</th>
		<th>Tax</th>
		<th>Ticket total due</th>
		<th>Title</th>
		<th>Firstname</th>
		<th>Lastname</th>
	</tr>
<?php
	foreach ($g_display["participations"] as $participation) {
		list($item, $devis) = $participation;
?>
	<tr>
		<td><?php echo $devis->label; ?></td>
		<td><?php echo $item->event_rate_name; ?></td>
		<td><?php echo $item->total_ht; ?></td>
		<td><?php echo $item->total_tax; ?></td>
		<td><?php echo $item->total_ttc; ?></td>
		<td><?php echo $item->participant_title; ?></td>
		<td><?php echo $item->participant_firstname; ?></td>
		<td><?php echo $item->participant_lastname; ?></td>
	</tr>
<?php
	}
?>
</table>