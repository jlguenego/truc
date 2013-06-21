<?php
	define("BASE_DIR", dirname(dirname(__FILE__)));

	require_once(BASE_DIR . "/include/constants.inc");
	require_once(BASE_DIR . "/include/globals.inc");
	require_once(BASE_DIR . "/include/misc.inc");
	require_once(BASE_DIR . "/include/facebook.inc");

	$config = array(
		'appId' => FACEBOOK_APP_ID,
		'secret' => FACEBOOK_SECRET_KEY,
	);

	// Initialize a Facebook instance from the PHP SDK
	$facebook = new Facebook($config);
	$user_id = $facebook->getUser();

	// Declare the variables we'll use to demonstrate
	// the new event-management APIs

	// We'll create an event in this example.
	// We'll need create_event permission for this.
	$event_id = 0;
	$event_name = "New Event API Test Event";
	$event_start = '2013-07-04T19:00:00-0700';    // We'll just start the event now.
	$event_privacy = "SECRET"; // We'll make it secret so we don't annoy folks.

	// Convenience method to print simple pre-formatted text.
	function printMsg($msg) {
		echo "<pre>$msg</pre>";
	}
?>


<?php
	printMsg('user_id: ' . $user_id);
	if(!$user_id) {
		// No user, so print a link for the user to login
		$login_url = $facebook->getLoginUrl( array(
			'scope' => 'create_event,rsvp_event'
		));
?>
<html>
	<head></head>
	<body>
		Please <a href="<?php echo $login_url; ?>">login.</a>
	</body>
</html>
<?php
		exit;
	}
?>

<html>
	<head></head>
	<body>
<?php
	// We have a user ID, so probably a logged in user.
	// If not, we'll get an exception, which we handle below.
	try {
		// Create an event
		$ret_obj = $facebook->api($user_id.'/events', 'POST', array(
			'name' => $event_name,
			'start_time' => $event_start,
			'privacy_type' => $event_privacy
		));

		if(isset($ret_obj['id'])) {
			// Success
			$event_id = $ret_obj['id'];
			printMsg('Event ID: ' . $event_id);
		} else {
			printMsg("Couldn't create event.");
		}
	} catch(FacebookApiException $e) {
		// If the user is logged out, you can have a
		// user ID even though the access token is invalid.
		// In this case, we'll get an exception, so we'll
		// just ask the user to login again here.
		$login_url = $facebook->getLoginUrl( array(
			'scope' => 'create_event, rsvp_event',
		));
		echo 'Error: Please <a href="' . $login_url . '">login.</a>';
		error_log($e->getType());
		error_log($e->getMessage());
		printMsg($e->getMessage());
		$trace = $e->getTrace();
		print_r($trace[0]['line']);
	}

	$facebook->destroySession();
?>
	</body>
</html>