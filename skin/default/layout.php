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

		<link href="_ext/jquery-ui-1.10.1.custom/css/ui-lightness/jquery-ui-1.10.1.custom.css" rel="stylesheet">
		<script src="_ext/jquery-ui-1.10.1.custom/js/jquery-1.9.1.js"></script>
		<script src="_ext/jquery-ui-1.10.1.custom/js/jquery-ui-1.10.1.custom.js"></script>
		<script src="_ext/tinymce/tinymce.min.js"></script>
		<script src="_ext/sha1.js"></script>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans">

		<!-- AddThisEvent -->
		<script type="text/javascript" src="http://js.addthisevent.com/atemay.js"></script>

		<?php google_analytics(); ?>
	</head>
	<body>
		<?php echo facebook_jscript_reference(); ?>
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
	layout_fixed();
?>
			</div>
			<?php layout_trace(); ?>
		</div>
<?php
		if (isset($_SESSION['event_id'])) {
?>
		<script>
			var task_page = "<?php echo HOST; ?>/task.php";
			$(document).ready(function() {
				eb_execute_tasks(<?php echo $_SESSION["event_id"]; ?>);
			});
		</script>
<?php
		}
?>
<!-- AddThisEvent Settings -->
<script type="text/javascript">
addthisevent.settings({
    mouse     : false,
    css       : true,
    outlook   : {show:true, text:"{{Outlook Calendar}}"},
    google    : {show:true, text:"{{Google Calendar}}"},
    yahoo     : {show:true, text:"{{Yahoo Calendar}}"},
    hotmail   : {show:true, text:"{{Hotmail Calendar}}"},
    ical      : {show:true, text:"{{iCal Calendar}}"},
    facebook  : {show:true, text:"{{Facebook Event}}"},
    callback  : ""
});
</script>
	</body>
</html>