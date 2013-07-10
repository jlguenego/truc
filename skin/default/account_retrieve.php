<?php
	$user = $g_display["user"];
?>
<span class="evt_title"><p>{{Account Infos}}</p></span>
	<table class="evt_table">
		<tr>
			<th class="th_left">{{Email}}</th>
			<td><?php echo $user->email ?></td>
		</tr>
		<tr>
			<th class="th_left">{{Firstname}}</th>
			<td><?php echo format_firstname($user->firstname) ?></td>
		</tr>
		<tr>
			<th class="th_left">{{Lastname}}</th>
			<td><?php echo format_lastname($user->lastname) ?></td>
		</tr>
		<tr>
			<th class="th_left">{{Postal address}}</th>
			<td><?php echo nl2br($user->address()); ?></td>
		</tr>
		<tr>
			<th class="th_left">{{VAT#}}</th>
			<td><?php echo nl2br($user->vat); ?></td>
		</tr>
		<tr>
			<th class="th_left">{{Compagny name}}</th>
			<td><?php echo nl2br($user->compagny_name); ?></td>
		</tr>
		<tr>
			<th class="th_left">{{Phone number}}</th>
			<td><?php echo $user->phone; ?></td>
		</tr>
	</table>

<?php
	if ($user->id == $_SESSION['user_id']) { // If the user is the owner
?>
	<a href="?action=get_form&amp;type=account&amp;id=<?php echo $user->id ?>">{{Edit your account}}</a>&nbsp;|&nbsp;<a href="?action=delete&amp;type=account&amp;id=<?php echo $user->id ?>">{{Delete your account}}</a>
<?php
	}
?>
	<br/>
	<br/>
	<h3>{{Events organized:}}</h3>
	<ul>
<?php
	if (count($g_display["events_organized"]) == 0) {
		echo _t("None");
	}
?>
<table class="evt_table">
	<tr>
		<th>Date</th>
		<th>{{Event}}</th>
	</tr>
<?php
	foreach ($g_display["events_organized"] as $event) {
		$id = $event["id"];
		$title = $event["title"];
?>
		<tr>
			<td><?php echo $event["happening_t"] ?></td>
			<td>
				<a href="?action=retrieve&amp;type=event&amp;id=<?php echo $id ?>">
					<?php echo $title ?>
				</a>
			</td>
		</tr>
<?php
	}
?>
</table>
	</ul>