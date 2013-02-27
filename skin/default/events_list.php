<html>
	<head>
		<title>Events lists</title>
	</head>
	<a href="index.php">Go back to index<a/><br/><br/>
	<ul>
<?php
		foreach ($g_display["events"] as $event) {
?>
	<li>
	<?php echo date("d M Y", $event["event_date"]).": "; ?>
	<a href="?action=retrieve&amp;type=event&amp;id=<?php echo $event['id'] ?>"><?php echo $event['title'] ?></a>
	</li>
<?php
		}
?>
	</ul>
</html>