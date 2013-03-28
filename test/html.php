<?php
	libxml_use_internal_errors(true);
	header("Content-Type: text/plain; charset=utf-8");
	$str = <<<EOF
<html>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	</head>
	<body>
		<p>Toto vas à la <b>plage</b>.</p>
	</body></div></b>
</html>
EOF;
	libxml_use_internal_errors(false);

	$str = strip_tags($str, "<meta><b><i>");

	$doc = new DOMdocument();
	$doc->loadHTML($str);
	$str = $doc->saveHTML();

	echo $str;
?>