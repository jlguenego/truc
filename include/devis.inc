<?php
	function devis_get_all_for_event($event_id) {
		global $g_pdo;

		$request = <<<EOF
SELECT * FROM `devis`
WHERE `id_event`=${event_id}
EOF;
		debug($request);
		$q = $g_pdo->prepare($request);
		if ($q->execute() === FALSE) {
			debug($request);
			throw new Exception("Get devis: ".sprint_r($g_pdo->errorInfo())." InnoDB?");
		};

		$devis_array = array();
		while ($row = $q->fetch()) {
			$items = devis_get_items($row["id"]);
			$row["items"] = $items;
			$devis = new Devis();
			$devis->build($row);
			$devis_array[] = $devis;
		}
		return $devis_array;
	}

	function devis_get_items($devis_id) {
		global $g_pdo;

		$request = <<<EOF
SELECT * FROM `devis_item`
WHERE `id_devis`=${devis_id}
EOF;
		debug($request);
		$q = $g_pdo->prepare($request);
		if ($q->execute() === FALSE) {
			debug($request);
			throw new Exception("Get devis_items: ".sprint_r($g_pdo->errorInfo())." InnoDB?");
		};
		$items = array();
		while ($item = $q->fetch()) {
			$items[] = $item;
		}
		return $items;
	}

	function devis_all_set_status_for_event($event_id, $status) {
		$devis_array = devis_get_all_for_event($event_id);
		foreach ($devis_array as $devis) {
			devis_set_status($devis, $status);
		}
	}

	function devis_set_status($devis, $status) {
		$devis->status = $status;
		$devis->update();
	}
?>