<?php
	function message_set_error($msg) {
		$_SESSION['error_msg'] = $msg;
	}

	function message_set_info($msg) {
		$_SESSION['info_msg'] = $msg;
	}

	function message_get_error() {
		global $g_error_msg;

		return $g_error_msg;
	}

	function message_get_info() {
		global $g_info_msg;

		return $g_info_msg;
	}

	function message_has_error() {
		global $g_error_msg;

		return !is_null_or_empty($g_error_msg);
	}

	function message_has_info() {
		global $g_info_msg;

		return !is_null_or_empty($g_info_msg);
	}

	function message_process() {
		global $g_error_msg;
		global $g_info_msg;

		if (isset($_SESSION['info_msg'])) {
			$g_info_msg = $_SESSION['info_msg'];
			unset($_SESSION['info_msg']);
		}
		if (isset($_SESSION['error_msg'])) {
			$g_error_msg = $_SESSION['error_msg'];
			unset($_SESSION['error_msg']);
		}
	}
?>