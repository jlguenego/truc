<?php
	$user = $g_display["user"];
	$token = $g_display["token"];
	$link = HOST."/index.php?action=get_form&amp;type=reset_password&amp;permission_token=".$token;
?>
<p>
	Dear <?php echo $user->get_name(); ?>,<br/>
	A request has been made to reset your password on the web site <a href="<?php echo HOST; ?>"><?php echo HOST; ?></a>.<br/>
	Here is a link to reset your password:<br/>
	<a href="<?php echo $link; ?>"><?php echo $link; ?></a><br/>
	If you never asked for this, please just ignore this email.
</p>
