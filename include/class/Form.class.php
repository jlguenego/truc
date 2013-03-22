<?php

	class Form {
		public $action;
		public $method = "POST";
		public $elements = array();
		public $css = "form";

		public function add_text($label, $name, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "text";
			$item->name = $name;
			$item->help = $help;
			$this->elements[] = $item;
		}

		public function add_password($label, $name, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "password";
			$item->name = $name;
			$item->help = $help;
			$this->elements[] = $item;
		}

		public function add_submit($label = "Submit") {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "submit";
			$this->elements[] = $item;
		}

		public function html() {
			$result = <<<EOF
<form class="{$this->css}" action="{$this->action}" method="{$this->method}">
EOF;
			foreach ($this->elements as $item) {
				switch ($item->type) {
					case "text":
						$result .= <<<EOF
<div class="{$this->css}_label">{$item->label}</div>
<input type="text" name="{$item->name}"/>
<div class="{$this->css}_help">{$item->help}</div>
EOF;
						break;
						case "password":
						$result .= <<<EOF
<div class="{$this->css}_label">{$item->label}</div>
<input type="password" name="{$item->name}"/>
<div class="{$this->css}_help">{$item->help}</div>
EOF;
						break;
					case "submit":
						$result .= "<input type=\"submit\" name=\"".$item->label."\"/>";
						break;
					default:
				}
			}
			$result .= "</form>";
			return $result;
		}
	}

	class FormItem {
		public $label;
		public $type;
		public $name;
		public $help;
	}
?>