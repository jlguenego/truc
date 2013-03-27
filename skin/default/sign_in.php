<?php
	$f = new Form();
	$f->title = "Sign in";
	$f->action = "?action=authenticate";
	if (isset($_GET["redirect"])) {
		$f->action .= "&amp;redirect=yes";
	}
	$f->method = "POST";
	$f->add_text("Login", "login", default_value("login"), "Enter your identifier");
	$f->add_password("Password", "password", "Enter your password");
	$f->add_submit("Sign in");
	echo $f->html();
?>

<a href="?action=get_form&type=forgotten_password">Forgot your password?</a><br/>
<br/>
<a href="index.php?action=get_form&amp;type=account">Don't have an account ?</a>