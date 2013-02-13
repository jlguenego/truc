<?php
	require_once("include/event.inc");
	require_once("include/user.inc");
	
	$error_msg = "";
	if (!isset($_SESSION["login"])) {
		$error_msg = "You have to be logged in";
	}
	if (!isset($_GET["id"]) || !isset($_POST["id"])) {
		$error_msg = "No event selected";
	}
	if (isset($_POST["id"]) && event_exists($_POST["id"])) {
		$event = get_event($_POST["id"]);
	}
	if (isset($_POST['confirm'])) {
		print_r($_POST);
	}
	if (isset($_POST['confirm']) && $_POST['confirm'] != TRUE) {
		$error_msg = "You have to check the engagement";
	}
	if (isset($_POST['confirm']) && $_POST['confirm'] == TRUE && $error_msg == "") {
		
		redirect_to("event.php?id=".$_GET["id"]);
	}
?>
<html>
	<head>
		<title>Participate</title>
	</head>
<?php
	if ($error_msg != "") {
		echo $error_msg;
	} else {
?>
	<a href="index.php">Back to index</a><br/><br/>
	Are you sure you want to participate to this event?
	<form name="input" action="participate.php" method="POST">
		<input type="checkbox" name="confirm"/> I engage myself to participate at this event.<br/>
		<input type="hidden" name="id" value="<?php echo $event['id']; ?>"/>
		<input type="submit" value="Yes"/>		
	</form>
	<form name="input" action="event.php?id=<?php echo $event['id']; ?>" method="POST">
		<input type="hidden" name="confirm" value="no"/>
		<input type="submit" value="NO"/>		
	</form>
<?php
	}
?>
</html>