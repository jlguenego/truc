<?php
	require_once(BASE_DIR . '/include/constants.inc');

	function payment_button() {
		global $g_display;
		$event_confirmed = $g_display["event_confirmed"];

		if ($event_confirmed) {
			require_once(BASE_DIR . '/payment/'.PAYMENT_PROVIDER.'/button_direct.php');
		} else {
			require_once(BASE_DIR . '/payment/'.PAYMENT_PROVIDER.'/button_authorization.php');
		}
	}
?>