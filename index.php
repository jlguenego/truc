<?php
	require_once("include/constants.inc");
	require_once("include/misc.inc");
	require_once("include/user.inc");
	
	// If not installed, goto installation page
	if (!is_installed()) {
		redirect_to("install.php");
	}
	
	// If installed
	require_once(SETTINGS_INI);
?>
<html>
	<head>
		<title>Index</title>
	</head>
<?php
	// Verify if user is logged in to show differents menu.
	if (!is_logged()) {
		echo "<a href=\"signin.php\">Sign in</a><br/>";
		echo "<a href=\"register.php\">Register</a><br/>";
		echo "<a href=\"event.php\">See events<a/>";
	} else {
		$user = get_user_by_login($_SESSION['login']);
		$id = $user['id'];
		echo "<a href=\"profil.php?id=$id\">Profil<a/><br/>";
?>
	<a href="createevent.php">Create an event<a/><br/>
	<a href="event.php">See events<a/><br/>
	<a href="signout.php">Sign out<a/>
</html>
<?php
	}
?>