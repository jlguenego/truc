<?php
	header("Cache-Control: no-cache");
	$css_id = null;
	global $g_state;
	switch ($g_state) {
		case "root":
			$css_id = "evt_main_root";
			break;
		default:
			$css_id = "evt_main_default";
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Event Biller</title>
		<meta charset="utf-8"/>
		<base href="<?php echo HOST; ?>" />

		<script type="text/javascript" src="jscript/misc.js"></script>
		<link href="<?php echo SKIN_DIR; ?>/default.css" rel="stylesheet">

		<link href="ext/jquery-ui-1.10.1.custom/css/ui-lightness/jquery-ui-1.10.1.custom.css" rel="stylesheet">
		<script src="ext/jquery-ui-1.10.1.custom/js/jquery-1.9.1.js"></script>
		<script src="ext/jquery-ui-1.10.1.custom/js/jquery-ui-1.10.1.custom.js"></script>
		<script src="ext/tiny_mce/tiny_mce.js"></script>
		<script src="ext/sha1.js"></script>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans">
	</head>
	<body>
		<div id="body">
<?php
	layout_header();
	layout_message();
?>
<div id="<?php echo $css_id; ?>">
<?php
	debug("including ".$g_i18n->filename("${g_page}"));
	include_once($g_i18n->filename("${g_page}"));
?>
</div>
<?php
	layout_footer();
?>
		</div>
<?php layout_trace(); ?>
	</body>
</html>