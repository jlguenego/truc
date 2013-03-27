<?php
	header("Cache-Control: no-cache");
?>
<html>
	<head>
		<title>EventIf</title>
		<meta charset="utf-8"/>
	</head>

<script type="text/javascript" src="jscript/misc.js"></script>
<link href="<?php echo SKIN_DIR; ?>/default.css" rel="stylesheet">

<link href="ext/jquery-ui-1.10.1.custom/css/ui-lightness/jquery-ui-1.10.1.custom.css" rel="stylesheet">
<script src="ext/jquery-ui-1.10.1.custom/js/jquery-1.9.1.js"></script>
<script src="ext/jquery-ui-1.10.1.custom/js/jquery-ui-1.10.1.custom.js"></script>
<script src="ext/tiny_mce/tiny_mce.js"></script>


<?php
	layout_header();
	layout_message();
?>
<div id="evt_main">
<?php
	debug("including " . SKIN_DIR."/${page}.php");
	include_once(SKIN_DIR."/${page}.php");
?>
</div>
<?php
	layout_footer();
	layout_trace();
?>
	</body>
</html>