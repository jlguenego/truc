<?php
	$f = new Form();
	$f->title = "Reset password";
	$f->action = "?action=handle_reset_password";
	$f->method = "POST";
	$f->add_password("New password", "password1", "Choose a new password");
	$f->add_password("Retype new password", "password2", "Confirm the new password");
	$f->add_submit("Reset password");
	echo $f->html();
?>