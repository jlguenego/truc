<?php
	require_once(BASE_DIR . "/include/constants.inc");

	function my_autoloader($class) {
		require_once(BASE_DIR . '/include/class/'.$class.'.class.php');
	}

	spl_autoload_register('my_autoloader');

	function _t($msg) {
		global $g_i18n;
		return $g_i18n->_t($msg);
	}

	function is_null_or_empty(&$var) {
		if (!isset($var)) {
			return TRUE;
		}
		if ($var == NULL) {
			return TRUE;
		}
		if ($var == "") {
			return TRUE;
		}
		return FALSE;
	}

	function is_installed() {
		if (!file_exists(TEMP_DIR)) {
			mkdir(TEMP_DIR);
		}
		if (!file_exists(SETTINGS_INI)) {
			return false;
		}
		return true;
	}

	function admin_exists() {
		try {
			global $g_pdo;

			$admin = ROLE_ADMIN;
			$request = <<<EOF
SELECT COUNT(*) FROM `user` WHERE `role`=${admin}
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute();
			$count = $q->fetch();
			return $count[0] > 0;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}

	function println($txt) {
		echo $txt . "<br/>";
	}

	function debug($msg) {
		global $g_debug;
		global $g_debug_lines;
		if ($g_debug) {
			$e = new Exception("");
			$trace_array = $e->getTrace();
			$trace = $trace_array[0];
			$function = "";
			if (isset($trace_array[1])) {
				$function = $trace_array[1]["function"];
			}
			$g_debug_lines[] = "<p>[DEBUG] ".$trace["file"].":".$trace["line"]." ".$function.
				"<br>\t".$msg."</p>";
		}

	}

	function create_id() {
		return seq_next('object');
	}

	function check($var) {
		//$var = mysql_escape_string($var);
		$var = htmlentities($var, 0, "UTF-8");
		// TODO: to implement
		return $var;
	}

	function check_mail($mail) {
		return preg_match("#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#", $mail);
	}

	function check_date($date) {
		if (!preg_match("#^[\d]{4}-[0-1][\d]-[0-3][\d]$#", $date)) {
			return FALSE;
		}
		return TRUE;
	}

	function redirect_to($path) {
		header('Location: ' . $path);
		header('Cache-Control: no-cache');
		exit(0);
	}

	function is_number($var) {
		return preg_match("#^[\d]+[\.,]?[\d]*$#", $var);
	}

	function curr($nbr) {
		return "".str_replace(",", "",number_format($nbr, 2));
	}

	function echo_default_value($name, $default = "") {
		if (!is_null_or_empty($_GET[$name])) {
			echo $_GET[$name];
		} else {
			echo $default;
		}
	}

	function default_value($name, $default = "") {
		if (!is_null_or_empty($_GET[$name])) {
			return $_GET[$name];
		} else {
			if (func_num_args() > 2) {
				$array = func_get_args();
				array_shift($array);
				foreach ($array as $value) {
					if (!is_null_or_empty($value)) {
						return $value;
					}
				}
				return "";
			} else {
				return $default;
			}
		}
	}

	function sprint_r($var) {
		ob_start();
		print_r($var);
		$output=ob_get_contents();
		ob_end_clean();
		return $output;
	}

	function seq_create($name, $init_value) {
		global $g_pdo;

		$request = <<<EOF
INSERT INTO `sequence`
SET
	`name`="${name}",
	`current`="${init_value}";
EOF;
		$st = $g_pdo->prepare($request);
		if ($st->execute() === FALSE) {
			echo($request."<br/>");
			throw new Exception("Sequence creation: ".sprint_r($g_pdo->errorInfo()));
		};
	}

	function seq_next($name) {
		global $g_pdo;

		$request = <<<EOF
SELECT `current` FROM `sequence`
WHERE `name`="${name}"
EOF;

		$st = $g_pdo->prepare($request);
		if ($st->execute() === FALSE) {
			throw new Exception("Sequence retrieve: ".sprint_r($g_pdo->errorInfo()));
		};

		$seq = $st->fetch();
		if (!isset($seq['current'])) {
			throw new Exception("No current value for sequence ".$name);
		}

		$value = $seq['current'] + 1;

		$request = <<<EOF
UPDATE `sequence`
SET `current`="${value}"
WHERE `name`="${name}"
EOF;
		$st = $g_pdo->prepare($request);
		if ($st->execute() === FALSE) {
			throw new Exception("Sequence update: ".sprint_r($g_pdo->errorInfo()));
		};
		return $value;
	}

	//string to timestamp conversion
	//s2t("2013-09-qwe v22", "%Y-%m-qwe v%d");
	function s2t($string, $format = "%Y-%m-%d") {
		list($hour, $minute, $second, $month, $day, $year) = array(0, 0, 0, 0, 0, 0);
		$i = 0;
		$j = 0;
		while ($i < strlen($format)) {
			$c_format = substr ($format , $i, 1);
			$c_string = substr ($string , $j, 1);
			if ($c_format != "%") {
				//echo "$c_format | $c_string<br/>";
				if ($c_format != $c_string) {
					throw new Exception("String and format do not match. format=" .
						$format . " and string=" . $string);
				}
				$i++;
				$j++;
			} else {
				$i++;
				$c_format = substr ($format , $i, 1);
				switch ($c_format) {
					case "Y":
						$year = substr($string, $j, 4);
						//echo $year;
						$j += 4;
						break;
					case "m":
						$month = substr($string, $j, 2);
						$j += 2;
						break;
					case "d":
						$day = substr($string, $j, 2);
						$j += 2;
						break;
					default:
						throw new Exception("Escaped character not understood. c=" . $c_format);
				}
				$i++;
			}
		}
		$result = mktime($hour, $minute, $second, $month, $day, $year);
		return $result;
	}

	function t2s($timestamp, $format = "Y-m-d") {
		// TODO: revisiter la fonction
		return date($format, $timestamp);
	}

	function default_str($val1, $val2) {
		if (is_null_or_empty($val1)) {
			return $val2;
		}
		return $val1;
	}

	# for the attributes (specially in forms)
	function _a($str) {
		return htmlentities($str, ENT_COMPAT, "UTF-8");
	}

	function dd() {
		global $g_dd;
		return $g_dd;
	}

	function ls($dirname) {
		$result = array();
		 foreach(scandir($dirname) as $file) {
			if ('.' === $file || '..' === $file) {
				continue;
			}
			$result[] = $file;
	    }
		return $result;
	}

	function rmdir_recursive($dir) {
		if (!is_dir($dir)) {
			if (is_file($dir)) {
				unlink("$dir");
			}
			return;
		}
	    foreach(scandir($dir) as $file) {
			if ('.' === $file || '..' === $file) {
				continue;
			}
			if (is_dir("$dir/$file")) {
				rmdir_recursive("$dir/$file");
			} else {
				unlink("$dir/$file");
			}
	    }
	    if (is_dir($dir)) {
	    	rmdir($dir);
	    }
	}

	function our_mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = true) {
		$first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
		$str_end = "";
		if ($lower_str_end) {
			$str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
		}
		else {
			$str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
		}
		$str = $first_letter . $str_end;
		return $str;
	}
?>