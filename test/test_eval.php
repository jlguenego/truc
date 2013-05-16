<?php
	function crash() {
		echo "Mayday\n";
		throw new Exception("Crash!");
	}

	try {
		eval("crash();");
		echo "Toto va bien";
	} catch (Exception $e) {
		echo $e->getMessage();
	}
?>