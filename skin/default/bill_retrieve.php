<?php
	$bill = $g_display["bill"];

	if ($bill->is_for(BILL_FOR_ORGANIZER)) {
		print_organizer_invoice($bill);
	} else {
		print_bill($bill);
	}
?>
