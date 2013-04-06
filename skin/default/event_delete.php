<?php
	$event = $g_display["event"];
?>
Are you sure you want to delete this event?
<div class="form_delete_confirmation">
	<form name="input" action="?action=delete&amp;type=event" method="POST">
		<input type="hidden" name="confirm" value="yes"/>
		<input type="hidden" name="id" value="<?php echo $event->id; ?>"/>
		<input type="submit" value="Yes"/>
	</form>
	<form name="input" action="?action=retrieve&amp;type=event&amp;id=<?php echo $event->id; ?>" method="POST">
		<input type="hidden" name="confirm" value="no"/>
		<input type="submit" value="No" id="no"/>
	</form>
</div>
<script>
	$(document).ready(function() {
		$("#no").focus();
	});
</script>