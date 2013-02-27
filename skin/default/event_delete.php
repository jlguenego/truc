<?php
	$event = $g_display["event"];
?>
<html>
	<head>
		<title>Delete "<?php echo $event['title']; ?>"</title>
	</head>
	<span style="color:red;">
		<?php echo $g_error_msg."<br/>" ?>
	</span>
	Are you sure you want to delete this event?
	<form name="input" action="?action=delete&amp;type=event" method="POST">
		<input type="hidden" name="confirm" value="yes"/>
		<input type="hidden" name="id" value="<?php echo $event['id']; ?>"/>
		<input type="submit" value="Yes"/>		
	</form>
	<form name="input" action="?action=retrieve&amp;type=event&amp;id=<?php echo $event['id']; ?>" method="POST">
		<input type="hidden" name="confirm" value="no"/>
		<input type="submit" value="NO"/>		
	</form>
</html>