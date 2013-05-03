<?php
	$devis = $_SESSION["devis"];
	$link = $devis->url();
	debug(sprint_r($devis));
	debug(sprint_r($_GET));
	debug(sprint_r($_POST));
	debug(sprint_r($_SESSION));
	$payment_html = "payment";
	if ($devis->status == DEVIS_STATUS_PLANNED) {
		$payment_html = "payment authorization";
	}
?>
<span class="evt_title"><p>{{Payment success!}}</p></span>
{{The <?php echo $payment_html; ?> succeeded. We just sent a mail to you for details.}}<br/>
<br/>
{{You can access to this quotation via this permalink:}} <a href="<?php echo $link; ?>" target="_blank"><?php echo $link; ?></a>.