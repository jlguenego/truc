<?php
	function bitcoin_button() {
		global $g_display;
		require_once(BASE_DIR . '/payment/bitcoin/bitcoin.php');
	}

	function bitcoin_uri($amount, $bill_label) {
		$uri = "bitcoin:" . BITCOIN_WALLET . "?";
		$amount = 'amount=' . eur2btc($amount);
		$label = '&amp;label=' . str_replace(' ', '+', COMPANY_NAME) . '|' . $bill_label;
		return $uri . $amount . $label;
	}

	function eur2btc($amount) {
		return number_format($amount/ 94, 8);
	}
?>