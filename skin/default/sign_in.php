<?php
	$f = new Form();
	$f->title = _t("Sign in")." | <a href=\"index.php?action=get_form&amp;type=account\">"._t("Create an account")."</a>";
	$f->action = "?action=authenticate";
	if (isset($_GET["redirect"])) {
		$f->action .= "&amp;redirect=yes";
	}
	$f->method = "POST";
	$f->add_text(_t("Email"), "email", default_value("email"), _t("Enter your email"));
	$f->add_password(_t("Password"), "clear_password", _t("Enter your password"));
	$f->add_hidden("password", "");
	$f->add_submit(_t("Sign in"));
	echo $f->html();
?>
<br/>
<br/>
<a href="?action=get_form&type=forgotten_password">{{Forgot your password?}}</a><br/>
<script>
	var hash_salt = "<?php echo RANDOM_SALT ?>";
	$(document).ready(function() {
		eb_sync_hash('clear_password', 'password');
	});
	$("form").submit(function() {
		$('input[name*=clear_]').val("");
	});
</script>