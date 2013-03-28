<span class="evt_title">Participant list</span>
<?php echo $g_display["items"][0]->event_name; ?>:
<table class="evt_table">
	<tr>
		<th>Ticket name</th>
		<th>Ticket amount</th>
		<th>Tax</th>
		<th>Ticket total due</th>
		<th>Title</th>
		<th>Firstname</th>
		<th>Lastname</th>
	</tr>
<?php
	foreach ($g_display["items"] as $item) {
?>
	<tr>
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