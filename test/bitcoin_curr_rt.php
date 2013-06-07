<?php
$currency = "EUR";
// get the new price from bitcoin charts
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://bitcoincharts.com/t/weighted_prices.json');
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
$file_contents = strstr($result,'{'); // get everything starting from first curly bracket
curl_close($ch);
if ($file_contents) {
	$currency_data = json_decode($file_contents, TRUE);
	$price = $currency_data[$currency]['24h']; // get the 24h-average price
} else {
	echo('Error getting json file');
}

$price_formatted = number_format($price,2,'.'); // german number format with 4 digits after the decimal point
echo "$price_formatted $currency";
?>