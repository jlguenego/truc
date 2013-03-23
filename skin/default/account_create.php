<a href="index.php">Back to index</a><br/><br/>
<span style="color:red;">
<?php
	echo $g_error_msg;
?>
</span><br/>
Please enter your info:
<?php
	$f = new Form();
	$f->action = "?action=create&amp;type=account";
	$f->method = "POST";
	$f->add_text("Login", "login", default_value("login"), "The login you will use to connect.");
	$f->add_text("Firstname", "firstname", default_value("firstname"), "Your Firstname.");
	$f->add_text("Lastname", "lastname", default_value("lastname"), "Your Lastname.");
	$f->add_password("Password", "password", "The password you want to associate to your account.");
	$f->add_password("Retype Password", "password2", "Retype your password.");
	$f->add_email("E-Mail", "email", default_value("email"), "A valid E-Mail you want to associate to your account.");
	$f->add_text("Full postal address", "address", default_value("address"), htmlentities("<numero><rue><code postal><ville><pays>", ENT_HTML5, "UTF-8"));
	require_once(BASE_DIR . '/include/recaptcha.inc');
	$publickey = CAPTCHA_PUB_KEY; // you got this from the signup page
	$f->add_raw_html(recaptcha_get_html($publickey));
	$f->add_checkbox("I have read and understood the <a href=\"CGU.pdf\" target=\"_blank\">CGU</a> and accept them.", "confirm", "", "");
	$f->add_submit("Submit");
	echo $f->html();
?>
<script>
	$('input[type=checkbox]').ready(eb_sync_next_button);
	$('input[type=checkbox]').change(eb_sync_next_button);

	function eb_sync_next_button() {
		if ($('input[type=checkbox]').is(':checked')) {
			$('input[value=Submit]').removeAttr('disabled');
		} else {
			$('input[value=Submit]').attr('disabled', 'disabled');
		}
	}
</script>