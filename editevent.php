<?php
	require_once("include/event.inc");
	require_once("include/rate.inc");
	include_once("include/tinyMCE.inc");
	
	if (isset($_POST['title']) && isset($_POST['person']) 
		&& isset($_POST['content']) && isset($_POST['date'])) {
		if (!check_date($_POST['date'])) {
			println("Not valid date");
		} else {
			$updated = update_event($_POST["id"], $_POST['title'], $_POST['content'], $_POST['date'], $_POST['person']);
			if (!$updated) {
				println("Event does not exists");
			}
			print_r($_POST['labels']);
			$i = 0;
			foreach ($_POST['labels'] as $label) {
				$rate = $_POST['rates'][$i];
				update_rate($_POST["id"], $label, $rate);
				$i++;
			}
			delete_unuse_rates($_POST["id"], $_POST['labels']);
		}
		redirect_to("event.php?id=".$_POST["id"]);
	}
	
	$error_msg = "";
	if (isset($_GET["id"])) {
		$event = get_event($_GET["id"]);
		if ($event == NULL) {
			$error_msg = "Event doesn't exists.";
		} else {
			$author = get_user($event['author_id']);
			if ($author["login"] != $_SESSION["login"]) {
				$error_msg = $author["login"]."!=".$_SESSION["login"]."<br/>
					Your are not the event creator.";
			}
			$rates = events_rates($_GET["id"]);
		}
	} else {
		$error_msg = "No event given.";
	}
	
	if ($error_msg == "") {
?>
<html>
	<head>
		<title>Edit <?php echo $event['title']; ?></title>
	</head>
	<script type="text/javascript" src="jscript/misc.js"></script>
	
	<a href="index.php">Go back to index</a><br/><br/>
	<form name="input" action="editevent.php" method="POST">
		<table>
		<tr>
			<td>Title: </td>
			<td><input type="text" name="title" value="<?php echo $event['title']; ?>"></td>
		</tr>
		<tr>
			<td>Number of person wanted: </td>
			<td><input type="text" name="person" value="<?php echo $event['nbr_person_wanted']; ?>"></td>
		</tr>
		<tr>
			<td>Date (DD.MM.YY): </td>
			<td><input type="text" name="date" value="<?php echo date("d.m.y", $event['event_date']); ?>"></td>
		</tr>
		</table>
		<div id="rates">
		</div>
<?php
	$i = 0;
	foreach ($rates as $rate) {
		$label = $rate["label"];
		$amount = $rate["amount"];
		echo "<script language=\"javascript\" type=\"text/javascript\">";
		echo "addRate('rates', '$label', '$amount');";
		echo "</script>";
		$i++;
	}
	echo "<script language=\"javascript\" type=\"text/javascript\">";
	echo "setCounter($i);";
	echo "</script>";
?>
		<input type="button" value="Add a rate" onClick="addRate('rates');">
		<textarea name="content">
			<?php echo $event['content']; ?>
		</textarea>
		<input type="hidden" name="id" value="<?php echo $event['id']; ?>"/>
		<input type="submit" value="Submit"/>
	</form>
</html>
<?php
	} else {
		println($error_msg);
	}
?>