<?php
	global $g_display;
	$bill = $g_display["bill"];
	$event = $bill->get_event();
?>
<div class="evt_title inline"><p><?php echo format_participate_title($event); ?> - {{Confirmation}}</p></div>
<?php print_bill($bill, PRINT_BILL_CONTEXT_CONFIRMATION); ?>
<br/>
<table class="evt_payment">
	<tr>
		<td class="evt_pay" width="500"><?php payment_button(); ?></td>
		<td width="200">&nbsp;</td>
		<td align="right" class="evt_pay"><?php bitcoin_button(); ?></td>
</tr>
</table>