<?php
	$user = $g_display["user"];
?>
<a href="index.php">Back to index</a><br/><br/>
<span style="color:red;">
	<?php echo "$g_error_msg<br/>"; ?>
</span>
Please enter your info:
<?php
	$f = new Form();
	$f->action = "?action=update&amp;type=account";
	$f->method = "POST";
	$f->add_text("Firstname", "firstname", default_value("firstname", $user["firstname"]), "Your Firstname.");
	$f->add_text("Lastname", "lastname", default_value("lastname", $user["lastname"]), "Your Lastname.");
	$f->add_email("E-Mail", "email", default_value("email", $user["email"]), "A valid E-Mail you want to associate to your account.");
	$f->add_text("Street# and street name", "street", default_value("street", $user["street"]), "Number and street name");
	$f->add_text("ZIP", "zip", default_value("zip", $user["zip"]), "ZIP code of your city.");
	$f->add_text("City", "city", default_value("city", $user["city"]), "Your city.");
	$f->add_text("Country", "country", default_value("country", $user["country"]), "Your country");
	$f->add_text("State (if necessary)", "state", default_value("state", $user["state"]), "Your state if any");
	$f->add_password("New Password (optional)", "new_pass", "Leave empty if you do not want to change your password.");
	$f->add_password("Retype new Password (optional)", "new_pass2", "Retype your new password.");
	$f->add_submit("Submit");
	echo $f->html();
?>