<?php
	// Verify if user is logged in to show differents menu.
	if (!is_logged()) {
?>
	<a href="?action=sign_in">Sign in</a><br/>
	<a href="?action=get_form&amp;type=account">Create account</a><br/>
	<a href="?action=retrieve&amp;type=events">See events<a/>
<?php
	} else {
?>
	<a href="?action=retrieve&amp;type=account">Show Account<a/><br/>
	<a href="?action=get_form&amp;type=event">Create an event<a/><br/>
	<a href="?action=retrieve&amp;type=events">See events<a/><br/>
	<a href="?action=sign_out">Sign out<a/><br/>
<?php
		if (is_admin()) {
?>
	<a href="?action=launchpayment">Events payment<a/>
<?php
		}
	}
?>