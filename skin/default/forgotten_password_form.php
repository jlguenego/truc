<form name="input" action="?action=handle_forgotten_password" method="POST">
	<table>
		<tr>
			<td>E-mail: </td>
			<td><input type="email" name="email" value="<?php echo_default_value("email"); ?>"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="Continue"></td>
		</tr>
	</table>
</form>