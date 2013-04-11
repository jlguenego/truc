<?php
	define('BASE_DIR', '../..');
	require_once(BASE_DIR."/include/misc.inc");
	$payment_type = "Complete";
	if ($_GET["payment_type"] == "authorization") {
		$payment_type = "Pending";
	}
?>
<html>
	<head>
		<title>Execute payment</title>
	</head>
	<body>
		<!-- action=payment_success&tx=2MN52113X70237425&st=Pending&amt=1.20&cc=EUR&cm=&item_number= -->
		<p><?php print_r($_GET); ?></p>
		<p>Do you want to proceed?</p>
		<a href="../../index.php?action=payment_success&tx=2MN52113X7023<?php printf("%04d", rand(0, 9999)); ?>&st=<?php echo $payment_type; ?>&amt=<?php echo curr($_GET["amount"]); ?>&cc=<?php echo $_GET["currency_code"]; ?>&cm=&item_number=">Yes</a>
		<a href="../../index.php?action=payment_cancel">No</a>
	</body>
</html>