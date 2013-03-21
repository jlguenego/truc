﻿<?php
	class Event {
		public $id = -1;
		public $title;
		public $confirmation_t;
		public $happening_t;
		public $open_t;
		public $funding_needed;
		public $funding_authorized;
		public $location;
		public $link;
		public $short_description;
		public $long_description;
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
			$event = $q->fetch(PDO::FETCH_ASSOC);
			if (!isset($event['id'])) {
				return NULL;
			}
			$this->hydrate($event);
		}

		public function load_default() {
			$this->title = "";
			$this->confirmation_t = "";
			$this->happening_t = "";
			$this->open_t = "";
			$this->funding_needed = "";
			$this->funding_authorized = 0;
			$this->location = "";
			$this->link = "http://";
			$this->short_description = "";
			$this->long_description = "";
			$this->nominative = 1;
			$this->status = EVENT_STATUS_PLANNED;
			$this->publish_flag = EVENT_PUBLISH_FLAG_NO;
			$this->user_id = get_id_from_account();;
		}

		private function hydrate($array) {
			foreach ($array as $key => $value) {
				debug($key."=>".$value);
				$this->$key = $value;
			}
		}

		public function store() {
			global $g_pdo;

			$this->id = create_id();
			$created_t = time();
			$mod_t = time();
			$status = EVENT_STATUS_PLANNED;
			$publish_flag = EVENT_PUBLISH_FLAG_NO;

			$request = <<<EOF
INSERT INTO `event`
SET
	`id`="{$this->id}",
	`created_t`="${created_t}",
	`mod_t`='${mod_t}',
	`id_user`="{$this->user_id}",
	`title`="{$this->title}",
	`location`="{$this->location}",
	`link`="{$this->link}",
	`short_description`="{$this->short_description}",
	`long_description`="{$this->long_description}",
	`happening_t`="{$this->happening_t}",
	`confirmation_t`="{$this->confirmation_t}",
	`open_t`="{$this->open_t}",
	`funding_needed`="{$this->funding_needed}",
	`funding_acquired`=0,
	`nominative`={$this->nominative},
	`status`=${status},
	`publish_flag`=${publish_flag}
EOF;
			debug($request);
			if(!$g_pdo->exec($request)) {
				throw new Exception("Get event participants mails: ".sprint_r($g_pdo->errorInfo())." InnoDB?");
			}
		}

		public function update() {
			global $g_pdo;

			$mod_t = time();
			$request = <<<EOF
UPDATE `event`
SET
	`mod_t`="${mod_t}",
	`title`="{$this->title}",
	`location`="{$this->location}",
	`link`="{$this->link}",
	`short_description`="{$this->short_description}",
	`long_description`="{$this->long_description}",
	`happening_t`="{$this->happening_t}",
	`confirmation_t`="{$this->confirmation_t}",
	`open_t`="{$this->open_t}",
	`funding_needed`="{$this->funding_needed}"
WHERE `id`="{$this->id}"
EOF;
			debug($request);
			if (!$g_pdo->exec($request)) {
				throw new Exception("Event update: " . sprint_r($g_pdo->errorInfo()));
			}
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

		public function can_participate() {
			if (s2t($this->open_t, "%Y-%m-%d") > time()) {
				return FALSE;
			}
		}
	}
?>