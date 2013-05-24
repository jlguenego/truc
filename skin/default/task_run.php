<?php
	$type = $g_display["type"];
	$classname = Record::get_classname($type);
?>
<div class="evt_title"><p>Task run</p></div>
<?php
	require($g_i18n->filename(SKIN_DIR."/sidebar_promote.php"));
	$tasks_left = $classname::get_progression($type, $_SESSION["event_id"]);
?>
<table class="evt_table">
	<tr>
		<th>Global remaining tasks</th>
		<th>Event remaining tasks</th>
	</tr>
	<tr>
		<td id="global"><?php echo $tasks_left[0]; ?></td>
		<td id="event"><?php echo $tasks_left[1]; ?></td>
	</tr>
</table>
<script>
	var event_id = <?php echo $_SESSION["event_id"]; ?>;
 	var id = window.setInterval(function() {
		var response = eb_execute_tasks(event_id);
		if (!response) {
			log("response is empty");
			return;
		}
		if (response == "0,0") {
			log("stop requesting");
			window.clearInterval(id);
		}
		var array = response.split(',');
		if (!array[1]) {
			log("response not well formatted.");
			return;
		}
		log("response="+response);
		$("#global").html(array[0]);
		$("#event").html(array[1]);
	}, 3000);
</script>