<html>
	<head>
		<link href="form.css" rel="stylesheet">
	</head>
	<body>
<?php
	define("BASE_DIR", "..");
	require_once(BASE_DIR . "/include/misc.inc");
	require_once(BASE_DIR . "/include/layout.inc");
	require_once(BASE_DIR . "/include/globals.inc");

	$f = new Form();
	$f->action = "form.php";
	$f->method = "GET";
	$f->add_text("Login", "login", "Enter your identifier");
	$f->add_password("Password", "password", "Enter your password");
	$f->add_submit("Login");
	echo $f->html();

?>
	</body>
</html>