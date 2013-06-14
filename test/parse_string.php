<?php
	$query_string = "?toto=xdd&kiki=plouf";
	parse_str($query_string, $_GET);
	print_r($_GET);
?>