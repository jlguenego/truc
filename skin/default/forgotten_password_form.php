<?php
	$f = new Form();
	$f->title = "Forgotten password";
	$f->action = "?action=handle_forgotten_password";
	$f->method = "POST";
	$f->add_email("E-Mail", "email", default_value("email"), "Please enter your account email.");
	$f->add_submit("Continue");
	echo $f->html();
?>