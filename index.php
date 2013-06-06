<?php
	define("BASE_DIR", dirname(__FILE__));

	require_once(BASE_DIR . "/include/constants.inc");
	require_once(BASE_DIR . "/include/globals.inc");
	require_once(BASE_DIR . "/include/misc.inc");
	require_once(BASE_DIR . "/include/layout.inc");
	require_once(BASE_DIR . "/include/authentication.inc");
	require_once(BASE_DIR . "/include/actions.inc");
	require_once(BASE_DIR . "/include/payment.inc");
	require_once(BASE_DIR . "/include/mail.inc");
	require_once(BASE_DIR . "/include/form.inc");
	require_once(BASE_DIR . "/include/security.inc");
	require_once(BASE_DIR . "/include/format.inc");
	require_once(BASE_DIR . "/include/print.inc");
	require_once(BASE_DIR . "/include/dd.inc");
	require_once(BASE_DIR . "/include/openid.inc");

	if (isset($_SERVER["HTTPS"])) {
		$base_url = HTTPS_ACCESS ."/";
	} else {
		$base_url = 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']);
	}
	if (substr($base_url, -1) != "/") {
		$base_url .= "/";
	}
	define('HOST', $base_url);

	session_start();

	$g_i18n = new I18n();
	$g_i18n->init();

	debug(BASE_DIR);
	//debug("SERVER=".sprint_r($_SERVER));
	debug("GET=".sprint_r($_GET));
	debug("POST=".sprint_r($_POST));
	debug("SESSION=".sprint_r($_SESSION));

	// If not installed, goto installation page
	if (!is_installed()) {
		redirect_to("install.php");
	}

	if (!admin_exists()) {
		redirect_to("deploy.php");
	}

	// If installed
	require_once(BASE_DIR . "/" . SETTINGS_INI);

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
		debug(sprint_r($e->getTrace()));
	}

	if (!in_array($_SESSION["state"], $g_states)) {
		$g_error_msg = "Undeclared state: ".$_SESSION["state"].".";
		$_SESSION["state"] = "error";
	}
	debug("SESSION['state']=".$_SESSION["state"]);
	debug("g_page=".$g_page);
	if ($_SESSION["state"] == "not_allowed") {
		$g_page = "error";
	}

	if (!is_null_or_empty($g_page)) {
		$g_state = $g_page;
		$g_page = SKIN_DIR."/".$g_page.".php";
	} else {
		$g_state = $_SESSION["state"];
		$g_page = SKIN_DIR."/".$_SESSION["state"].".php";
	}

	if (!file_exists($g_page)) {
		$g_page = SKIN_DIR."/error.php";
		$g_error_msg = _t("Page not existing.");
	}
	debug("Session after: ".$_SESSION["state"]);
	//$g_info_msg = "This is an info message.";
	//$g_error_msg = "This is an error message.";

	layout_i18n(SKIN_DIR."/layout.php");
?>
