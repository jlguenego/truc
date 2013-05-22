<?php
	$type = $g_display["type"];
	require($g_i18n->filename(SKIN_DIR."/sidebar_promote.php"));
?>
<div>
<a href="JavaScript:eb_create_record('<?php echo $type; ?>');">New</a>
<?php
	echo Record::get_table($type);
?>
<a href="JavaScript:eb_create_record('<?php echo $type; ?>');">New</a>
</div>
<div id="dialog" style="display: none;" title="">
	<form name="create_record" action="?action=create&amp;type=<?php echo $type; ?>" method="post">
<?php
	foreach (dd()->get_entity($type)->get_fields() as $field) {
		if (!$field->is_in_create_form) {
			continue;
		}
		if ($field->is_foreign_key()) {
?>
			<input type="hidden" name="<?php echo $field->name; ?>" value="<?php echo $_SESSION[$field->name]; ?>"/>
<?php
		} else if ($field->type == "html") {
?>
			<textarea name="<?php echo $field->name; ?>" class="apply_tinymce" placeholder="<?php echo $field->label; ?>"></textarea>
<?php
		} else if ($field->type == "timestamp_date") {
?>
			<input class="timestamp_date" type="text" name="<?php echo $field->name; ?>" placeholder="<?php echo $field->label; ?>"/>
<?php
		} else {
?>
			<input type="text" name="<?php echo $field->name; ?>" placeholder="<?php echo $field->label; ?>"/>
<?php
		}
	}
?>
	</form>
</div>
<script>
	$( ".timestamp_date" ).datepicker({ minDate: "+0d", dateFormat: "yy-mm-dd"});
</script>