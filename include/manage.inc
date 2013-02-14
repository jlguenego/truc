<?php 
	require_once('include/user.inc');
	require_once('include/event.inc');
	
	function participate($id_user, $id_event, $nbr) {
		try {
			global $g_pdo;
			if (!user_exists($id_user) || !event_exists($id_event)) {
				debug("error");
				return FALSE;
			}
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
INSERT INTO `${dbname}`.`participate` 
SET 
	`id_user`=${id_user}, 
	`id_event`="${id_event}";
	
EOF;
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