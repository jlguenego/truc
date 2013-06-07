<?php
	class A {
		public $x;
		public $y;
	}

	$a = new A();
	$a->x = "coucou";
	$a->y = "hello";

	$b = clone $a;
	var_dump($a);
	var_dump($b);
	$b->x = "salut";
	var_dump($a);
	var_dump($b);
?>