<?php
	class Event {
		public $id;
		public $title;
		public $event_deadline;
		public $event_date;
		public $funding_wanted;
		public $funding_authorized;
		public $location;
		public $link;
		public $description_short;
		public $description_long;
		public $nominative;
		public $status;
		public $publish_flag;
		public $user_id;
		public $rates = array();

		public function load($id) {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT * FROM `${dbname}`.`event`
WHERE `id`= ${id}
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute();
			$event = $q->fetch();
			if (!isset($event['id'])) {
				return NULL;
			}
			$this->hydrate($event);
		}

		private function hydrate($array) {
			foreach ($array as $key => $value) {
				$this->$key = $value;
			}
		}

		public function store() {
			global $g_pdo;

			$id = $this->id;
			$created_t = time();
			$mod_t = time();
			$title = $this->title;
			$event_date = $this->event_date;
			$event_deadline = $this->event_deadline;
			$funding_wanted = $this->funding_wanted;
			$location = $this->location;
			$link = $this->link;
			$description_short = $this->description_short;
			$description_long = $this->description_long;
			$nominative = $this->nominative;
			$status = EVENT_STATUS_PLANNED;
			$publish_flag = EVENT_PUBLISH_FLAG_NO;
			$id_user = $this->user_id;

			$request = <<<EOF
INSERT INTO `event`
SET
	`id`="${id}",
	`created_t`="${created_t}",
	`mod_t`="${mod_t}",
	`id_user`="${id_user}",
	`title`="${title}",
	`location`="${location}",
	`link`="${link}",
	`short_description`="${description_short}",
	`long_description`="${description_long}",
	`event_date`="${event_date}",
	`event_deadline`="${event_deadline}",
	`funding_wanted`="${funding_wanted}",
	`funding_acquired`=0,
	`nominative`=${nominative},
	`status`=${status},
	`publish_flag`=${publish_flag}
EOF;
			if(!$g_pdo->exec($request)) {
				debug($request);
			throw new Exception("Get event participants mails: ".sprint_r($g_pdo->errorInfo())." InnoDB?");
			}

			//foreach ($this->items as $item) {
			//	$item->create();
			//}
		}

		public function update() {
			global $g_pdo;

			$id = $this->id;
			$mod_t = time();
			$title = $this->title;
			$location = $this->location;
			$link = $this->link;
			$description_short = $this->description_short;
			$description_long = $this->description_long;
			$event_date = $this->event_date;
			$event_deadline = $this->event_deadline;
			$funding_wanted = $this->funding_wanted;
			$request = <<<EOF
UPDATE `event`
SET
	`mod_t`="${mod_t}",
	`title`="${title}",
	`location`="${location}",
	`link`="${link}",
	`short_description`="${description_short}",
	`long_description`="${description_long}",
	`event_date`="${event_date}",
	`event_deadline`="${event_deadline}",
	`funding_wanted`="${funding_wanted}"
WHERE `id`="${id}"
EOF;
			debug($request);
			$g_pdo->exec($request);
		}

		public function is_published() {
			return $this->publish_flag == EVENT_PUBLISH_FLAG_YES;
		}

		public function is_confirmed() {
			return $this->status == EVENT_STATUS_CONFIRMED;
		}

		public function is_cancelled() {
			return $this->status == EVENT_STATUS_CANCELLED;
		}
	}
?>