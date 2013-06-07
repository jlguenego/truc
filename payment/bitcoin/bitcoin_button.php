<?php
	$devis = $g_display['devis'];
	$bitcoin_uri = '';
	try {
		$bitcoin_uri = bitcoin_uri($devis->total_ttc, $devis->label);
?>
<a class="evt_bitcoin_button" href="?action=bitcoin_payment&amp;id=<?php echo $devis->id; ?>">
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