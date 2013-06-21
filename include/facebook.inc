<?php
	require_once(BASE_DIR . "/_ext/facebook-php-sdk/src/facebook.php");

	function facebook_authenticate() {
		$facebook = new Facebook(array(
			'appId'  => FACEBOOK_APP_ID,
			'secret' => FACEBOOK_SECRET_KEY,
		));

		$user = $facebook->getUser();
		if ($user) {
			try {
				// Proceed knowing you have a logged in user who's authenticated.
				$user_profile = $facebook->api('/me?scope=email');
			} catch (FacebookApiException $e) {
				error_log($e);
				$user = null;
			}
		}

		if ($user == null) {
			$params = array(
				'scope' => 'email',
				'redirect_uri' => FACEBOOK_REDIRECT_URI.'?action=auto_authenticate&techno=facebook',
			);
			$loginUrl = $facebook->getLoginUrl($params);
			redirect_to($loginUrl);
		}

		debug("user_profile=".sprint_r($user_profile));
		$email = $user_profile['email'];
		$user = User::get_from_email($email);

		if ($user == NULL) {
			$firstname = '';
			$lastname = '';
			$locale = $_SESSION["locale"];
			if (isset($user_profile['first_name'])) {
				$firstname = $user_profile['first_name'];
			}
			if (isset($user_profile['last_name'])) {
				$lastname = $user_profile['last_name'];
			}
			if (isset($user_profile['locale'])) {
				$locale = $user_profile['locale'];
			}
			$user = User::create_from_partner($email, $lastname, $firstname, $locale);
		}
		$facebook->destroySession();
		return $user;
	}

	function facebook_jscript_reference() {
		$host = HOST;
		$appid = FACEBOOK_APP_ID;
		$content = <<<EOF
<!-- FACEBOOK START -->
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    // init the FB JS SDK
    FB.init({
      appId      : ${appid},
      channelUrl : '${host}/_ext/facebook-php-sdk/channel.html',
      status     : true,
      xfbml      : true
    });
  };

  // Load the SDK asynchronously
  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/all.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
<!-- FACEBOOK END -->
EOF;
		return $content;
	}

	function facebook_comment($url = '') {
		$nbr_displayed_post = 10;
		$width = 680;
		$href = $url;
		$content = <<<EOF
<div class="fb-comments" data-href="${href}" data-width="${width}" data-num-posts="${nbr_displayed_post}"></div>
EOF;
		return $content;
	}

	function facebook_create_event($event) {
		$b_success = false;
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
		$event_id = $event->id;
		$event_name = $event->title;
		$event_start = $event->happening_t.'T19:00:00-0700';    // We'll just start the event now.
		$event_privacy = "SECRET"; // We'll make it secret so we don't annoy folks.

		if (!$user_id) {
			$params = array(
				'scope' => 'create_event, rsvp_event',
				//'redirect_uri' => $event->get_url(),
			);
			$loginUrl = $facebook->getLoginUrl($params);
			redirect_to($loginUrl);
			//throw new Exception('Not logged to Facebook');
		}

		try {
			debug('user_id='.$user_id);
			$ret_obj = $facebook->api($user_id.'/events', 'POST', array(
				'name' => $event_name,
				'start_time' => $event_start,
				'privacy_type' => $event_privacy,
				'description'=> format_html2text($event->short_description),
				'location' => $event->location,
			));
			debug('ret_obj='.$ret_obj['id']);
			$b_success = isset($ret_obj['id']);
			if ($b_success) {
				$event->facebook_event_id = $ret_obj['id'];
				$event->add_flag(EVENT_FLAG_FACEBOOK_EVENT_CREATED);
				$event->update();
			}
		} catch(FacebookApiException $e) {
			$params = array(
				'scope' => 'create_event, rsvp_event',
				'redirect_uri' => $event->get_url(),
			);
			$loginUrl = $facebook->getLoginUrl($params);
			redirect_to($loginUrl);
		}
		$facebook->destroySession();
		return $b_success;
	}
?>