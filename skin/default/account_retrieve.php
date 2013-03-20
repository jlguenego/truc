<?php
	$user = $g_display["user"];
	$lastname = $user['lastname'];
	$firstname = $user['firstname'];
?>
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
		<tr>
			<td>Postal address</td>
			<td><?php echo $user['address'] ?></td>
		</tr>
	</table>

<?php
	if ($user['login'] == $_SESSION['login']) { // If the user is the owner
?>
	<a href="?action=get_form&amp;type=account&amp;id=<?php echo $_GET["id"] ?>">Edit your account</a><br/>
<?php
	}
?>
	<h3>Events organized:</h3>
	<ul>
<?php
	foreach ($g_display["events_organized"] as $event) {
		$id = $event['id'];
		$title = $event['title'];
?>
		<li>
			<?php echo date("d M Y", $event["happening_t"]) ?>:
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
	foreach ($g_display["participations"] as $participation) {
		$event = $participation["event"];
		$id = $event['id'];
		$title = $event['title'];
		$quantity = $participation["quantity"];
?>
		<li>
			<?php echo date("d M Y", $event["happening_t"]) ?>:
			<a href="?action=retrieve&amp;type=event&amp;id=<?php echo $id ?>">
				<?php echo $title ?>
			</a>
			(<?php echo $quantity ?> tickets)
		</li>
<?php
	}
?>
	</ul>