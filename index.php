<?php
	define("BASE_DIR", ".");

	require_once(BASE_DIR . "/include/constants.inc");
	require_once(BASE_DIR . "/include/globals.inc");
	require_once(BASE_DIR . "/include/misc.inc");
	require_once(BASE_DIR . "/include/layout.inc");
	require_once(BASE_DIR . "/include/user.inc");
	require_once(BASE_DIR . "/include/rate.inc");
	require_once(BASE_DIR . "/include/actions.inc");
	require_once(BASE_DIR . "/include/manage.inc");
	require_once(BASE_DIR . "/include/payment.inc");
	require_once(BASE_DIR . "/include/mail.inc");
	require_once(BASE_DIR . "/include/form.inc");
	require_once(BASE_DIR . "/include/security.inc");

	debug("SERVER=".sprint_r($_SERVER));
	debug("GET=".sprint_r($_GET));
	debug("POST=".sprint_r($_POST));

	// If not installed, goto installation page
	if (!is_installed()) {
		redirect_to("install.php");
	}

	if (!admin_exists()) {
		redirect_to("deploy.php");
	}

	// If installed
	require_once(BASE_DIR . "/" . SETTINGS_INI);

	session_start();

	$_GET = array_merge($_GET, $_POST);
	security_html_injection();
	debug("GET=".sprint_r($_GET));

	if (is_null_or_empty($_SESSION["state"])) {
		$_SESSION["state"] = "root";
	}
	debug("Session before: ".$_SESSION["state"]);

	if (is_null_or_empty($_GET["action"])) {
		$_SESSION["state"] = "root";
		$_GET["action"] = "none";
	}

	try {
		action();
	} catch (Exception $e) {
		$_SESSION["state"] = "error";
		$g_error_msg = $e->getMessage();
	}

	if (!in_array($_SESSION["state"], $g_states)) {
		$g_error_msg = "Undeclared state: ".$_SESSION["state"].".";
		$_SESSION["state"] = "error";
	}
	if ($_SESSION["state"] == "not_allowed") {
		$page = "error";
	} else {
		$page = $_SESSION["state"];
	}

	debug("Session after: ".$_SESSION["state"]);
	include_once(SKIN_DIR."/layout.php");
?>