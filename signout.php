<?php
	require_once("include/misc.inc");	
	session_destroy();
	session_start();
	redirect_to("index.php");
?>