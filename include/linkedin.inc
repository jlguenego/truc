<?php
	define('REDIRECT_URI', '?action=auto_authenticate&techno=linkedin');
	define('SCOPE', 'r_fullprofile r_emailaddress rw_nus');

	function linkedin_authenticate() {
		if (isset($_GET['error'])) {
			// LinkedIn returned an error
			throw new Exception(_t('Connection failed: ').$_GET['error_description']);
		} elseif (isset($_GET['code'])) {
			// User authorized your application
			if ($_SESSION['linkedin_state'] == $_GET['state']) {
				// Get token so you can make API calls
				getAccessToken();
			} else {
				throw new Exception('Error while connecting.');
			}
		} else {
			if ((empty($_SESSION['expires_at'])) || (time() > $_SESSION['expires_at'])) {
			// Token has expired, clear the state
			unset($_SESSION['linkedin_state']);
			//$_SESSION = array();
		}
			if (empty($_SESSION['access_token'])) {
				// Start authorization process
				getAuthorizationCode();
			}
		}
		$user_profile = fetch('GET', '/v1/people/~:(firstName,lastName,email-address)');
		$email = $user_profile->emailAddress;
		$user = User::get_from_email($email);

		if ($user == NULL) {
			$firstname = '';
			$lastname = '';
			if (isset($user_profile->firstName)) {
				$firstname = $user_profile->firstName;
			}
			if (isset($user_profile->lastName)) {
				$lastname = $user_profile->lastName;
			}
			$user = new User();
			$user->id = create_id();
			$user->email = $email;
			$user->firstname = $firstname;
			$user->lastname = $lastname;
			$user->password = 'linkedin';
			$user->locale = $_SESSION["locale"];
			$user->role = ROLE_USER;
			$user->activation_status = ACTIVATION_STATUS_ACTIVATED;
			$user->clean_format();
			$user->store();
		}
		unset($_SESSION['access_token']);
		return $user;
	}

	function getAuthorizationCode() {
	    $params = array(
			'response_type' => 'code',
			'client_id' => LINKEDIN_API_KEY,
			'scope' => SCOPE,
			'state' => uniqid('', true), // unique long string
			'redirect_uri' => HOST.REDIRECT_URI,
    	);

	    // Authentication request
	    $url = 'https://www.linkedin.com/uas/oauth2/authorization?' . http_build_query($params);

	    // Needed to identify request when it returns to us
	    $_SESSION['linkedin_state'] = $params['state'];

	    // Redirect user to authenticate
	    redirect_to($url);
	}

function getAccessToken() {
    $params = array(
		'grant_type' => 'authorization_code',
		'client_id' => LINKEDIN_API_KEY,
		'client_secret' => LINKEDIN_API_SECRET,
		'code' => $_GET['code'],
		'redirect_uri' => HOST.REDIRECT_URI
	);

    // Access Token request
    $url = 'https://www.linkedin.com/uas/oauth2/accessToken?' . http_build_query($params);

    // Tell streams to make a POST request
    $context = stream_context_create(
                    array('http' =>
                        array('method' => 'POST',
                        )
                    )
                );

    // Retrieve access token information
    $response = file_get_contents($url, false, $context);

    // Native PHP object, please
    $token = json_decode($response);

    // Store access token and expiration time
    $_SESSION['access_token'] = $token->access_token; // guard this!
    $_SESSION['expires_in']   = $token->expires_in; // relative time (in seconds)
    $_SESSION['expires_at']   = time() + $_SESSION['expires_in']; // absolute time

    return true;
}

function fetch($method, $resource, $body = '') {
    $params = array('oauth2_access_token' => $_SESSION['access_token'],
                    'format' => 'json',
              );

    // Need to use HTTPS
    $url = 'https://api.linkedin.com' . $resource . '?' . http_build_query($params);
    // Tell streams to make a (GET, POST, PUT, or DELETE) request
    $context = stream_context_create(
                    array('http' =>
                        array('method' => $method,
                        )
                    )
                );


    // Hocus Pocus
    $response = file_get_contents($url, false, $context);

    // Native PHP object, please
    return json_decode($response);
}
?>