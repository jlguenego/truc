<?php
	// Verify if user is logged in to show differents menu.
	if (!is_logged()) {
		echo "<a href=\"?action=sign_in\">Sign in</a><br/>";
		echo "<a href=\"?action=create&amp;type=account\">Register</a><br/>";
		echo "<a href=\"?action=create&amp;type=events\">See events<a/>";
	} else {
		echo "<a href=\"?action=retrieve&amp;type=account\">Show Account<a/><br/>";
?>
	<a href="?action=create&amp;type=event">Create an event<a/><br/>
	<a href="?action=retrieve&amp;type=events">See events<a/><br/>
	<a href="?action=sign_out">Sign out<a/><br/>
<?php
		if (is_admin()) {
			echo "<a href=\"?action=launchpayment\">Events payment<a/>";
		}
	}
?>