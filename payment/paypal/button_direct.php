<?php
	$bill = $g_display['bill'];
	$event = $g_display['event'];
?>

<form action="<?php echo PAYPAL_URL; ?>" method="post">
	<input type="hidden" name="cmd" value="_xclick">
	<input type="hidden" name="business" value="<?php echo PAYPAL_SELLER; ?>">
	<input type="hidden" name="lc" value="US">
	<input type="hidden" name="item_name" value="<?php echo $bill->label.": ".$event->title; ?>">
	<input type="hidden" name="amount" value="<?php echo $bill->total_ttc; ?>">
	<input type="hidden" name="currency_code" value="EUR">
	<input type="hidden" name="button_subtype" value="services">
	<input type="hidden" name="no_note" value="0">
	<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">
	<input type="image" src="https://www.paypalobjects.com/fr_XC/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
	<img alt="" border="0" src="https://www.paypalobjects.com/fr_XC/i/scr/pixel.gif" width="1" height="1"><br/>
	<a href="#" title="{{PayPal How does it work?}}" onclick="javascript:window.open('https://www.paypal.com/webapps/mpp/paypal-popup','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=600');"><img src="https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_moyens_paiement_fr.jpg" border="0" alt="PayPal Acceptance Mark"></a>
</form>