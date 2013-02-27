<html>
<head>
	<title>Register</title>
</head>
<?php layout_header(); ?>
	<a href="index.php">Back to index</a><br/><br/>
	<span style="color:red;">
	<?php
		echo $g_error_msg;
	?>
	</span><br/>
	Please enter your info:
	<form name="input" action="?action=create&amp;type=account" method="POST">
		<table>
		<tr>
			<td>Login: </td>
			<td><input type="text" name="login" value=""></td>
		</tr>
		<tr>
			<td>Firstname: </td>
			<td><input type="text" name="firstname" value=""></td>
		</tr>
		<tr>
			<td>Lastname: </td>
			<td><input type="text" name="lastname" value=""></td>
		</tr>
		<tr>
			<td>Password: </td>
			<td><input type="password" name="password" value=""></td>
		</tr>
		<tr>
			<td>Retype Password: </td>
			<td><input type="password" name="password2" value=""></td>
		</tr>
		<tr>
			<td>Mail: </td>
			<td><input type="email" name="email" value=""></td>
		</tr>
		<tr>
			<td><input type="submit" value="Submit"></td>
		<tr/>
		</table>		
	</form>
<?php layout_footer(); ?>
</html>