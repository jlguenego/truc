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
				'redirect_uri' => HOST.'?action=auto_authenticate&techno=facebook',
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
			$user = new User();
			$user->id = create_id();
			$user->email = $email;
			$user->firstname = $firstname;
			$user->lastname = $lastname;
			$user->password = 'facebook';
			$user->locale = $locale;
			$user->role = ROLE_USER;
			$user->activation_status = ACTIVATION_STATUS_ACTIVATED;
				$user->clean_format();
			$user->store();
		}
		$facebook->destroySession();
		return $user;
	}
?>