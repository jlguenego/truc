<?php
	define("BASE_DIR", dirname(dirname(__FILE__)));

	require_once(BASE_DIR . "/include/constants.inc");
	require_once(BASE_DIR . "/include/globals.inc");
	require_once(BASE_DIR . "/include/misc.inc");

	print_r(Address::get_unused());
?>

		public static function get_unused() {
			$array = array(
				'bill' => array(
					'id_address',
				),
				'event' => array(
					'id_address',
					'id_address1',
				),
				'user' => array(
					'id_address',
				),
			);

			$used_addresses = array();
			foreach ($array as $table_name => $fields_names) {
				foreach (Address::get_ids($table_name, $fields_names) as $id) {
					$used_addresses[] = $id;
				}
			}
			global $g_pdo;

			$where = '';
			$first = true;
			foreach ($used_addresses as $used_address) {
				if (!$first) {
					$where .= ' AND ';
				}
				$where .= 'a.id != '.$used_address;
				$first = false;
			}

			$request = <<<EOF
SELECT
  a.id
FROM
  address a
WHERE
  ${where}
GROUP BY a.id
EOF;

			$pst = $g_pdo->prepare($request);
			$pst->execute();
			$array = array();
			while (($record = $pst->fetch(PDO::FETCH_ASSOC)) != null) {
				$array[] = $record['id'];
			}
			echo($request.'<br/>');
			return $array;
		}

		public function get_ids($table_name, $fields_names) {
			global $g_pdo;

			$where = '';
			$first = true;
			foreach ($fields_names as $fields_name) {
				if (!$first) {
					$where .= ' OR ';
				}
				$where .= 'a.id = '.$table_name.'.'.$fields_name;
				$first = false;
			}

			$request = <<<EOF
SELECT
  a.id
FROM
  address a,
  ${table_name}
WHERE
  ${where}
GROUP BY a.id
EOF;

			$pst = $g_pdo->prepare($request);
			$pst->execute();
			$array = array();
			while (($record = $pst->fetch(PDO::FETCH_ASSOC)) != null) {
				$array[] = $record['id'];
			}
			echo($request.'<br/>');
			return $array;
		}