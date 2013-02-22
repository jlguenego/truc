<?php
	require_once("include/event.inc");
	require_once("include/user.inc");
	require_once("include/rate.inc");
	
	$error_msg = "";
	if (isset($_POST['confirm'])){
		if($_POST['confirm'] != "yes") {
			redirect_to("index.php");
		} else {
			delete_events_rates($_POST['id']);
			delete_event($_POST['id']);
			redirect_to("index.php");
		}
	}
	if (isset($_GET['id']) && event_exists($_GET['id'])) {
		$event = get_event($_GET['id']);
		$user = get_user_by_login($_SESSION["login"]);
		if ($event['author_id'] != $user['id']) {
			$error_msg = "You are not the creator of this event";
		}
	} else {
		$error_msg = "No existing event given.";
	}
	
	if ($error_msg != "") {
		$content = <<<EOF
<html>
	<head>
		<title>ERROR</title>
	</head>
	${error_msg}
</html>
EOF;
		echo $content; 
	} else {
?>
<html>
	<head>
		<title>Delete "<?php echo $event['title']; ?>"</title>
	</head>
	Are you sure you want to delete this event?
	<form name="input" action="?action=delete&amp;type=event" method="POST">
		<input type="hidden" name="confirm" value="yes"/>
		<input type="hidden" name="id" value="<?php echo $event['id']; ?>"/>
		<input type="submit" value="Yes"/>		
	</form>
	<form name="input" action="event.php?id=<?php echo $event['id']; ?>" method="POST">
		<input type="hidden" name="confirm" value="no"/>
		<input type="submit" value="NO"/>		
	</form>
</html>
<?php
	}
?>