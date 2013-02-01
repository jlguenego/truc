<?php
	require_once("include/constants.inc");
	require_once(SETTINGS_INI);
	require_once('include/install.inc');
	$error_msg = '';
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