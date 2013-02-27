<?php
	require_once("include/user.inc");
	require_once("include/event.inc");
	require_once("include/manage.inc");
	
	if (!isset($_GET["id"])) {
		redirect_to("index.php");
	}
	
	$user = get_user($_GET["id"]);
	$lastname = strtoupper($user['lastname']);
	$firstname = ucfirst($user['firstname']);
?>
<html>
	<head>
		<title>Account retrieve</title>
	</head>
	
	<a href="index.php">Go back to index</a><br/><br/>
	Account details:
	<table border="2px">
		<tr>
			<td>Username</td>
			<td><?php echo $user['login'] ?></td>
		</tr>
		<tr>
			<td>Firstname</td>
			<td><?php echo $firstname ?></td>
		</tr>
		<tr>
			<td>Lastname</td>
			<td><?php echo $lastname ?></td>
		</tr>
		<tr>
			<td>Email</td>
			<td><?php echo $user['email'] ?></td>
		</tr>
	</table>
	
<?php
	if ($user['login'] == $_SESSION['login']) { // If the user is the owner
?>
	<a href="?action=update&amp;type=account">Edit your account</a><br/>
<?php
	}
?>
	<h3>Events organized:</h3>
	<ul>
<?php
	foreach (user_events($_GET["id"]) as $event) {
		$id = $event['id'];
		$title = $event['title'];
?>
		<li>
			<?php echo date("d M Y", $event["event_date"]) ?>: 
			<a href="?action=retrieve&amp;type=event&amp;id=<?php echo $id ?>">
				<?php echo $title ?>
			</a>
		</li>
<?php
	}
?>
	</ul>
	
	<h3>Participations:</h3>
	<ul>
<?php
	$user_part = user_participations($_GET["id"]);
	foreach ($user_part as $participation) {
		$event = get_event($participation["id_event"]);$id = $event['id'];
		$title = $event['title'];
		$quantity = $participation["quantity"];
?>
		<li>
			<?php echo date("d M Y", $event["event_date"]) ?>: 
			<a href="?action=retrieve&amp;type=event&amp;id=<?php echo $id ?>">
				<?php echo $title ?>
			</a>
			(<?php echo $quantity ?> tickets)
		</li>
<?php
	}
?>
	</ul>
</html>