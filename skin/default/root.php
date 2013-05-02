	<div id="evt_menu">
		<a class="evt_button" href="?action=get_form&amp;type=event">{{Create an event}}</a>
		<a class="evt_button" href="events">{{See events}}</a>
<?php
	// Verify if user is logged in to show differents menu.
	if (is_admin_logged()) {
?>
			<a class="evt_button" href="?action=supervision">{{Events supervision}}</a>
<?php
	// Verify if user is logged in to show differents menu.
	} else if (is_logged()) {
?>
			<a class="evt_button" href="?action=supervision">{{Manage my events}}</a>
<?php
	}
?>
	</div>

