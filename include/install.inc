<?php
	require_once('include/misc.inc');

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

			$content = <<<EOF
<?php
	define("MYSQL_USER", "$login");
	define("MYSQL_PASSWORD", "$password");
	define("MYSQL_HOST", "$host");
	define("MYSQL_DBNAME", "$dbname");

	define("CONTACT_MAIL", "$contact_email");
	
	define('HOST', "$base_url");

?>
EOF;
			file_put_contents(SETTINGS_INI, $content);
			$requests = file_get_contents("install.sql");
			$requests = str_replace("ENGINE=InnoDB", "", $requests);

			$st = $db->prepare($requests,
				array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => TRUE));
			if ($st->execute() === FALSE) {
				echo($requests.'<br/>');
				throw new Exception("Table creation: ".sprint_r($db->errorInfo())." InnoDB?");
			};

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
			unlink(SETTINGS_INI);
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
		debug("End uninstall");
	}
?>