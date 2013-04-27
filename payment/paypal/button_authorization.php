<?php
	global $g_display;
	$devis = $g_display["devis"];
	$event = $g_display["event"];
	// TODO: image authorization only
?>

<b>Note: This event is not yet confirmed.</b><br/>
Your payment authorization will not be captured until the event is confirmed.<br/>
If the event is cancelled, your authorization payment will be cancelled as well.<br/>
In both case of confirmation or cancellation, you will be noticed by email.<br/>
This event will be confirmed or cancelled before the <?php echo date("d F Y", s2t($event->get_confirmation_date())); ?>.<br/>
<br/>
<br/>

<form action="<?php echo PAYPAL_URL; ?>" method="post">
	<input type="hidden" name="cmd" value="_xclick">
	<input type="hidden" name="business" value="<?php echo PAYPAL_SELLER; ?>">
	<input type="hidden" name="lc" value="US">
	<input type="hidden" name="item_name" value="<?php echo $devis->label.": ".$event->title; ?>">
	<input type="hidden" name="amount" value="<?php echo $devis->total_ttc; ?>">
	<input type="hidden" name="currency_code" value="EUR">
	<input type="hidden" name="button_subtype" value="services">
	<input type="hidden" name="no_note" value="0">
	<input type="hidden" name="paymentaction" value="authorization">
	<input type="hidden" name="notify_url" value="<?php echo HOST."/payment/paypal/ipn.php"; ?>">
	<input type="hidden" name="return" value="<?php echo HOST."/index.php?action=payment_success"; ?>">
	<input type="hidden" name="cancel_return" value="<?php echo HOST."/index.php?action=payment_cancel"; ?>">
	<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">
	<input type="image" src="<?php echo HOST . "/payment/paypal/authorize.png"; ?>" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
	<img alt="" border="0" src="https://www.paypalobjects.com/fr_XC/i/scr/pixel.gif" width="1" height="1">
</form>
<!-- PayPal Logo --><table border="0" cellpadding="10" cellspacing="0" align="center"><tr><td align="center"></td></tr><tr><td align="center"><a href="#" title="PayPal Comment Ca Marche" onclick="javascript:window.open('https://www.paypal.com/webapps/mpp/paypal-popup','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=600');"><img src="https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_moyens_paiement_fr.jpg" border="0" alt="PayPal Acceptance Mark"></a></td></tr></table><!-- PayPal Logo -->
