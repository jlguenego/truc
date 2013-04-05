<!DOCTYPE html>
<html>
	<head>
		<script type="text/javascript" src="../jscript/misc.js"></script>
		<link href="../skin/default/default.css" rel="stylesheet">

		<link href="../ext/jquery-ui-1.10.1.custom/css/ui-lightness/jquery-ui-1.10.1.custom.css" rel="stylesheet">
		<script src="../ext/jquery-ui-1.10.1.custom/js/jquery-1.9.1.js"></script>
		<script src="../ext/jquery-ui-1.10.1.custom/js/jquery-ui-1.10.1.custom.js"></script>
		<script src="../ext/tiny_mce/tiny_mce.js"></script>
	</head>
	<body>
		<?php
			$f = new Form();
		?>
		<script>
			tinyMCE.init({
		        // General options
		        mode : "textareas",
		        theme : "advanced",
		        plugins : "lists,spellchecker,advhr,preview",

		        // Theme options
		        theme_advanced_buttons1 : "fontsizeselect,|,bold,italic,underline,|,bullist,numlist,|,undo,redo,|,link,unlink,anchor,image,|,copy,cut,paste,|,code,|,preview,",
		        theme_advanced_toolbar_location : "top",
		        theme_advanced_toolbar_align : "left",
		        theme_advanced_statusbar_location : "bottom",
		        theme_advanced_resizing : true,

		        // Skin options
		        skin : "o2k7",
		        skin_variant : "silver",

		        // Example content CSS (should be your site CSS)
		        //content_css : "css/example.css",

		        // Drop lists for link/image/media/template dialogs
		        template_external_list_url : "js/template_list.js",
		        external_link_list_url : "js/link_list.js",
		        external_image_list_url : "js/image_list.js",
		        media_external_list_url : "js/media_list.js",

		        // Replace values for the template plugin
		        template_replace_values : {
		                username : "Some User",
		                staffid : "991234"
		        }
			});
		</script>
	</body>
</html>