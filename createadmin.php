<?php
	require_once("include/constants.inc");
	require_once("include/globals.inc");
	require_once("include/misc.inc");
	require_once("include/user.inc");
		
	$error_msg = '';
	if (isset($_POST['login'])) {
		if (check_mail($_POST["email"])) {
			println("Valid mail");
		} else {
			println("invalid mail");
		}
		try {
			add_user($_POST['name'], $_POST['lastname'], $_POST['login'], $_POST['password'], $_POST['email'], TRUE);
			$install_done = <<<EOF
<html>
	<head>
		<title>Install Done</title>
	</head>
	<body>
		<a href="index.php">Go to index</a>
	</body>
<html>
EOF;
			println($install_done);
		} catch (Exception $e) {
			println("Install failed: " . $e->getMessage());
		}
	} else {
?>
<html>
<head>
	<title>Installer</title>
</head>
	<?php
		echo $error_msg;
	?>
	Please enter the admin user info:
	<form name="input" action="createadmin.php" method="POST">
		<table>
		<tr>
			<td>Login: </td>
			<td><input type="text" name="login" value="admin"></td>
		</tr>
		<tr>
			<td>Name: </td>
			<td><input type="text" name="name" value="yannis"></td>
		</tr>
		<tr>
			<td>Lastname: </td>
			<td><input type="text" name="lastname" value="thomias"></td>
		</tr>
		<tr>
			<td>Password: </td>
			<td><input type="password" name="password" value="toto"></td>
		</tr>
		<tr>
			<td>Retype Password: </td>
			<td><input type="password" name="password2" value="toto"></td>
		</tr>
		<tr>
			<td>Mail: </td>
			<td><input type="email" name="email" value="toto@toto.fr"></td>
		</tr>
		<tr>
			<td><input type="submit" value="Submit"></td>
		<tr/>
		</table>		
	</form>
</html>
<?php
	}
?>