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
	$f->add_password("Password", "password", "Enter your password");
	$f->add_email("Email", "email", "", "Enter your email");
	$f->add_number("Birth year", "year", "", "Enter your birth year");
	$f->add_textarea("Suggestion/Comment", "suggestion", "", "Enter your ideas...");
	$f->add_checkbox("All tickets must be nominative", "nominative", "checked", "All ticket of an nominative event must have a participant name indicated.");
	$options = <<<EOF
<option value="M">Male</option>
<option value="F" selected>Female</option>
EOF;
	$f->add_select("Gender", "gender", $options, "Enter your gender...");
	$f->add_submit("email");
	echo $f->html();

?>
	</body>
</html>