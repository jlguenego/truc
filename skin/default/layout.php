<html>
	<head>
		<title>EventIf</title>
		<meta charset="utf-8"/>
	</head>

<script type="text/javascript" src="jscript/misc.js"></script>

<link href="ext/jquery-ui-1.10.1.custom/css/ui-lightness/jquery-ui-1.10.1.custom.css" rel="stylesheet">
<script src="ext/jquery-ui-1.10.1.custom/js/jquery-1.9.1.js"></script>
<script src="ext/jquery-ui-1.10.1.custom/js/jquery-ui-1.10.1.custom.js"></script>

<style type="text/css">
	div.ui-datepicker{
		font-size:10px;
	}
	.help {
		font-size:12px;
	}

	table, td, th {
		border-collapse: collapse;
		border: 1px solid #000;
	}
</style>
<?php
	layout_header();
	layout_message();

	include_once(SKIN_DIR."/${page}.php");

	layout_footer();
	layout_trace();
?>
	</body>
</html>