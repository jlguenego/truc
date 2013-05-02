	<div id="evt_menu">
		<p>
			<b>Organize your event without risk.</b>
		</p>
		<h1>Be sure people will support you!</h1>
		<p>
			Event-Biller is a <b>crowdfunding</b> platform that allows you to try to
			organize events.
		</p>
		<p>
			If enough attendees have booked tickets for your event and made payment
			authorization, then you can:<br/>
			<i>
				- Confirm the event<br/>
				- Capture all the payments<br/>
			</i>
			So you have both the <b>money</b> and the <b>attendees</b> to successfully make your event.
		</p>
		<a class="evt_button" href="?action=get_form&amp;type=event">{{Declare an event}}</a>
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

