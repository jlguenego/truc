<div class="evt_title"><p>{{Event List}}</p></div>
<table class="evt_table">
	<tr>
		<th>{{Name}}</th>
		<th>{{Date}}</th>
		<th>{{Status}}</th>
		<th>{{Participate}}</th>
	</tr>
<?php
		foreach ($g_display["events"] as $event) {
			if (!$event->is_published() && !$event->can_be_administrated()) {
				debug("Event not displayed.");
			} else {
?>
	<tr>
		<td><a href="<?php echo $event->get_url(); ?>"><?php echo $event->title ?></a></td>
		<td><?php echo format_date($event->happening_t); ?></td>
		<td>
			<?php
				echo _t($event->display_status());
				if (!$event->is_published()) {
					echo _t(" (Non Published)");
				}
			?>
		</td>
		<td align="center">
			<?php
				if ($event->can_participate()) {
			?>
				<button class="evt_button evt_btn_small" style="width: 160px;" onclick="window.location='?action=get_form&amp;type=participation&amp;event_id=<?php echo $event->id ?>';return false;"><?php echo format_participate_button($event); ?></button>
			<?php
				} else if($event->get_remaining_tickets_amount() <= 0) {
			?>
			{{Sold out}}
			<?php
				}
			?>
		</td>
	</tr>
<?php
			}
		}
?>
</table>