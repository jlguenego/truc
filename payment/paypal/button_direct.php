<?php
	$devis = $g_display['devis'];
	$event = $g_display['event'];
?>

<form action="<?php echo PAYPAL_URL; ?>" method="post">
	<input type="hidden" name="cmd" value="_xclick">
	<input type="hidden" name="business" value="<?php echo PAYPAL_SELLER; ?>">
	<input type="hidden" name="lc" value="US">
	<input type="hidden" name="item_name" value="<?php echo $devis->label.": ".$event->title; ?>">
	<input type="hidden" name="amount" value="<?php echo $devis->total_ttc; ?>">
	<input type="hidden" name="currency_code" value="EUR">
	<input type="hidden" name="button_subtype" value="services">
	<input type="hidden" name="no_note" value="0">
	<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">
	<input type="image" src="https://www.paypalobjects.com/fr_XC/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
	<img alt="" border="0" src="https://www.paypalobjects.com/fr_XC/i/scr/pixel.gif" width="1" height="1">
</form>