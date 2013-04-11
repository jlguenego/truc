<?php
	$user = $g_display["user"];

	$f = new Form();
	$f->cancel = true;
	$f->cancel_url = "?action=retrieve&amp;type=account";
	$f->title = "Account update";
	$f->action = "?action=update&amp;type=account";
	$f->method = "POST";
	$f->add_text("Firstname", "firstname",
		default_value("firstname", $user->firstname), "Your Firstname.");
	$f->add_text("Lastname", "lastname",
		default_value("lastname", $user->lastname), "Your Lastname.");
	$f->add_email("E-Mail", "email",
		default_value("email", $user->email),
		"A valid E-Mail you want to associate to your account.");
	$item = $f->add_text("Phone number (optional)", "phone",
		default_value("phone", $user->phone), "Used only when processing order if we need to contact you quickly.");
	$item->is_optional = true;
	$item = $f->add_text("Street# and street name", "street",
		default_value("street", $user->street), "Number and street name");
	$item->other_attr = "size=\"70\"";
	$f->add_text("ZIP", "zip", default_value("zip", $user->zip),
		"ZIP code of your city.");
	$f->add_text("City", "city", default_value("city", $user->city), "Your city.");
	$f->add_select("Country", "country", form_get_country_options(default_value("country", $user->country)),
		"Your country");
	$f->add_text("State (optional)", "state", default_value("state", $user->state),
		"Your state if any");
	$f->add_password("New Password (optional)", "clear_new_pass",
		"Leave empty if you do not want to change your password.");
	$f->add_password("Retype new Password (optional)", "clear_new_pass2",
		"Retype your new password.");
	$f->add_hidden("new_pass", "");
	$f->add_hidden("new_pass2", "");
	$f->add_hidden("id", $user->id);
	$f->add_submit("Submit");
	echo $f->html();
?>
<script>
	var hash_salt = "<?php echo RANDOM_SALT ?>";
	$(document).ready(function() {
		eb_sync_hash('clear_new_pass', 'new_pass');
		eb_sync_hash('clear_new_pass2', 'new_pass2');
	});
	$("form").submit(function() {
		$('input[name*=clear_]').val("");
	});
</script>