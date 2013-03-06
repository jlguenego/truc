<html>
	<head>
		<title>EventIf</title>
		<meta charset="utf-8"/>
	</head>
<link rel="stylesheet" href="ext/jquery-ui.css" />

<script type="text/javascript" src="jscript/misc.js"></script>
<script src="ext/jquery-1.9.1.js"></script>
<script src="ext/jquery-ui.js"></script>

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