<?php
	// Verify if user is logged in to show differents menu.
	if (!is_logged()) {
?>
	<a href="?action=retrieve&amp;type=events">See events</a>
<?php
	} else {
?>
	<a href="?action=retrieve&amp;type=account">Show Account</a><br/>
	<a href="?action=get_form&amp;type=event">Create an event</a><br/>
	<a href="?action=retrieve&amp;type=events">See events</a><br/>
	<a href="?action=sign_out">Sign out<a/><br/>
	<a href="?action=supervision">Events supervision</a>
<?php
	}
?>