<?php
	$bill = $g_display['bill'];
?>

<form action="payment/intern/execute_payment.php" method="get">
	<input type="hidden" name="item_name" value="<?php echo $bill->label; ?>">
	<input type="hidden" name="amount" value="<?php echo $bill->total_ttc; ?>">
	<input type="hidden" name="payment_type" value="authorization"/>
	<input type="hidden" name="currency_code" value="EUR">
	<input type="submit" value="Pay(authorization only)"/>
</form>
