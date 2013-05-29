<?php
	function db_get_columns($table) {
		global $g_pdo;

		$request = <<<EOF
SHOW COLUMNS
FROM $table
EOF;
		$pst = $g_pdo->prepare($request);
		$pst->execute();

		$result = array();
		while (($record = $pst->fetch()) != null) {
			$result[] = $record[0];
		}
		return $result;
	}

	function db_get_tables() {
		global $g_pdo;

		$request = "SHOW TABLES";
		$pst = $g_pdo->prepare($request);
		$pst->execute();

		$tables = array();
		while (($record = $pst->fetch(PDO::FETCH_NUM)) != null) {
			$tables[] = $record[0];
		}
		return $tables;
	}

	function db_execute_file($file) {
		global $g_pdo;

		$requests = file_get_contents($file);
		if (!preg_match("#;#", $requests)) {
			return;
		}
		echo nl2br($requests);
		$g_pdo->exec($requests);
	}

	function db_set_constraints($bool) {
		global $g_pdo;
		$value = 0;
		if ($bool) {
			$value = 1;
		}
		$request = "SET FOREIGN_KEY_CHECKS = ".$value;
		$pst = $g_pdo->prepare($request);
		$pst->execute();
	}
?>