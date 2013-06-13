<?php
	require_once(BASE_DIR . '/_ext/lightopenid/openid.php');

	function openid_authenticate($url) {
		global $g_error_msg;

		$openid = new LightOpenID(HOST);
	    if(!$openid->mode) {
	    	debug('No mode');
            $openid->identity = $url;
            # The following two lines request email, full name, and a nickname
            # from the provider. Remove them if you don't need that data.
            $openid->required = array(
            	'contact/email',
            	'namePerson/first',
            	'namePerson/last',
            );
            $openid->optional = array('namePerson', 'namePerson/friendly');
            redirect_to($openid->authUrl());
	    } elseif($openid->mode == 'cancel') {
	    	debug('cancel');
			throw new Exception(_t('You have cancelled authentication!'));
	    } else {
	    	debug('connection ok');
	    	if ((!$_GET['url'] != 'http://openid.orange.fr') && (!$openid->validate())) {
	    		throw new Exception(_t('Connection not well validated.'));
	    	}
	    	debug('connection valid');
			$attributes = $openid->getAttributes();
			$email = $attributes['contact/email'];
			$user = User::get_from_email($email);

			if ($user == NULL) {
				$firstname = '';
				$lastname = '';
				if (isset($attributes['namePerson/first'])) {
					$firstname = $attributes['namePerson/first'];
				}
				if (isset($attributes['namePerson/last'])) {
					$lastname = $attributes['namePerson/last'];
				}
				$user = new User();
				$user->id = create_id();
				$user->email = $email;
				$user->firstname = $firstname;
				$user->lastname = $lastname;
				$user->password = 'openid';
				$user->locale = $_SESSION["locale"];
				$user->role = ROLE_USER;
				$user->activation_status = ACTIVATION_STATUS_ACTIVATED;
				$user->clean_format();
				$user->store();
			}

			return $user;
		}
	}
?>