<?php
	$bill = $_SESSION["bill"];
	$link = $bill->url();
	debug(sprint_r($bill));
	debug(sprint_r($_GET));
	debug(sprint_r($_POST));
	debug(sprint_r($_SESSION));
	$payment_html = "payment";
	if ($bill->status == BILL_STATUS_PLANNED) {
		$payment_html = "payment authorization";
	}
?>
<span class="evt_title"><p>{{Order successfully processed!}}</p></span>
{{You can access to this bill via this permalink:}} <a href="<?php echo $link; ?>" target="_blank"><?php echo $link; ?></a>.<br/>
<br/>
{{Back to the}} <a href="<?php echo "event/".$bill->event_id; ?>">{{event page}}</a>