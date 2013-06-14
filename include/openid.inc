<?php
	require_once(BASE_DIR . '/_ext/lightopenid/openid.php');

	function openid_authenticate($url) {
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
	    	$b_validate = true;
	    	if (isset($_GET['url'])) {
				if ($_GET['url'] == 'http://openid.orange.fr') {
					$b_validate = false;
				}
	    	}
	    	if ($b_validate) {
	    		if (!$openid->validate()) {
	    			throw new Exception(_t('Connection not well validated.'));
	    		}
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
				$locale = $_SESSION['locale'];
				$user = User::create_from_partner($email, $lastname, $firstname, $locale);
			}

			return $user;
		}
	}
?>