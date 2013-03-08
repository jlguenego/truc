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
			<td><input type="text" name="login" value="<?php echo_default_value("login"); ?>"></td>
		</tr>
		<tr>
			<td>Firstname: </td>
			<td><input type="text" name="firstname" value="<?php echo_default_value("firstname"); ?>"></td>
		</tr>
		<tr>
			<td>Lastname: </td>
			<td><input type="text" name="lastname" value="<?php echo_default_value("lastname"); ?>"></td>
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
			<td><input type="email" name="email" value="<?php echo_default_value("email"); ?>"></td>
		</tr>
		<tr>
			<td>Full postal address: </td>
			<td><input type="text" name="address" placeholder="<numero><rue><code postal><ville><pays>"></td>
		</tr>
		<tr>
			<td colspan="2">
				<?php
					require_once('include/recaptcha.inc');
					$publickey = CAPTCHA_PUB_KEY; // you got this from the signup page
					echo recaptcha_get_html($publickey);
				?>
			</td>
		</tr>
		<tr>
			<td><input type="submit" value="Submit"></td>
		<tr/>
	</table>
</form>