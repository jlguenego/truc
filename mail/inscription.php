<?php
	$user = $g_display["user"];
	$lien = HOST.'/?action=activation&amp;key='.$user->activation_key;
?>
<p>
	Dear <?php echo $user->get_name(); ?>,<br/>
	Your account has been created. However it has to be activated.<br/>
	Please click the link below to activate your account:<br/>
	<a href="<?php echo $lien; ?>"><?php echo $lien; ?></a>
</p>
