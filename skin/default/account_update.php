<?php
	$user = $g_display["user"];
?>
<a href="index.php">Back to index</a><br/><br/>
<span style="color:red;">
	<?php echo "$g_error_msg<br/>"; ?>
</span>
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
		<td><input type="password" name="password" value=""></td>
	</tr>
	<tr>
		<td><input type="submit" value="Submit"></td>
	<tr/>
	</table>		
</form>