<?php
	$bill = $g_display['bill'];
	$bitcoin_uri = '';
	try {
		$bitcoin_uri = bitcoin_uri($bill->total_ttc, $bill->label);
?>
<a class="evt_bitcoin_button" href="?action=bitcoin_payment&amp;id=<?php echo $bill->id; ?>">
	<div class="evt_bitcoin_btn_txt">{{Buy with}}</div>
	<img src="<?php echo SKIN_DIR; ?>/images/bitcoin_btn.png" />
</a>
<?php
	} catch (Exception $e) {
?>
{{Payment with bitcoin currently not available.}}
<?php
	}
?>