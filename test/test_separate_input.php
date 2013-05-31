<!DOCTYPE html>
<html>
	<head>
		<script type="text/javascript" src="../_ext/jquery-ui-1.10.1.custom/js/jquery-1.9.1.js"></script>
	</head>
	<body>
		<form action="test_separate_input.php" method="get">
			<input type="text" name="login" />
			<input type="password" name="password" />
			<input type="submit" value="ok" />
		</form>
		<script>
			$('form').submit(function() {
				 $('input[type=password]').attr('name', '');
			});
		</script>
	</body>
</html>

