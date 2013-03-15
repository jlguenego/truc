<?php
	$lien = $_GET["host"].'/?action=activation&amp;key='.$_GET["key"]
?>
<p>
	Dear <?php echo $_GET["login"]; ?>,<br/>
	Thanks for subscribe. Your are almost ready to create or participate to events.<br/>
	Please click the link below to activate your account:<br/>
	<a href="<?php echo $lien; ?>"><?php echo $lien; ?></a>
</p>