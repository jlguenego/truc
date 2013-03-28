<?php
	define("BASE_DIR", ".");

	require_once(BASE_DIR . "/include/constants.inc");
	require_once(BASE_DIR . "/include/globals.inc");
	require_once(BASE_DIR . "/include/misc.inc");
	require_once(BASE_DIR . "/include/user.inc");
	require_once(BASE_DIR . "/include/layout.inc");

	if (isset($_POST['login'])) {
		try {
			if (!check_mail($_POST["email"])) {
				throw new Exception("Invalid mail");
			}
			$user = new User();
			$user->id = create_id();
			$user->login = $_POST["login"];
			$user->set_password($_POST["password"]);
			$user->email = $_POST["email"];
			$user->lastname = ucfirst($_POST["lastname"]);
			$user->firstname = mb_strtoupper($_POST["password"], "UTF-8");
			$user->role = ROLE_ADMIN;
			$user->activation_status = ACTIVATION_STATUS_ACTIVATED;
			$user->generate_activation_key();
			$user->street = $_POST["street"];
			$user->zip = $_POST["zip"];
			$user->city = $_POST["city"];
			$user->country = $_POST["country"];
			$user->state = $_POST["state"];
			$user->store();
			seq_create('quotation', 1000);
			seq_create('invoice', 10000);
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
		echo $g_error_msg;
	?>
	Please enter the admin user info:
	<form name="input" action="deploy.php" method="POST">
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
			<td>Street: </td>
			<td><input type="text" name="street" value="16 rue de Juilly"></td>
		</tr>
		<tr>
			<td>Zip: </td>
			<td><input type="text" name="zip" value="77020"></td>
		</tr>
		<tr>
			<td>City: </td>
			<td><input type="text" name="city" value="Torcy"></td>
		</tr>
		<tr>
			<td>Country: </td>
			<td><input type="text" name="country" value="France"></td>
		</tr>
		<tr>
			<td>State: </td>
			<td><input type="text" name="state" value=""></td>
		</tr>
		<tr>
			<td><input type="submit" value="Submit"></td>
		</tr>
		</table>
	</form>
</html>
<?php
	}
	layout_trace();
?>