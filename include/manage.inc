<?php 
	require_once('include/user.inc');
	require_once('include/event.inc');
	
	function is_participating($id_user, $id_event) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`participate`
WHERE `id_user`= :id_user AND `id_event`= :id_event
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(":id_user" => $id_user, 
				":id_event" => $id_event));
			$count = $q->fetch();
			return $count[0] > 0;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
	
	function participation_quantity($id_user, $id_event) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT `quantity` FROM `${dbname}`.`participate`
WHERE `id_user`=${id_user} AND `id_event`=${id_event}
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute();
			$quantity = $q->fetch();
			return $quantity[0];
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
	
	function participate($id_user, $id_event, $nbr) {
		try {
			$nbr=(int)$nbr;
			global $g_pdo;
			if (!user_exists($id_user) || !event_exists($id_event)) {
				debug("error");
				return FALSE;
			}
			$dbname = MYSQL_DBNAME;
			if (is_participating($id_user, $id_event)) {
				$nbr += participation_quantity($id_user, $id_event);
				$request = <<<EOF
UPDATE `${dbname}`.`participate` 
SET `quantity`=${nbr}
WHERE `id_user`=${id_user} AND `id_event`="${id_event}";
EOF;
			} else {
				$request = <<<EOF
INSERT INTO `${dbname}`.`participate` 
SET 
	`id_user`=${id_user}, 
	`id_event`="${id_event}",
	`quantity`=${nbr}
EOF;
			}
			debug($request);
			$g_pdo->exec($request);
			event_add_person($id_event, $nbr);
			return TRUE;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}	
	
	function user_participations($user_id) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT `id_event`, `quantity` FROM `${dbname}`.`participate` 
WHERE `id_user`=${user_id}
EOF;
			//debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute();
			$participation = $q->fetch();
			$participations = array();
			while (isset($participation["id_event"])) {
				$participations[] = $participation;
				$participation = $q->fetch();
			}
			return $participations;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
?>