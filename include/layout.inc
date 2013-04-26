<?php
	function layout_i18n($file) {
		global $g_page;
		global $g_display;
		global $g_i18n;

		ob_start();
		require_once($file);
		$layout = ob_get_contents();
		ob_clean();
		print($g_i18n->parse($layout));
	}

	function layout_header() {
		require_once(BASE_DIR . "/" . SKIN_DIR."/header.inc");
	}

	function layout_footer() {
		require_once(BASE_DIR . "/" . SKIN_DIR."/footer.inc");
	}

	function layout_message() {
		global $g_info_msg;
		global $g_error_msg;

		//$g_info_msg .= "DEBUG";

		if (!is_null_or_empty($g_info_msg)) {
?>
		<div id="evt_info">{{Info:}} <?php echo $g_info_msg; ?></div>
<?php
		}
		if (!is_null_or_empty($g_error_msg)) {
?>
		<div id="evt_error">{{Error:}} <?php echo $g_error_msg; ?></div>
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