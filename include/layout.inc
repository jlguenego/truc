<?php
	require_once("include/user.inc");
	
	function layout_header() {
		require_once(SKIN_DIR."/header.inc");
	}
	
	function layout_footer() {
		require_once(SKIN_DIR."/footer.inc");
	}
	
	function layout_message() {
		global $g_info_msg;
		if (!is_null_or_empty($g_info_msg)) {
			echo $g_info_msg."<br/>";
		}
	}
	
	function layout_trace() {
		global $g_debug;
		global $g_debug_lines;
		if ($g_debug) {
			echo "<h3>[TRACE]</h3>";
			foreach ($g_debug_lines as $debug) {
				echo $debug;
			}
		}
	}
	
	function paypal_button() {
		global $g_display;
		$event_confirmed = $g_display["event_confirmed"];
		
		if ($event_confirmed) {
			echo "Confirmed";
		} else {
			echo "Non confirmed";
		}
	}
?>