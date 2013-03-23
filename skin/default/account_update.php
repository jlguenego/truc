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
	$f->add_text("Full postal address", "address", default_value("address", $user["address"]), htmlentities("<numero><rue><code postal><ville><pays>", ENT_HTML5, "UTF-8"));
	$f->add_password("New Password (optional)", "new_pass", "Leave empty if you do not want to change your password.");
	$f->add_password("Retype new Password (optional)", "new_pass2", "Retype your new password.");
	$f->add_submit("Submit");
	echo $f->html();
?>