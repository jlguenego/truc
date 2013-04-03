<?php
	define("BASE_DIR", ".");

	require_once(BASE_DIR . '/include/install.inc');
	require_once(BASE_DIR . '/include/misc.inc');

	if (is_installed()) {
		redirect_to("index.php");
	}

	$error_msg = '';
	if (isset($_POST['login'])) {
		try {
			install();
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
	Please enter the database connection parameters:
	<form name="input" action="install.php" method="POST">
		<table>
		<tr>
			<td>Username: </td>
			<td><input type="text" name="login" value="root"></td>
		</tr>
		<tr>
			<td>Password: </td>
			<td><input type="password" name="password"></td>
		</tr>
		<tr>
			<td>Database Name: </td>
			<td><input type="text" name="dbname" value="jlgutilidb1"></td>
		</tr>
		<tr>
			<td>Hostname: </td>
			<td><input type="text" name="host" value="localhost"></td>
		</tr>
		<tr>
			<td>Contact mail: </td>
			<td><input type="email" name="contact_email" value="contact@jlg-utilities.com"></td>
		</tr>
		<tr>
			<td>Payment type:</td>
			<td>
				<select name="payment_type">
					<option value="paypal">Paypal</option>
					<option value="paypal_sandbox">Paypal Sandbox</option>
					<option value="intern">Intern</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="test_mode" value="true" checked/>Test Mode</td>
		</tr>
		<tr>
			<td><input type="submit" value="Submit"></td>
		</tr>
		</table>
	</form>
</html>
<?php
	}
?>