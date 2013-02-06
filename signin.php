<?php
	require_once("include/database.inc");
	
	$error_msg = "";
	if (isset($_POST['login']) && isset($_POST['password'])) {
		if (authenticate($_POST['login'], $_POST['password'])) {
			$_SESSION['login'] = $_POST['login'];
			redirect_to("index.php");
		} else {
			$error_msg = "Wrong login or password";
		}
	}
?>
<html>
<head>
	<title>Sign in</title>
</head>
	<?php
		echo $error_msg;
	?>
	<form name="input" action="signin.php" method="POST">
		<table>
		<tr>
			<td>Login: </td>
			<td><input type="text" name="login" value="admin"></td>
		</tr>
		<tr>
			<td>Password: </td>
			<td><input type="password" name="password" value="toto"></td>
		</tr>
		<tr>
			<td><input type="submit" value="Sign in"></td>
		<tr/>
		</table>		
	</form>
</html>