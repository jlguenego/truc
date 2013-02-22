<?php
	require_once('include/install.inc');
	require_once('include/user.inc');
	
	$error_msg = "";
	$user = array();
	if (isset($_SESSION['login'])) {
		$user = get_user_by_login($_SESSION['login']);
	} else {
		$error_msg .= "You have to log in<br/>";
	}
	if (isset($_POST['password'])) {
		if ($user['password'] != hash_pass($user['id'], $_SESSION['login'], $_POST['password'])) {
			$error_msg .= "Wrong password<br/>";
		}
		if ($_POST["email"] != "") {
			if (!check_mail($_POST["email"])) {
				$error_msg .= "Invalid mail<br/>";
			}
			if (used_mail($_POST["email"])) {
				$error_msg .= "This mail is already used<br/>";
			}
			$user['email'] = $_POST['email'];
		}
		if ($_POST['new_pass'] != "" || $_POST['new_pass2'] != "") {
			if ($_POST['new_pass'] != $_POST['new_pass2']) {
				$error_msg .= "Passwords are different<br/>";
			}
			$user['password'] = $_POST['new_pass'];
		} else {
			$user['password'] = $_POST['password'];
		}
		if ($error_msg == "") {
			update_user(
				$user['id'],
				$user['password'], 
				$user['email']);
			$error_msg = 'ok';
		}
	}
	if ($error_msg != 'ok') {
?>
<html>
<head>
	<title>Edit your account</title>
</head>
	<a href="index.php">Back to index</a><br/><br/>
	<?php
		echo $error_msg;
	?>
	Please enter your info:
	<form name="input" action="?action=update&amp;type=account" method="POST">
		<table>
		<tr>
			<td>New Password: </td>
			<td><input type="password" name="new_pass"></td>
		</tr>
		<tr>
			<td>Retype Password: </td>
			<td><input type="password" name="new_pass2"></td>
		</tr>
		<tr>
			<td>Mail: </td>
			<td><input type="email" name="email" value="<?php echo $user['email']; ?>"></td>
		</tr>
		<tr></tr>
		<tr>
			<td>Password: </td>
			<td><input type="password" name="password" value="toto"></td>
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
	<title>Profil edited</title>
</head>
	Profil correctly edited.<br/>
	<a href="?action=retrieve&amp;type=account">Back to your profile</a>
</html>
<?php
	}
?>