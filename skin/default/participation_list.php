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
		<th>{{Lastname}}</th>
		<th>{{Firstname}}</th>
		<th>{{Email}}</th>
	</tr>
<?php
	foreach ($g_display["participations"] as $participation) {
		list($item, $bill) = $participation;
		if ($item->class != '/item/ticket') {
			continue;
		}
		$user = User::get_from_id($bill->user_id);
		$rate_name = $item->event_rate_name;
		$lastname = $item->attendee_lastname;
		$firstname = $item->attendee_firstname;
?>
	<tr>
		<td><a href="<?php echo $bill->url(); ?>" target="_blank"><?php echo $bill->label; ?></a></td>
		<td><?php echo $rate_name; ?></td>
		<td class="evt_curr"><?php echo $item->total_ht; ?>€</td>
		<td class="evt_curr"><?php echo $item->total_tax; ?>%</td>
		<td class="evt_curr"><?php echo $item->total_ttc; ?>€</td>
		<td><?php echo $lastname; ?></td>
		<td><?php echo $firstname; ?></td>
		<td><?php echo $user->email; ?></td>
<?php
	if (is_admin_logged() && !$bill->is_really_paid()) {
?>
		<td><a class="evt_button evt_btn_small" href="?action=confirm&amp;type=bill&amp;id=<?php echo $bill->id; ?>">{{Confirm}}</a></td>
		<td><a class="evt_button evt_btn_small" href="?action=delete&amp;type=bill&amp;id=<?php echo $bill->id; ?>">{{Delete}}</a></td>
<?php
	}
?>
	</tr>
<?php
	}
?>
</table>