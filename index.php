<?php
	require_once("include/constants.inc");
	require_once("include/globals.inc");
	require_once("include/misc.inc");
	require_once("include/layout.inc");
	require_once("include/user.inc");
	require_once("include/event.inc");
	require_once("include/rate.inc");
	require_once("include/actions.inc");
	require_once("include/manage.inc");
	require_once("include/payment.inc");
	require_once("include/devis.inc");
	require_once("include/mail.inc");

	// If not installed, goto installation page
	if (!is_installed()) {
		redirect_to("install.php");
	}

	if (!admin_exists()) {
		redirect_to("deploy.php");
	}

	// If installed
	require_once(SETTINGS_INI);

	session_start();

	$_GET = array_merge($_GET, $_POST);
	if (is_null_or_empty($_SESSION["state"])) {
		$_SESSION["state"] = "root";
	}
	debug("Session before: ".$_SESSION["state"]);

	if (is_null_or_empty($_GET["action"])) {
		$_SESSION["state"] = "root";
		$_GET["action"] = "none";
	}

	action();

	if (!in_array($_SESSION["state"], $g_states)) {
		$g_error_msg = "Undeclared state: ".$_SESSION["state"].".";
		$_SESSION["state"] = "not_allowed";

	}
	$page = $_SESSION["state"];

	debug("Session after: ".$_SESSION["state"]);
	debug("<hr/>");
	include_once(SKIN_DIR."/layout.php");
?>