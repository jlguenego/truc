<?php
	require_once('include/install.inc');
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
			<td><input type="text" name="login" value="jlgutilidb1"></td>
		</tr>
		<tr>
			<td>Password: </td>
			<td><input type="password" name="jlgyt12"></td>
		</tr>
		<tr>
			<td>Database Name: </td>
			<td><input type="text" name="dbname" value="jlgutilidb1"></td>
		</tr>
		<tr>
			<td>Hostname: </td>
			<td><input type="text" name="host" value="mysql5-7.start"></td>
		</tr>
		<tr>
			<td>Contact mail: </td>
			<td><input type="email" name="contact_email" value="contact@jlg-utilities.com"></td>
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