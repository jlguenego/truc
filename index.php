<?php
	require_once("include/constants.inc");
	require_once("include/misc.inc");
	
	if (!is_installed()) {
		redirect_to("install.php");
	}
	require_once(SETTINGS_INI);
	echo "Hello";
?>