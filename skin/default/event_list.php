<span class="evt_title">Event List</span>
<table id="evt_table">
	<tr>
		<th>Name</th>
		<th>Date</th>
		<th>Status</th>
		<th>Participate</th>
	</tr>
<?php
		foreach ($g_display["events"] as $event) {
			if (!$event->is_published() && !user_can_administrate_event($event)) {
				debug("Event not displayed.");
			} else {
?>
	<tr>
		<td><a href="?action=retrieve&amp;type=event&amp;id=<?php echo $event->id ?>"><?php echo $event->title ?></a></td>
		<td><?php echo $event->happening_t; ?></td>
		<td>
			<?php
				echo $event->display_status();
				if (!$event->is_published()) {
					echo " (Non Published)";
				}
			?>
		</td>
		<td>
			<?php
				if ($event->can_participate()) {
			?>
			<a href="?action=get_form&amp;type=participation&amp;event_id=<?php echo $event->id ?>">
				<button>Participate</button>
			</a>
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