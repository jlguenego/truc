<?php
	require_once(BASE_DIR . '/include/misc.inc');

	// Install the web site
	function install() {
		try {
			debug("Starting to install");
			$login = check($_POST['login']);
			$password = check($_POST['password']);
			$host = check($_POST['host']);
			$dbname = check($_POST['dbname']);
			if (!check_mail($_POST["contact_email"])) {
				throw new Exception("Invalid contact mail");
			}
			$contact_email = $_POST["contact_email"];
			echo("mysql:host=${host};charset=UTF-8");

			$db = new PDO("mysql:host=${host};charset=UTF-8", $login, $password);

			$requests = <<<EOF
CREATE DATABASE IF NOT EXISTS ${dbname} DEFAULT CHARACTER SET = 'utf8';
USE ${dbname};
EOF;
			debug($requests);

			if ($db->exec($requests) === FALSE) {
				throw new Exception("DB creation: ".sprint_r($db->errorInfo()));
			};

			$db = new PDO("mysql:host=${host};dbname=${dbname};charset=UTF-8", $login, $password);
			$base_url = 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']);
			$test_mode = (isset($_POST["test_mode"])) ? "true" : "false";
			$payment_type = $_POST["payment_type"];
			$content = <<<EOF
<?php
	define("MYSQL_USER", "$login");
	define("MYSQL_PASSWORD", "$password");
	define("MYSQL_HOST", "$host");
	define("MYSQL_DBNAME", "$dbname");

	define("CONTACT_MAIL", "$contact_email");

	define('HOST', "$base_url");

	define('TEST_MODE', $test_mode);

	define('PAYMENT_PROVIDER', "$payment_type");
	require_once('payment/'.PAYMENT_PROVIDER.'/constants.inc');
?>
EOF;
			file_put_contents(SETTINGS_INI, $content);
			chmod(SETTINGS_INI, 0400);
			chmod("install.sql", 0400);
			$requests = file_get_contents("install.sql");
			$requests = str_replace("ENGINE=InnoDB", "", $requests);

			$st = $db->prepare($requests,
				array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => TRUE));
			if ($st->execute() === FALSE) {
				echo($requests.'<br/>');
				throw new Exception("Table creation: ".sprint_r($db->errorInfo())." InnoDB?");
			};

			if (!$test_mode) {
				$content = <<<EOF
Order Deny,Allow
Deny from all
EOF;
				file_put_contents("test/.htaccess", $content);
			}

			redirect_to("index.php");
		} catch (Exception $e) {
			throw $e;
		}
	}

	// Uninstal the web site
	function uninstall() {
		try {
			debug("Starting to uninstall");
			$login = MYSQL_USER;
			$password = MYSQL_PASSWORD;
			$host = MYSQL_HOST;
			$dbname = MYSQL_DBNAME;

			$pdo = new PDO("mysql:host=${host};dbname=${dbname};charset=UTF-8", $login, $password);
			$pdo->exec("DROP DATABASE IF EXISTS $dbname");
			chmod(SETTINGS_INI, 0700);
			unlink(SETTINGS_INI);
			if (!TEST_MODE) {
				unlink("test/.htaccess");
			}
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
		debug("End uninstall");
	}
?>