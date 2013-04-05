<?php
	$user = $g_display["user"];
?>
<span class="evt_title">Account Infos</span>
	<table class="evt_table">
		<tr>
			<th>Email</th>
			<td><?php echo $user->email ?></td>
		</tr>
		<tr>
			<th>Firstname</th>
			<td><?php echo $user->firstname ?></td>
		</tr>
		<tr>
			<th>Lastname</th>
			<td><?php echo $user->lastname ?></td>
		</tr>
		<tr>
			<th>Postal address</th>
			<td><?php echo $user->address(); ?></td>
		</tr>
	</table>

<?php
	if ($user->email == $_SESSION['email']) { // If the user is the owner
?>
	<a href="?action=get_form&amp;type=account&amp;id=<?php echo $user->id ?>">Edit your account</a><br/>
	<a href="?action=delete&amp;type=account&amp;id=<?php echo $user->id ?>" class="account_delete">Delete your account</a>
<?php
	}
?>
	<br/>
	<h3>Events organized:</h3>
	<ul>
<?php
	foreach ($g_display["events_organized"] as $event) {
		$id = $event["id"];
		$title = $event["title"];
?>
		<li>
			<?php echo $event["happening_t"] ?>:
			<a href="?action=retrieve&amp;type=event&amp;id=<?php echo $id ?>">
				<?php echo $title ?>
			</a>
		</li>
<?php
	}
?>
	</ul>