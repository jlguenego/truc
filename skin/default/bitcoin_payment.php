<?php
	$bill = $g_display['bill'];
	$event = $g_display['event'];
	$bitcoin_uri = $g_display['bitcoin_uri'];
?>
<div class="evt_title"><p>{{Pay with bitcoins}}</p></div>
{{Event:}} <a href="?action=retrieve&amp;type=event&amp;id=<?php echo $event->id; ?>"><?php echo $event->title; ?></a>
<table width="100%">
	<tr>
		<td width="300">
			<h2>{{Order Information}}</h2>
			<b><?php echo $bill->total_ttc; ?></b> €<br/>
			(<?php echo eur2btc($bill->total_ttc); ?> BTC)<br/>
			<br/>
			<br/>
			<span class="help">{{Exchange rate:}} 1BTC = <?php echo bitcoin_get_curr(); ?> EUR</span>
		</td>
		<td rowspan="2" align="center" valign="top">
			<div class="evt_bitcoin_payment_info">
				{{Send payment to}}<br/>
				<a href="<?php echo $bitcoin_uri; ?>"><?php echo BITCOIN_ADDRESS; ?></a><br/>
				<br/>
				<img src="<?php echo bitcoin_generate_qrcode($bitcoin_uri); ?>" />
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<h2>{{Buyer Informations}}</h2>
			<?php echo $bill->client_name; ?><br/>
			<?php echo $bill->get_client_address(); ?><br/>
		</td>
	</tr>
</table>