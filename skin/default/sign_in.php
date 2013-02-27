<?php
	require_once("include/user.inc");
?>
<html>
<head>
	<title>Sign in</title>
</head>
	<a href="index.php">Go back to index</a><br/><br/>
	<span style="color:red;">
	<?php
		echo $g_error_msg;
	?>
	</span><br/>
	<form name="input" action="?action=authenticate" method="POST">
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