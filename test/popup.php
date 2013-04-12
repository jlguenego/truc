<!DOCTYPE html>
<html>
	<head>
		<link href="../ext/jquery-ui-1.10.1.custom/css/ui-lightness/jquery-ui-1.10.1.custom.css" rel="stylesheet">
		<script src="../ext/jquery-ui-1.10.1.custom/js/jquery-1.9.1.js"></script>
		<script src="../ext/jquery-ui-1.10.1.custom/js/jquery-ui-1.10.1.custom.js"></script>
	</head>
	<body>
		<button id="open">Click Me</button><br/>
		You typed this:
		<div id="result"></div>
		<div id="dialog" style="display: none;" title="Fill this.">
			<textarea id="keke"></textarea>
		</div>
		<script>
			$("#open").click(function(){
				$("#dialog").dialog({
					modal: true,
					buttons: {
						Ok: function() {
							var content = $("#keke").val();
							console.log("content="+content);
							$("#result").html(content);
							//$(this).dialog("close");
							$(this).dialog("destroy").remove();
						}
					}
			    });
			});
		</script>
	</body>
</html>