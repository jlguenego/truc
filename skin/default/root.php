	<div id="evt_menu">
		<ul>
			<li><a href="?action=get_form&amp;type=event">{{Create an event}}</a></li>
			<li><a href="events">{{See events}}</a></li>
<?php
	// Verify if user is logged in to show differents menu.
	if (is_admin_logged()) {
?>
			<li><a href="?action=supervision">{{Events supervision}}</a></li>
<?php
	// Verify if user is logged in to show differents menu.
	} else if (is_logged()) {
?>
			<li><a href="?action=supervision">{{Manage my events}}</a></li>
<?php
	}
?>
		</ul>
	</div>

