<!DOCTYPE html>
<html>
	<head>
		<script type="text/javascript" src="../ext/jquery-ui-1.10.1.custom/js/jquery-1.9.1.js"></script>
	</head>
	<body>
		<input type="text" name="test"/>
		<p></p>
		<script>
			$("input").keyup(function() {
				$("p").html($(this).val());
			});
		</script>
	</body>
</html>