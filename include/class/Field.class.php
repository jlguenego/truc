<?php
	class Field {
		public $name;
		public $type;
		public $value;

		public function __construct($array = "") {
			if (is_array($array)) {
				$this->name = $array[0];
				$this->type = $array[1];
			}
		}

		public function is_foreign_key() {
			global $g_dd;
			if (preg_match("#_id$#", $this->name) != 1) {
				return false;
			}
			if (!isset($g_dd[$this->type])) {
				return false;
			}
			return true;
		}
	}
?>