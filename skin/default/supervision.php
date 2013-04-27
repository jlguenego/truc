<?php
	global $g_event_states;
	global $g_event_publish_flag;

?>

<table class="evt_table inline">
	<tr>
		<th>{{Event ID}}</th>
		<th>{{Event name}}</th>
		<th style="min-width: 80px;">{{Event date}}</th>
		<th>{{Confirmation date}}</th>
		<th>{{Status}}</th>
		<th>{{Published?}}</th>
	</tr>
<?php
		foreach ($g_display["events"] as $event) {
			$status = $g_event_states[$event->status];
			$publish_flag = $g_event_publish_flag[$event->publish_flag];
?>
	<tr>
		<td><?php echo $event->id; ?></td>
		<td><a href="?action=retrieve&amp;type=event&amp;id=<?php echo $event->id ?>"><?php echo $event->title ?></a></td>
		<td class="evt_date"><?php echo $event->happening_t; ?></td>
		<td class="evt_date"><?php echo $event->get_confirmation_date(); ?></td>
		<td><?php echo $event->display_status(); ?></td>
		<td><?php echo _t($publish_flag); ?></td>
<?php
			$confirm_button_grey = "";
			$cancel_button_grey = "";
			$publish_button_grey = "";
			if ($event->status != EVENT_STATUS_PLANNED) {
				$confirm_button_grey = "disabled";
				$cancel_button_grey = "disabled";
			}
			if ($event->status == EVENT_STATUS_INACTIVATED
				|| (!$event->is_ready_for_publication())) {
				$publish_button_grey = "disabled";
			}
			if (is_admin_logged()) {
				if (!$event->is_published()) {
?>
		<td>
			<form action="?action=publish_event&amp;id=<?php echo $event->id ?>" method="POST">
				<input type="submit" value="{{Publish}}" <?php echo $publish_button_grey ?>/>
			</form>
		</td>
<?php
				} else {
?>
		<td>
			<form name="unpublish" action="?action=unpublish_event&amp;id=<?php echo $event->id ?>" method="POST">
				<input type="submit" value="{{Unpublish}}" <?php echo $publish_button_grey ?>/>
				<input type="hidden" name="reason" />
			</form>
		</td>
<?php
				}
			}
?>
		<td>
			<form action="?action=confirm_event&amp;id=<?php echo $event->id ?>" method="POST">
				<input type="submit" value="{{Confirm}}" <?php echo $confirm_button_grey ?>/>
			</form>
		</td>
		<td>
			<form action="?action=cancel_event&amp;id=<?php echo $event->id ?>" method="POST">
				<input type="submit" value="{{Cancel}}" <?php echo $cancel_button_grey ?>/>
			</form>
		</td>
	</tr>
<?php
		}
?>
</table>
<div id="dialog" style="display: none;" title="Unpublish reason">
	<textarea id="dialog_textarea">The reason is ...</textarea>
</div>
<script>
	$(document).ready(eb_unpublish);
</script>