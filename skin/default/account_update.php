<?php
	$user = $g_display["user"];

	$f = new Form();
	$f->cancel = true;
	$f->cancel_url = "?action=retrieve&amp;type=account";
	$f->title = _t("Account update");
	$f->action = "?action=update&amp;type=account";
	$f->method = "POST";
	$f->add_text(_t("Firstname"), "firstname",
		default_value("firstname", format_firstname($user->firstname)), _t("Your Firstname."));
	$f->add_text(_t("Lastname"), "lastname",
		default_value("lastname", format_lastname($user->lastname)), _t("Your Lastname."));
	$f->add_email(_t("Email"), "email",
		default_value("email", $user->email),
		_t("A valid Email you want to associate to your account."));
	$item = $f->add_text(_t("Phone number (optional)"), "phone",
		default_value("phone", $user->phone),
		_t("Used only when processing order if we need to contact you quickly."));
	$item->is_optional = true;
	$item = $f->add_text(_t("Street# and street name"), "street",
		default_value("street", $user->street), _t("Number and street name"));
	$item->other_attr = "size=\"70\"";
	$f->add_text(_t("ZIP"), "zip", default_value("zip", $user->zip),
		_t("ZIP code of your city."));
	$f->add_text(_t("City"), "city", default_value("city", $user->city),
		_t("Your city."));
	$f->add_select(_t("Country"), "country", form_get_country_options(default_value("country", $user->country)),
		_t("Your country"));
	$f->add_text(_t("State (optional)"), "state", default_value("state", $user->state),
		_t("Your state if any."));
	$f->add_password(_t("New Password (optional)"), "clear_new_pass",
		_t("Leave empty if you do not want to change your password."));
	$f->add_password(_t("Retype new Password (optional)"), "clear_new_pass2",
		_t("Retype your new password."));
	$f->add_hidden("new_pass", "");
	$f->add_hidden("new_pass2", "");
	$f->add_hidden("id", $user->id);
	$f->add_submit(_t("Update"));
	echo $f->html();
?>
<script>
	var hash_salt = "<?php echo RANDOM_SALT ?>";
	$(document).ready(function() {
		eb_sync_hash('clear_new_pass', 'new_pass');
		eb_sync_hash('clear_new_pass2', 'new_pass2');
	});
	$("form").submit(function() {
		$('input[type=password]').attr('name', '');
	});
</script>