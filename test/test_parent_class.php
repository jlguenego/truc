<?php
	class A {

	}

	class B extends A {

	}

	echo "A parent: ".get_parent_class("A")."\n";
	echo "B parent: ".get_parent_class("B");
?>