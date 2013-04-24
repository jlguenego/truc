<?php
	$user = $g_display["user"];
	$lien = HOST.'/?action=activation&amp;key='.$user->activation_key;
?>
<p>
	Chèr(e) <?php echo $user->get_name(); ?>,<br/>
	Votre compte est créé, mais a besoin d'être activé pour être utilisé.<br/>
	Veuillez cliquer sur le lien ci-dessous pour activer votre compte :<br/>
	<a href="<?php echo $lien; ?>"><?php echo $lien; ?></a>
</p>
