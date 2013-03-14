<a href="index.php">Go back to index<a/><br/><br/>
<table>
	<tr>
		<th>Event name</th>
		<th>Event date</th>
		<th>Deadline</th>
		<th>Status</th>
	</tr>
<?php
		foreach ($g_display["events"] as $event) {
?>
	<tr>
		<td><a href="?action=retrieve&amp;type=event&amp;id=<?php echo $event['id'] ?>"><?php echo $event['title'] ?></a></td>
		<td><?php echo date("d M Y", $event["event_date"]); ?></td>
		<td><?php echo date("d M Y", $event["event_deadline"]); ?></td>
		<td><?php echo $event["status"]; ?></td>
<?php
		$publish_button_grey = "";
		$unpublish_button_grey = "";
		$confirm_button_grey = "";
		$cancel_button_grey = "";
		if ($event["status"] == EVENT_STATUS_SUBMITTED) {
			$confirm_button_grey = "disabled";
			$cancel_button_grey = "disabled";
			$unpublish_button_grey = "disabled";
		} else {
			$publish_button_grey = "disabled";
		}
?>
		<td>
			<form action="?action=valid_event&amp;id=<?php echo $event['id'] ?>" method="POST">
				<input type="submit" value="Publish event" <?php echo $publish_button_grey ?>/>
			</form>
		</td>
		<td>
			<form action="?action=confirm_event&amp;id=<?php echo $event['id'] ?>" method="POST">
				<input type="submit" value="Confirm event" <?php echo $confirm_button_grey ?>/>
			</form>
		</td>
		<td>
			<form action="?action=cancel_event&amp;id=<?php echo $event['id'] ?>" method="POST">
				<input type="submit" value="Cancel event" <?php echo $cancel_button_grey ?>/>
			</form>
		</td>
		<td>
			<form action="?action=unpublish_event&amp;id=<?php echo $event['id'] ?>" method="POST">
				<input type="submit" value="Unpublish event" <?php echo $unpublish_button_grey ?>/>
			</form>
		</td>
	</tr>
<?php
		}
?>
</table>