<?php
	function sync_lock() {
		global $g_sync_lock_file;
		if ($g_sync_lock_file != null) {
			return false;
		}
		// try to create a file and lock it.
		$g_sync_lock_file = fopen(SYNC_LOCK_FILE, "w");
		if ($g_sync_lock_file == false) {
			return false;
		}
		// try to put a lock on the file
		return flock($g_sync_lock_file, LOCK_EX);
	}

	function sync_unlock() {
		global $g_sync_lock_file;
		if ($g_sync_lock_file == null) {
			return false;
		}
		$result = fclose($g_sync_lock_file);
		$g_sync_lock_file = null;
		return $result;
	}


?>