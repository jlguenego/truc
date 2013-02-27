<?php
	require_once('include/user.inc');
	
	$error_msg = "";
	if (isset($_POST['login'])) {
		foreach ($_POST as $value) {
			if ($value == "") {
				$error_msg .= "Please fill all fields<br/>";
				break;
			}
		}
		if (!check_mail($_POST["email"])) {
			$error_msg .= "Invalid mail<br/>";
		}
		if (used_mail($_POST["email"])) {
			$error_msg .= "This mail is already used<br/>";
		} 
		if (user_exists($_POST['login'])) {
			$error_msg .= "User already exists<br/>";
		}
		if ($_POST['password'] != $_POST['password2']) {
			$error_msg .= "Passwords are different<br/>";
		}
		if ($error_msg == "") {
			add_user(
				$_POST['name'], 
				$_POST['lastname'], 
				$_POST['login'],
				$_POST['password'], 
				$_POST['email'], 
				0);
			$error_msg = 'ok';
		}
	}
	if ($error_msg != 'ok') {
?>
<html>
<head>
	<title>Register</title>
</head>
	<a href="index.php">Back to index</a><br/><br/>
	<?php
		echo $error_msg;
	?>
	Please enter your info:
	<form name="input" action="?action=create&amp;type=account" method="POST">
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
	} else {
?>
<html>
<head>
	<title>Register</title>
</head>
	Registration OK. <a href="index.php">Back to index</a>
</html>
<?php
	}
?>