<?php
	$f = new Form();
	$f->title = "Reset password";
	$f->action = "?action=handle_reset_password";
	$f->method = "POST";
	$f->add_password("New password", "clear_password1", "Choose a new password");
	$f->add_password("Retype new password", "clear_password2", "Confirm the new password");
	$f->add_hidden("password1", "");
	$f->add_hidden("password2", "");
	$f->add_submit("Reset password");
	echo $f->html();
?>
<script>
	var hash_salt = "<?php echo RANDOM_SALT ?>";
	$(document).ready(function() {
		eb_sync_hash('clear_password1', 'password1');
		eb_sync_hash('clear_password2', 'password2');
	});
	$("form").submit(function() {
		$('input[name*=clear_]').val("");
	});
</script>