<?php
	global $g_display;
	$bill = $g_display["bill"];
	$event = $bill->get_event();
?>
<div class="evt_title inline"><p>{{Order tickets}} - {{Confirmation}}</p></div>
<?php print_bill($bill, PRINT_BILL_CONTEXT_CONFIRMATION); ?>
<br/>
<?php
	if ($bill->total_ttc > 0) {
?>
<table class="evt_payment">
	<tr>
		<td class="evt_pay" width="500"><?php payment_button(); ?></td>
		<td width="200">&nbsp;</td>
		<td align="right" class="evt_pay"><?php bitcoin_button(); ?></td>
	</tr>
</table>
<?php
	} else {
?>
<table class="evt_payment">
	<tr>
		<td align="center" class="evt_pay" width="500"><a href="?action=confirm_order&amp;id=<?php echo $bill->id; ?>" class="evt_button">{{Confirm your order!}}</a></td>
	</tr>
</table>
<?php
	}
?>