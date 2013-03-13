<?php
	require_once("include/user.inc");
	require_once("include/event.inc");

	function rate_exists($label, $event_id) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`rate`
WHERE `label`= :label AND `id_event`= :event_id
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute(array(":label" => $label,
				":event_id" => $event_id));
			$count = $q->fetch();
			return $count[0] > 0;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}

	function update_rate($event_id, $label, $tax_rate, $amount) {
		try {
			global $g_pdo;

			if (!event_exists($event_id)) {
				return FALSE;
			}
			if (!rate_exists($label, $event_id)) {
				return add_rate($label, $amount, $tax_rate, $event_id);
			}
			$label = check($label);
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
UPDATE `${dbname}`.`rate`
SET `amount`="${amount}" , `tax_rate`="${tax_rate}"
WHERE `label`="${label}" AND `id_event`="${event_id}";
EOF;
			debug($request);
			$g_pdo->exec($request);
			return TRUE;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}

	function add_rate($label, $rate, $tax_rate, $event_id) {
		try {
			global $g_pdo;

			if (!event_exists($event_id)) {
				return FALSE;
			}
			if (rate_exists($label, $event_id)) {
				return FALSE;
			}
			$label = check($label);
			$id = create_id();
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
INSERT INTO `${dbname}`.`rate`
SET
	`id`="${id}",
	`label`="${label}",
	`id_event`="${event_id}",
	`tax_rate`="${tax_rate}",
	`amount`="${rate}";
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute();
			return TRUE;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}

	function delete_events_rates($id_event) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
DELETE FROM `${dbname}`.`rate`
WHERE `id_event`=${id_event}
EOF;
			$g_pdo->exec($request);
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}

	function delete_unuse_rates($event_id, $labels) {
		$rates = events_rates($event_id);
		foreach ($rates as $rate) {
			if (!in_array($rate["label"], $labels)) {
				debug("Deleted ".$rate["label"]);
				delete_rate($rate["label"], $event_id);
			}
		}
	}

	function delete_rate($label, $id_event) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
DELETE FROM `${dbname}`.`rate`
WHERE `label`="${label}" AND `id_event`=${id_event}
EOF;
			debug($request);
			$g_pdo->exec($request);
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}

	function events_rates($id) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT `label`, `amount`, `tax_rate` FROM `${dbname}`.`rate`
WHERE `id_event`=${id}
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute();
			$rate = $q->fetch();
			$rates = array();
			while (isset($rate["label"])) {
				$rates[] = $rate;
				$rate = $q->fetch();
			}
			return $rates;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
?>