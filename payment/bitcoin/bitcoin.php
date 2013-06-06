<?php
	$devis = $g_display['devis'];
?>
{{If you prefer, you can pay with BitCoin:}}
<a href="<?php echo bitcoin_uri($devis->total_ttc, $devis->label); ?>">
	<?php echo BITCOIN_WALLET; ?>
</a>