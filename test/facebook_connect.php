<?php
	require_once("../_ext/facebook-php-sdk/src/facebook.php");

	$config = array();
	$config['appId'] = '562698780446843';
	$config['secret'] = '722704d72aac65765804f63bd1e12013';
	$config['fileUpload'] = true;

	$facebook = new Facebook($config);
	$user = $facebook->getUser();

	if ($user) {
		try {
		} catch (FacebookApiException $e) {
			error_log($e);
			$user = null;
		}
	}

	if ($user) {
		$logoutUrl  = $facebook->getLogoutUrl();
	} else {
		$loginUrl  = $facebook->getLoginUrl(array(
			'scope' => 'email',
		));
	}

	$profile = $facebook->api('/me?scope=email');

	$params = array(
		'ok_session' => 'A',
		'no_user' => 'B',
		'no_session' => 'C',
	);


	$url_is_log = $facebook->getLoginStatusUrl($params);
?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
	<head>
		<title>php-sdk</title>
		<style>
			body {
				font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
			}
			h1 a {
				text-decoration: none;
				color: #3b5998;
			}
			h1 a:hover {
				text-decoration: underline;
			}
		</style>
	</head>
	<body>
		<a href="<?php echo $url_is_log; ?>"><?php echo $url_is_log; ?></a>
		<h1>php-sdk</h1>
<?php
	if ($user) {
?>
		<a href="<?php echo $logoutUrl; ?>">Logout</a>
<?php
	} else {
?>
		<div>
			Login using OAuth 2.0 handled by the PHP SDK:
			<a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
		</div>
<?php
	}
?>

		<h3>PHP Session</h3>
		<pre><?php print_r($_SESSION); ?></pre>

<?php
	if ($user) {
?>
		<h3>You</h3>
		<img src="https://graph.facebook.com/<?php echo $user; ?>/picture">

		<h3>Your User Object (/me?scope=email)</h3>
		<pre><?php print_r($profile); ?></pre>
<?php
	} else {
?>
		<strong><em>You are not Connected.</em></strong>
<?php
	}
?>
		<h3>Public profile</h3>
		<img src="https://graph.facebook.com/naitik/picture">
		<?php echo $profile['name']; ?>
	</body>
</html>
