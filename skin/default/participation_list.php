<div class="evt_title"><p>{{Attendee list}}</p></div>
{{Event name}}: <?php echo "<a href=\"?action=retrieve&amp;type=event&amp;id=".$g_display["event"]->id."\">".$g_display["event"]->title."</a>"; ?><br/>
{{Organizer}}: <?php echo $g_display["event"]->organizer_name; ?>
<table class="evt_table inline">
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
<?php
	if (is_admin_logged()) {
?>
		<td><a href="<?php echo $devis->url(); ?>"><?php echo $devis->label; ?></a></td>
<?php
	} else {
?>
		<td><?php echo $devis->label; ?></td>
<?php
	}
?>
		<td><?php echo $item->event_rate_name; ?></td>
		<td class="evt_curr"><?php echo $item->total_ht; ?>€</td>
		<td class="evt_curr"><?php echo $item->total_tax; ?>%</td>
		<td class="evt_curr"><?php echo $item->total_ttc; ?>€</td>
		<td><?php echo $item->attendee_title; ?></td>
		<td><?php echo $item->attendee_firstname; ?></td>
		<td><?php echo $item->attendee_lastname; ?></td>
<?php
	if (is_admin_logged() && !$devis->is_really_paid()) {
?>
		<td><a class="evt_button evt_btn_small" href="?action=confirm&amp;type=bill&amp;id=<?php echo $devis->id; ?>">{{Confirm}}</a></td>
		<td><a class="evt_button evt_btn_small" href="?action=delete&amp;type=bill&amp;id=<?php echo $devis->id; ?>">{{Delete}}</a></td>
<?php
	}
?>
	</tr>
<?php
	}
?>
</table>