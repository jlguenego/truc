<?php
	$f = new Form();
	$f->title = "Sign in | <a href=\"index.php?action=get_form&amp;type=account\">Create an account</a>";
	$f->action = "?action=authenticate";
	if (isset($_GET["redirect"])) {
		$f->action .= "&amp;redirect=yes";
	}
	$f->method = "POST";
	$f->add_text("Email", "email", default_value("email"), "Enter your email");
	$f->add_password("Password", "clear_password", "Enter your password");
	$f->add_hidden("password", "");
	$f->add_submit("Sign in");
	echo $f->html();
?>
<br/>
<br/>
<a href="?action=get_form&type=forgotten_password">Forgot your password?</a><br/>
<script>
	var hash_salt = "<?php echo RANDOM_SALT ?>";
	$(document).ready(function() {
		eb_sync_hash('clear_password', 'password');
	});
	$("form").submit(function() {
		$('input[name*=clear_]').val("");
	});
</script>