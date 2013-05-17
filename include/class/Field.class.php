<?php
	class Field {
		public $name;
		public $type;
		public $value;
		public $colname;
		public $is_in_create_form = true;

		public function __construct($name, $type) {
			$this->name = $name;
			$this->type = $type;
			$this->colname = Field::get_colname($name);
		}

		public static function get_colname($name) {
			$result = preg_replace("#^(.*)_id$#", "id_$1", $name);
			debug("dd_get_colname($name)=".$result);
			return $result;
		}

		public function is_foreign_key() {
			global $g_dd;
			if (preg_match("#_id$#", $this->name) != 1) {
				return false;
			}
			if (!$g_dd->has_entity($this->type)) {
				return false;
			}
			return true;
		}
	}
?>