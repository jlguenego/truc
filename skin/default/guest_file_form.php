<?php
	$guests = $g_display["guests"];

?>
<div class="evt_title"><p>{{Guest management}}</p></div>

<?php
	require($g_i18n->filename(SKIN_DIR."/sidebar_promote.php"));

	$f = new Form();
	$f->action = "?action=merge&amp;type=guest_file";
	$f->method = "POST";
	$f->add_file(_t("File with email address"), "guest_filename",
		_t("A valid text file that contains one email address per line."));
	$f->add_submit(_t("Submit"));
	echo $f->html();
?>
<table class="evt_table">
	<tr>
		<th>{{Id}}</th>
		<th>{{Email}}</th>
	</tr>
<?php
	foreach ($guests as $guest) {
?>
	<tr>
		<td><?php echo $guest->id; ?></td>
		<td><?php echo $guest->email; ?></td>
		<td><a href="?action=delete&amp;type=guest&amp;id=<?php echo $guest->id; ?>">Delete</a></td>
	</tr>
<?php
	}
?>

</table>

