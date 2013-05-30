<?php
	$input = "toto.coco/truc.php";
	$replacement = "[add_in_front_of_extention]";
	$output = preg_replace("/([.][^.]*?)$/", ".".$replacement."$1", $input);
	echo "input=".$input."<br/>";
	echo "output=".$output;
?>