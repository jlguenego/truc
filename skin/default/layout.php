<?php
	global $g_state;

	header("Cache-Control: no-cache");

	$css_div_id = "evt_".$g_state;
	$css_class_id = "evt_main";
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
		<div id="global_frame">
			<div id="body">
<?php
	layout_header();
?>
<div id="<?php echo $css_div_id; ?>" class="<?php echo $css_class_id; ?>">
<?php
	layout_message();
	debug("including ".$g_i18n->filename("${g_page}"));
	include_once($g_i18n->filename("${g_page}"));
?>
</div>
<?php
	layout_footer();
?>
			</div>
			<?php layout_trace(); ?>
		</div>
		<script>
			var task_page = "<?php echo HOST; ?>/task.php";
			$(document).ready(function() {
				eb_execute_tasks(<?php echo $_SESSION["event_id"]; ?>);
			});
		</script>
	</body>
</html>