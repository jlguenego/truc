<?php
/**
 *
 * Use this file to define global variable
 *
**/

	$g_locales = array("en", "fr");
	$g_default_locale = "en";
	$g_i18n = null;

	// sync lib
	$g_sync_lock_file = null;

	$g_states = array(
		"root",
		"error",
		"sign_in",
		"participation_regular",
		"participation_regular_recapitulation",
		"participation_nominative",
		"participation_nominative_recapitulation",
		"not_allowed",
		"event_retrieve",
		"event_form",
		"event_delete",
		"event_list",
		"event_promote",
		"account_create",
		"account_retrieve",
		"account_update",
		"account_delete_confirm",
		"devis_retrieve",
		"payment_cancel",
		"payment_success",
		"supervision",
		"forgotten_password_form",
		"forgotten_password_mail_sent",
		"reset_password_form",
		"participation_list_regular",
		"participation_list",
		"guest_file_form",
		"record_manage",
		"task_run",
		"bitcoin_payment",
	);

	$g_event_states = array(
		EVENT_STATUS_PLANNED => "Planned",
		EVENT_STATUS_CONFIRMED => "Confirmed",
		EVENT_STATUS_CANCELLED => "Cancelled",
		EVENT_STATUS_INACTIVATED => "Inactivated",
	);

	$g_event_publish_flag = array(
		EVENT_PUBLISH_FLAG_YES => "Yes",
		EVENT_PUBLISH_FLAG_NO => "No",
	);

	$g_tax_rates = array(
		"With tax (19.6%)" => 19.6,
		"With tax (5.5%)" => 5.5,
		"Without tax" => 0
	);

	$g_task_command_list = array(
		"mail_advertisement",
	);

	$g_debug_lines = array();
	$g_error_msg = "";
	$g_info_msg = "";

	$g_page = null;
	$g_state = null;

	// Allows to have only one entity of PDO
	$g_pdo = null;
	$g_mysali = null;
	$g_use_recaptcha = true;
	$g_debug = true;
	if (file_exists(BASE_DIR . "/" . SETTINGS_INI)) {
		require_once(BASE_DIR . "/" . SETTINGS_INI);
		$g_debug = TEST_MODE;
		$g_use_recaptcha = (!TEST_MODE) || ($_SERVER["HTTP_HOST"] == "localhost");
		try {
			$g_pdo = new PDO('mysql:host='.MYSQL_HOST.';dbname='.MYSQL_DBNAME.';charset=UTF-8', MYSQL_USER, MYSQL_PASSWORD);
			$g_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$g_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$g_pdo->exec("set names utf8"); // ovh php 5.2
		} catch (Exception $e) {
		}
	}

	$g_display = array(); // Layout pages should access only to this one
?>