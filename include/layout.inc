<?php
	require_once(BASE_DIR . "/include/user.inc");

	function layout_header() {
		require_once(BASE_DIR . "/" . SKIN_DIR."/header.inc");
	}

	function layout_footer() {
		require_once(BASE_DIR . "/" . SKIN_DIR."/footer.inc");
	}

	function layout_message() {
		global $g_info_msg;
		if (!is_null_or_empty($g_info_msg)) {
?>
		<div id="info"><?php echo $g_info_msg; ?></div>
<?php
		}
	}

	function layout_trace() {
		global $g_debug;
		global $g_debug_lines;
		if ($g_debug) {
			echo '<div id="evt_trace">';
			echo "<h3>[TRACE]</h3>";
			foreach ($g_debug_lines as $debug) {
				echo $debug;
			}
			echo '</div>';
		}
	}
?>