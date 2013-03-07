<?php
/**
 *
 * Use this file to define global variable
 *
**/
	$g_states = array(
		"root",
		"error",
		"sign_in",
		"participation",
		"participation_recapitulation",
		"not_allowed",
		"event_create",
		"event_retrieve",
		"event_update",
		"event_delete",
		"events_list",
		"account_create",
		"account_retrieve",
		"account_update",
		"cancel_payment",
		"success_payment",
	);
	
	$g_tax_rates = array(
		"With tax (19.6%)" => 19.6,
		"Without tax" => 0	
	);

	$g_debug = true;
	$g_debug_lines = array();
	$g_error_msg = "";
	$g_info_msg = "";
	
	// Allows to have only one entity of PDO
	if (file_exists(SETTINGS_INI)) {
		require_once(SETTINGS_INI);
		try {
			$g_pdo = new PDO('mysql:host='.MYSQL_HOST.';dbname='.MYSQL_DBNAME.';charset=UTF-8', MYSQL_USER, MYSQL_PASSWORD);
		} catch (Exception $e) {
		}
	}
	
	$g_display = array(); // Layout pages should access only to this one
?>