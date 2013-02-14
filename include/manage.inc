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
	`id_event`="${id_event}";
	`quantity`=${nbr}
EOF;
			}
			debug($request);
			$g_pdo->exec($request);
			add_person_to_event($id_event, $nbr);
			return TRUE;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
?>