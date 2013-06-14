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
		if (!TEST_MODE) {
			redirect_to("index.php");
		}
	}

	if (isset($_POST['remove_settings_only'])) {
			chmod(BASE_DIR . "/" . SETTINGS_INI, 0755);
		unlink(BASE_DIR . "/" . SETTINGS_INI);
?>
<html>
	<head>
		<title>Removed _settings.ini only done.</title>
	</head>
	<body>
		Removed _settings.ini only done.
		<a href="index.php">Go to index</a>
	</body>
<html>
<?php
	} else if (isset($_POST['confirm'])) {
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

	Remove _settings.ini (uninstall but let database intact)
	<form name="input" action="uninstall.php" method="POST">
		<input type="hidden" name="remove_settings_only" value="yes"/>
		<input type="submit" value="Submit"/>
	</form>
	<a href="support/save_db.php">Backup the database</a>
</html>
<?php
	}
?>