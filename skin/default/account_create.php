<?php
	require_once(BASE_DIR . '/include/recaptcha.inc');

	$f = new Form();
	$f->cancel = true;
	$f->title = "Account creation";
	$f->action = "?action=create&amp;type=account";
	$f->method = "POST";
	$f->add_email("E-Mail", "email", default_value("email"), "A valid E-Mail you want to associate to your account. Will act as your login.");
	$f->add_text("Firstname", "firstname", default_value("firstname"),
		"Your Firstname.");
	$f->add_text("Lastname", "lastname", default_value("lastname"), "Your Lastname.");
	$item = $f->add_password("Password", "clear_password", "The password you want to associate to your account.");
	$item->is_optional = true;
	$item = $f->add_password("Retype Password", "clear_password2", "Retype your password.");
	$item->is_optional = true;
	$f->add_hidden("password", "");
	$f->add_hidden("password2", "");
	//$f->add_text("Street# and street name", "street", default_value("street"), "Number and street name");
	//$f->add_text("ZIP", "zip", default_value("zip"), "ZIP code of your city.");
	//$f->add_text("City", "city", default_value("city"), "Your city.");
	//$item = $f->add_text("State (Optional)", "state", default_value("state"), "Your state if any");
	//$item->is_optional = true;
	//$f->add_text("Country", "country", default_value("country"), "Your country");
	if($g_use_recaptcha) {
		$publickey = CAPTCHA_PUB_KEY; // you got this from the signup page
		$f->add_raw_html(recaptcha_get_html($publickey));
	}
	$f->add_checkbox("I have read and understood the <a href=\"CGU.pdf\" target=\"_blank\">CGU</a> and accept them.", "confirm", "", "");
	$f->add_submit("Submit");
	echo $f->html();
?>
<script>
	var hash_salt = "<?php echo RANDOM_SALT ?>";
	$(document).ready(function() {
		eb_sync_hash('clear_password', 'password');
		eb_sync_hash('clear_password2', 'password2');
	});
	$("form").submit(function() {
		$('input[name*=clear_]').val("");
	});


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