<?php
	$devis = $_SESSION["devis"];
	$link = $devis->url();
	debug(sprint_r($devis));
?>
The payment/authorization succeed. We just sent a mail to you for details.<br/>
You can access to your devis with this permalink: <a href="<?php echo $link; ?>"><?php echo $link; ?></a>.