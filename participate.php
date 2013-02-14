<?php
	require_once("include/event.inc");
	require_once("include/user.inc");
	require_once("include/manage.inc");
	
	$error_msg = "";
	$event = NULL;
	if (!isset($_SESSION["login"])) {
		$error_msg = "You have to be logged in";
	} elseif (is_null_or_empty($_GET["id"]) && is_null_or_empty($_POST['participate'])) {
		$error_msg = "No event selected";
	} else {
		$event = get_event($_GET["id"]);
	}
	if (!is_null_or_empty($event) && !is_null_or_empty($_POST['participate'])) {
		if (!is_number($_POST['person_amount'])) {
			$error_msg = "Please enter a number for the amount of person";
		}
		if (!isset($_POST['confirm'])) {
			$error_msg = "You have to check the engagement";
		}
		if ($error_msg == "") {
			$user = get_user_by_login($_SESSION['login']);
			participate($user['id'], $event["id"], $_POST['person_amount']);
			redirect_to("event.php?id=".$event["id"]);
		}
	}
?>
<html>
	<head>
		<title>Participate</title>
	</head>
	<a href="index.php">Back to index</a><br/><br/>
<?php
	if ($error_msg != "") {
		echo "$error_msg<br/><br/>";
	}
?>
	Are you sure you want to participate to this event?
	<form name="input" action="participate.php?id=<?php echo $event['id']; ?>" method="POST">
		Number of person: <input type="number" name="person_amount"/><br/>
		<input type="checkbox" name="confirm"/> I engage myself to participate to this event.<br/>
		<input type="hidden" name="participate" value="participate"/>
		<input type="submit" value="Yes"/>		
	</form>
	<form name="input" action="event.php?id=<?php echo $event['id']; ?>" method="POST">
		<input type="hidden" name="confirm" value="no"/>
		<input type="submit" value="No"/>		
	</form>
</html>