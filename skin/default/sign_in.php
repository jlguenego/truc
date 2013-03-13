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
		<td><input type="text" name="login" value="<?php echo_default_value("login"); ?>"></td>
	</tr>
	<tr>
		<td>Password: </td>
		<td><input type="password" name="password" value=""></td>
	</tr>
	<tr>
		<td><a href="?action=get_form&type=account">Create an account</a></td>
		<td><input type="submit" value="Sign in"></td>
	</tr>
	</table>
</form>