<?php
	$user = $g_display["user"];
	$token = $g_display["token"];
	$link = HOST."/index.php?action=get_form&amp;type=reset_password&amp;permission_token=".$token;
?>
<p>
	Chèr(e) <?php echo $user->get_name(); ?>,<br/>
	Une demande de réinitialisation de votre mot de passe a été faite sur notre site <a href="<?php echo HOST; ?>"><?php echo HOST; ?></a>.<br/>
	Voici un lien pour réinitialiser votre mot de passe :<br/>
	<a href="<?php echo $link; ?>"><?php echo $link; ?></a><br/>
	Si vous n'avez pas demandé de réinitialisation, veuillez ignorer ce mail.
</p>
