<?php
	define('SITE_ROOT', ".");
	define('SETTINGS_INI', "settings.ini");
	$g_debug = true;
	
	// Allows to have only one entity of PDO
	if (file_exists(SETTINGS_INI)) {
		require_once(SETTINGS_INI);
		try {
			$g_pdo = new PDO('mysql:host='.MYSQL_HOST.';dbname='.MYSQL_DBNAME.';charset=UTF-8', MYSQL_USER, MYSQL_PASSWORD);
		} catch (Exception $e) {
		}
	}
?>