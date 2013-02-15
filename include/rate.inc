<?php
	require_once("include/user.inc");
	require_once("include/event.inc");
	
	function rate_exists($label, $event_id) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`rate`
WHERE `label`= :label AND `id_event= :event_id
EOF;
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
	
	
				// $request = <<<EOF
// UPDATE `${dbname}`.`rate` SET `rate`="${rate}" 
// WHERE `label`="${label}" AND `event_id`="${event_id}";
// EOF;
	
	function add_rate($label, $rate, $event_id) {
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
	`amount`="${rate}";
EOF;
			debug($request);
			$g_pdo->exec($request);
			return TRUE;
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
SELECT `label`, `amount` FROM `${dbname}`.`rate`
WHERE `id_event`=${id}
EOF;
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