<?php
	$user = $g_display["user"];
?>
<p>
	Dear <?php echo $user->get_name(); ?>, <br/>
	Your account has been updated and your password has been changed.<br/>
	If it is not you, please contact our support: <?php echo CONTACT_MAIL ?>
</p>