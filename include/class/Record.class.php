<?php
	class Record {
		public $type;

		public $id;
		public $created_t;
		public $mod_t;
		public $fields = array();

		public function __construct($type) {
			$this->type = $type;
		}

		public static function get_from_id($type, $id) {
			global $g_pdo;
			global $g_dd;

			$request = <<<EOF
SELECT * FROM $type
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $id));
			$r = $pst->fetch();
			if (!isset($r['id'])) {
				return NULL;
			}
			$record = new Record($type);

			$record->id = $id;
			$record->created_t = time();
			$record->mod_t = $record->created_t;
			foreach ($g_dd[$record->type] as $array) {
				$field = new Field($array);
				$field->value = $r[dd_get_colname($field->name)];
				$record->fields[] = $field;
			}

			return $record;
		}

		public function delete() {
			global $g_pdo;
			$request = <<<EOF
DELETE FROM {$this->type}
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
		}

		public static function get_table($type) {
			$result = <<<EOF
<table class="evt_table">
	<tr>
EOF;
			$columns = Record::get_fields($type);
			foreach ($columns as $field) {
				$result .= <<<EOF
		<th>{$field->name}</th>
EOF;
			}
			$result .= <<<EOF
	</tr>
EOF;
			foreach (Record::select_all($type) as $record) {
				$result .= <<< EOF
	<tr>
EOF;
				foreach ($columns as $field) {
					$colname = dd_get_colname($field->name);
					$value = $record[$colname];
					$html = Record::format_value($value, $field, $record["id"]);
					$result .= <<<EOF
		<td>$html</td>
EOF;
				}
				$id = $record["id"];
				$result .= <<<EOF
		<td><a href="?action=delete&amp;type=$type&amp;id=$id">Delete</a></td>
EOF;
				$classname = dd_get_classname($type);
				foreach (get_class_methods(new $classname()) as $method) {
					if (preg_match("#^action_#", $method)) {
						$result .= <<<EOF
		<td><a href="?action=$method&amp;type=$type&amp;id=$id">$method</a></td>
EOF;
					}
				}
				$result .= <<<EOF
	</tr>
EOF;
			}
			$result .= <<<EOF
</table>
EOF;
			return $result;
		}

		public static function get_fields($type) {
			global $g_dd;
			debug("g_dd=".sprint_r($g_dd));
			$fields = $g_dd[$type];
			debug("fields=".sprint_r($fields));
			array_unshift($fields,
				array("id", "primary_key"),
				array("created_t", "timestamp"),
				array("mod_t", "timestamp"));
			$result = array();
			foreach ($fields as $array) {
				$field = new Field($array);
				$result[] = $field;
			}
			return $result;
		}

		public static function select_all($type) {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM $type
ORDER BY id
EOF;
			$pst = $g_pdo->prepare($request);
			$pst->execute();

			$result = array();
			while (($record = $pst->fetch()) != null) {
				$result[] = $record;
			}
			return $result;
		}

		public static function format_value($value, $field, $id) {
			global $g_dd;
			if ($field->type == "timestamp") {
				return t2s($value, "Y/m/d-H:i:s");
			}
			if ($field->type == "html") {
				$result = <<<EOF
<a href="JavaScript:eb_show_html('html_${id}_{$field->name}')">&gt;&gt;details</a>
<div id="html_${id}_{$field->name}" style="display: none;">
	$value
</div>
EOF;
				return $result;
			}
			if (isset($g_dd[$field->type])) {
				return '<a href="?action=manage&amp;type='.$field->type.'">'.$value.'</a>';
			}
			return $value;
		}

		public function check_form() {
			// TODO: To be done.
		}

		public function hydrate_from_form() {
			global $g_dd;

			$this->created_t = time();
			$this->mod_t = $this->created_t;
			foreach ($g_dd[$this->type] as $array) {
				$field = new Field($array);
				$field->value = $_GET[$field->name];
				$this->fields[] = $field;
			}
		}

		public function store() {
			global $g_pdo;

			$other_fields = array();
			foreach ($this->fields as $field) {
				$colname = dd_get_colname($field->name);
				$other_fields[] = "`$colname`= :{$field->name}";
			}
			$rest = join(",", $other_fields);

			$request = <<<EOF
INSERT INTO {$this->type}
SET
	`id`= :id,
	`created_t`= :created_t,
	`mod_t`= :mod_t,
	$rest
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":created_t" => $this->created_t,
				":mod_t" => $this->mod_t,
			);
			foreach ($this->fields as $field) {
				$array[":".$field->name] = $field->value;
			}
			$pst->execute($array);
		}
	}
?>