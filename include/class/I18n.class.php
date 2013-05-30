<?php
	class I18n {
		public $locale;
		public $array = array();

		public function init($locale = null) {
			global $g_default_locale;

			if ($locale != null) {
				$this->locale = $locale;
			} else {
				if (is_null_or_empty($_SESSION["locale"])) {
					$_SESSION["locale"] = I18n::guess_locale();
				}
				if (is_null_or_empty($_SESSION["locale"])) {
					$_SESSION["locale"] = $g_default_locale;
				}
				$this->locale = $_SESSION["locale"];
			}
			debug("load with locale: ".$this->locale);
			$this->load();
		}

		public function load() {
			$filename = BASE_DIR."/locale/".$this->locale."/messages.php";
			if (!file_exists($filename)) {
				debug("Locale file does not exist.");
				return;
			}
			require($filename);
			debug("array=".sprint_r($this->array));
		}

		public function parse($str) {
			debug("Parsing page.");
			$str = preg_replace_callback('/[{][{](.*?)[}][}]/', array($this, "gettext"), $str);
			return $str;
		}

		public function get_mail_html($filename) {
			global $g_display;
			ob_start();
			include($this->filename(BASE_DIR."/mail/header.php"));
			include($this->filename($filename));
			include($this->filename(BASE_DIR."/mail/footer.php"));
			$result = ob_get_contents();
			ob_end_clean();
			debug("Mail=".$result);
			return $this->parse($result);
		}

		public function filename($filename) {
			$result = preg_replace("/([.][^.]*?)$/", ".".$this->locale."$1", $filename);
			debug("looking for filename: ".$result);
			debug("input=".$filename);
			debug("output=".$result);
			if (file_exists($result)) {
				return $result;
			}
			return $filename;
		}

		public function gettext($array) {
			return $this->_t($array[1]);
		}

		public function _t($msg) {
			$array = $this->array;
			if (isset($array[$msg])) {
				return $array[$msg];
			}
			return preg_replace("/^\[.*?\]/", "", $msg);
		}

		public static function guess_locale() {
			return I18n::preffered_browser_language();
		}

		public static function preffered_browser_language()
		{
			$result = "";
			$max_coef = 0;
			$langs = explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
			foreach($langs as $lang)
			{
				$array = explode(";",$lang);
				$codelang = $array[0];
				$coef = null;
				if (count($array) > 1) {
					$coef = $array[1];
				}
				if($coef == null) $coef = 1;
				if($coef > $max_coef)
				{
					$result = substr($codelang,0,2);
					$max_coef = $coef;
				}
			}
			return $result;
		}

		public static function get_os_locale() {
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				if ($_SESSION["locale"] == "fr") {
					return 'fra';
				}
			} else {
				if ($_SESSION["locale"] == "fr") {
					return 'fr_FR';
				}
			}
			return $_SESSION["locale"];
		}

		public static function menu() {
			global $g_locales, $g_default_locale;
			$array_link = array();
			foreach ($g_locales as $locale) {
				if ($locale == $_SESSION["locale"]) {
					$array_link[] = "$locale";
				} else {
					$array_link[] = <<<EOF
<a href="?action=set_locale&amp;locale=$locale">$locale</a>
EOF;
				}
			}
			$array_locale = "[&nbsp;" . strtolower(join("&nbsp;|&nbsp;", $array_link)) . "&nbsp;]";
			$result = <<<EOF
$array_locale
EOF;
			echo $result;
		}
	}
?>