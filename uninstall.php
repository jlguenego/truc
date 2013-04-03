<?php
	session_start();
	define("BASE_DIR", ".");

	require_once(BASE_DIR . "/include/constants.inc");
	require_once(BASE_DIR . "/" . SETTINGS_INI);
	require_once(BASE_DIR . "/include/globals.inc");
	require_once(BASE_DIR . '/include/install.inc');
	require_once(BASE_DIR . '/include/authentication.inc');
	$error_msg = '';

	if (!is_admin_logged()) {
		redirect_to("index.php");
	}

	if (isset($_POST['confirm'])) {
		try {
			uninstall();
			$uninstall_done = <<<EOF
<html>
	<head>
		<title>Uninstall Done</title>
	</head>
	<body>
		<a href="index.php">Go to index</a>
	</body>
<html>
EOF;
			println($uninstall_done);
		} catch (Exception $e) {
			println("Uninstall failed: " . $e->getMessage());
		}
	} else {
?>
<html>
<head>
	<title>Uninstaller</title>
</head>
	Are you sure you want to uninstall all?
	<form name="input" action="uninstall.php" method="POST">
		<input type="hidden" name="confirm" value="yes"/>
		<input type="submit" value="Submit"/>
	</form>
</html>
<?php
	}
?>