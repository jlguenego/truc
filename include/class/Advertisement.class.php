<?php
	class Advertisement extends Record {
		public function __construct() {
			parent::__construct("advertisement");
		}

		public function action_send() {
			debug("Action Send");
		}
	}
?>