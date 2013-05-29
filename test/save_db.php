<?php
	define("BASE_DIR", dirname(dirname(__FILE__)));
	require_once(BASE_DIR . "/include/constants.inc");
	require_once(BASE_DIR . "/include/globals.inc");
	require_once(BASE_DIR . "/include/misc.inc");
	require_once(BASE_DIR . "/include/db.inc");

	$dirname = str_replace("\\", "/", BASE_DIR."/test/mydb");
	rmdir_recursive($dirname);
	mkdir($dirname, 0777, true);

	$tables = db_get_tables();

	foreach ($tables as $table) {
		$content = "";
		$columns = db_get_columns($table);
		$column_list = join(",", $columns);

		$request = <<<EOF
SELECT *
FROM $table
EOF;
		$pst = $g_pdo->prepare($request);
		$pst->execute();
		while (($record = $pst->fetch()) != null) {
			$values = array();
			foreach ($columns as $column) {
				if (!is_null($record[$column])) {
					$values[] = "'".str_replace("'", "\\'", $record[$column])."'";
				} else {
					$values[] = "NULL";
				}
			}
			$value_list = join(",", $values);
			$line = "INSERT INTO ${table} (${column_list}) VALUES (${value_list});\n";
			$content .= $line;
		}
		file_put_contents($dirname."/".$table.".sql", $content);
	}
?>