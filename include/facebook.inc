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
		$href = HOST.$url;
		$content = <<<EOF
<div class="fb-comments" data-href="${href}" data-width="${width}" data-num-posts="${nbr_displayed_post}"></div>
EOF;
		return $content;
	}
?>