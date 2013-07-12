<?php
	require_once(BASE_DIR . "/include/constants.inc");
	require_once(BASE_DIR . "/include/globals.inc");
	require_once(BASE_DIR . "/include/misc.inc");
	require_once(BASE_DIR . "/include/print.inc");
	require_once(BASE_DIR . "/include/format.inc");
	$bill = Bill::get_from_id(100077);
?>

<?php print_bill($bill, PRINT_BILL_CONTEXT_DEFAULT, PRINT_BILL_FORMAT_PDF); ?>