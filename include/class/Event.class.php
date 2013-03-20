<?php
	class Event {
		public $id;
		public $title;
		public $confirmation_t;
		public $happening_t;
		public $funding_needed;
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

			$request = <<<EOF
SELECT * FROM `event`
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
			$mod_t = date('Y-m-d H:i:s', time());
			$title = $this->title;
			$happening_t = $this->happening_t;
			$confirmation_t = $this->confirmation_t;
			$funding_needed = $this->funding_needed;
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
	`mod_t`='${mod_t}',
	`id_user`="${id_user}",
	`title`="${title}",
	`location`="${location}",
	`link`="${link}",
	`short_description`="${description_short}",
	`long_description`="${description_long}",
	`happening_t`="${happening_t}",
	`confirmation_t`="${confirmation_t}",
	`funding_needed`="${funding_needed}",
	`funding_acquired`=0,
	`nominative`=${nominative},
	`status`=${status},
	`publish_flag`=${publish_flag}
EOF;
			if(!$g_pdo->exec($request)) {
				debug($request);
				throw new Exception("Get event participants mails: ".sprint_r($g_pdo->errorInfo())." InnoDB?");
			}
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
			$happening_t = $this->happening_t;
			$confirmation_t = $this->confirmation_t;
			$funding_needed = $this->funding_needed;
			$request = <<<EOF
UPDATE `event`
SET
	`mod_t`="${mod_t}",
	`title`="${title}",
	`location`="${location}",
	`link`="${link}",
	`short_description`="${description_short}",
	`long_description`="${description_long}",
	`happening_t`="${happening_t}",
	`confirmation_t`="${confirmation_t}",
	`funding_needed`="${funding_needed}"
WHERE `id`="${id}"
EOF;
			debug($request);
			$g_pdo->exec($request);
		}

		public function delete() {
			global $g_pdo;

			$this->check_linked_devis();

			$request = <<<EOF
DELETE FROM `rate`
WHERE `id_event`={$this->id}
EOF;
			debug($request);
			$g_pdo->exec($request);

			$request = <<<EOF
DELETE FROM `event`
WHERE `id`={$this->id}
EOF;
			debug($request);
			if(!$g_pdo->exec($request)) {
				throw new Exception("Event delete:".sprint_r($g_pdo->errorInfo()));
			}
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

		public function check_owner() {
			if ($this->user_id != get_id_from_account() && !is_admin()) {
				throw new Exception("You are not the creator of this event");
			}
		}

		public function check_linked_devis() {
			global $g_pdo;

			$request = "SELECT COUNT(*) FROM `devis` WHERE `id_event`=" . $this->id;
			debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute();
			$count = $q->fetch();
			if($count[0] > 0) {
				throw new Exception("This event can not be deleted for accountancy reasons.");
			}
		}
	}
?>