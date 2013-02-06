<?php
	require_once("include/constants.inc");
	require_once("include/misc.inc");
	
	// If not installed, goto installation page
	if (!is_installed()) {
		redirect_to("install.php");
	}
	
	// If installed
	require_once(SETTINGS_INI);
	
	// Verify if user is logged in to show differents menu.
	if (!is_logged()) {
		echo "<a href=\"signin.php\">Sign in</a>";
	} else {
?>
<html>
	<head>
		<title>Index</title>
	</head>
	You are logged.<br/>
	<a href="createevent.php">Create an event<a/><br/>
	<a href="signout.php">Sign out<a/>
</html>
<?php
	}
?>