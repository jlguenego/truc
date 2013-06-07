<?php
	require_once(BASE_DIR . "/_ext/phpqrcode/qrlib.php");

	function bitcoin_button() {
		global $g_display;
		require_once(BASE_DIR . '/payment/bitcoin/bitcoin_button.php');
	}

	function bitcoin_uri($amount, $bill_label) {
		$uri = "bitcoin:" . BITCOIN_ADDRESS . "?";
		$amount = 'amount=' . eur2btc($amount);
		$label = '&amp;label=' . str_replace(' ', '+', COMPANY_NAME) . '|' . $bill_label;
		return $uri . $amount . $label;
	}

	function eur2btc($amount) {
		return number_format($amount / bitcoin_get_curr(), 8);
	}

	function bitcoin_get_curr() {
		$result = '';
		if (!file_exists(BITCOIN_FILENAME)) {
			$result = bitcoin_get_curr_from_website();
			file_put_contents(BITCOIN_FILENAME, $result);
		}
		$last_mod = filemtime(BITCOIN_FILENAME);
		if ((time() - $last_mod) > 3600) {
			try {
				$result = bitcoin_get_curr_from_website();
				file_put_contents(BITCOIN_FILENAME, $result);
			} catch (Exception $e) {
			}
		}
		$result = file_get_contents(BITCOIN_FILENAME);
		return trim($result);
	}

	function bitcoin_get_curr_from_website() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://bitcoincharts.com/t/weighted_prices.json');
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		$file_contents = strstr($result,'{'); // get everything starting from first curly bracket
		curl_close($ch);
		if ($file_contents) {
			$currency_data = json_decode($file_contents, TRUE);
			$price = $currency_data['EUR']['24h']; // get the 24h-average price
		} else {
			throw new Exception('Error getting json file');
		}

		$price_formatted = number_format($price,2,'.',''); // german number format with 4 digits after the decimal point

		return $price_formatted;
	}

	function bitcoin_generate_qrcode($bitcoin_uri) {
		if (!is_dir(QRCODE_CACHE_DIR)) {
			mkdir(QRCODE_CACHE_DIR, 0777, true);
		}
		$filename = QRCODE_CACHE_DIR.'/'.md5($bitcoin_uri).'.png';
		QRcode::png($bitcoin_uri, $filename, 'H', 5, 2);
		return $filename;
	}
?>