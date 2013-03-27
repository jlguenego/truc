<?php

	class Form {
		public $action;
		public $method = "POST";
		public $elements = array();
		public $css = "form";
		public $title = "";

		public function add_text($label, $name, $default, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "text";
			$item->name = $name;
			$item->default = $default;
			$item->help = $help;
			$this->elements[] = $item;
			return $item;
		}

		public function add_select($label, $name, $html_spec, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "select";
			$item->name = $name;
			$item->html_spec = $html_spec;
			$item->help = $help;
			$this->elements[] = $item;
			return $item;
		}

		public function add_email($label, $name, $default, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "email";
			$item->name = $name;
			$item->default = $default;
			$item->help = $help;
			$this->elements[] = $item;
			return $item;
		}

		public function add_number($label, $name, $default, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "number";
			$item->name = $name;
			$item->default = $default;
			$item->help = $help;
			$this->elements[] = $item;
			return $item;
		}

		public function add_textarea($label, $name, $default, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "textarea";
			$item->name = $name;
			$item->default = $default;
			$item->help = $help;
			$this->elements[] = $item;
			return $item;
		}

		public function add_password($label, $name, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "password";
			$item->name = $name;
			$item->help = $help;
			$this->elements[] = $item;
			return $item;
		}

		public function add_checkbox($label, $name, $checked, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "checkbox";
			$item->name = $name;
			$item->html_spec = $checked;
			$item->help = $help;
			$this->elements[] = $item;
			return $item;
		}

		public function add_submit($label = "Submit") {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "submit";
			$this->elements[] = $item;
			return $item;
		}

		public function add_hidden($name, $value) {
			$item = new FormItem();
			$item->type = "hidden";
			$item->name = $name;
			$item->default = $value;
			$this->elements[] = $item;
			return $item;
		}

		public function add_raw_html($html) {
			$item = new FormItem();
			$item->type = "raw_html";
			$item->html_spec = $html;
			$this->elements[] = $item;
			return $item;
		}

		public function html() {

			$result = <<<EOF
<span class="{$this->css}_title">{$this->title}</span>
<form class="{$this->css}" action="{$this->action}" method="{$this->method}">
EOF;
			$autofocus = "autofocus";
			foreach ($this->elements as $item) {
				switch ($item->type) {
					case "raw_html":
						$result .= $item->html_spec;
						break;
					case "hidden":
						$result .= <<<EOF
<input type="hidden" name="{$item->name}" value="{$item->default}"/>
EOF;
						break;
					case "text":
						$result .= <<<EOF
<div class="{$this->css}_label">{$item->label}</div>
<input type="text" id="{$item->name}" name="{$item->name}" value="{$item->default}" {$item->other_attr} $autofocus/>
<div class="{$this->css}_help">{$item->help}</div>
EOF;
						break;
					case "email":
						$result .= <<<EOF
<div class="{$this->css}_label">{$item->label}</div>
<input type="email" name="{$item->name}" value="{$item->default}"/>
<div class="{$this->css}_help">{$item->help}</div>
EOF;
						break;
					case "number":
						$result .= <<<EOF
<div class="{$this->css}_label">{$item->label}</div>
<input type="number" name="{$item->name}" value="{$item->default}"/>
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
					case "textarea":
						$result .= <<<EOF
<div class="{$this->css}_label">{$item->label}</div>
<textarea name="{$item->name}" maxlength="{$item->maxlength}" title="Up to {$item->maxlength} characters">{$item->default}</textarea>
<div class="{$this->css}_help">{$item->help}</div>
EOF;
						break;
					case "select":
						$result .= <<<EOF
<div class="{$this->css}_label">{$item->label}</div>
<select name="{$item->name}">
{$item->html_spec}
</select>
<div class="{$this->css}_help">{$item->help}</div>
EOF;
						break;
					case "checkbox":
						$result .= <<<EOF
<input type="checkbox" name="{$item->name}" {$item->html_spec}/>{$item->label}<br/>
<div class="{$this->css}_help">{$item->help}</div>
EOF;
						break;

					case "submit":
						$result .= "<input type=\"submit\" value=\"".$item->label."\"/>";
						break;
					default:
				}
				$autofocus = "";
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
		public $maxlength;
		public $default;
		public $html_spec;
		public $other_attr;
	}
?>