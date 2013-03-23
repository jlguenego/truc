<?php
	$devis = $_SESSION["devis"];
	$link = $devis->url();
	debug(sprint_r($devis));
	$payment_html = "payment";
	if ($devis->status == DEVIS_STATUS_PLANNED) {
		$payment_html = "payment authorization";
	}
?>
The <?php echo $payment_html; ?> succeed. We just sent a mail to you for details.<br/>
<br/>
You can access to your devis with this permalink: <a href="<?php echo $link; ?>" target="_blank"><?php echo $link; ?></a>.