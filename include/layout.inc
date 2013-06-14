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

	function layout_fixed() {
		require_once(BASE_DIR . "/" . SKIN_DIR."/fixed.inc");
	}

	function layout_header() {
		require_once(BASE_DIR . "/" . SKIN_DIR."/header.inc");
	}

	function layout_footer() {
		require_once(BASE_DIR . "/" . SKIN_DIR."/footer.inc");
	}

	function layout_message() {
		if (message_has_info()) {
?>
		<div data-message="evt_info" class="evt_message evt_shadowed">{{Info:}} <?php echo message_get_info(); ?></div>
<?php
		}
		if (message_has_error()) {
?>
		<div data-message="evt_error" class="evt_message evt_shadowed">{{Error:}} <?php echo message_get_error(); ?></div>
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