<?php
	$f = new Form();
	$f->title = _t("Forgotten password");
	$f->action = "?action=handle_forgotten_password";
	$f->method = "POST";
	$f->add_email(_t("Email"), "email", default_value("email"), _t("Please enter your account email."));
	$f->add_submit(_t("Continue"));
	echo $f->html();
?>