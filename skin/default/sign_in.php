<span style="color:red;">
<?php
	echo $g_error_msg;
?>
</span><br/>
<?php
	$f = new Form();
	$f->action = "?action=authenticate";
	$f->method = "POST";
	$f->add_text("Login", "login", default_value("login"), "Enter your identifier");
	$f->add_password("Password", "password", "Enter your password");
	$f->add_submit("Sign in");
	echo $f->html();
?>

<a href="?action=get_form&type=forgotten_password">Forgot your password?</a>