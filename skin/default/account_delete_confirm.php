<?php
	$user = $g_display["user"];
?>
Are you sure you want to delete your account?
<form name="input" action="?action=delete&amp;type=account" method="POST">
	<input type="hidden" name="confirm" value="yes"/>
	<input type="hidden" name="id" value="<?php echo $user->id; ?>"/>
	<input type="submit" value="Yes"/>
</form>
<form name="input" action="?action=retrieve&amp;type=account&amp;id=<?php echo $user->id; ?>" method="POST">
	<input type="hidden" name="confirm" value="no"/>
	<input type="submit" value="NO"/>
</form>