<?php
	require_once('include/misc.inc');
	function install() {
		try {
			debug("Starting to install");
			$login = check($_POST['login']);
			$password = check($_POST['password']);
			$host = check($_POST['host']);
			$dbname = check($_POST['dbname']);
			
			$db = new PDO("mysql:host=${host};charset=UTF-8", $login, $password);
			
			$requests = <<<EOF
CREATE DATABASE IF NOT EXISTS ${dbname} DEFAULT CHARACTER SET = 'utf8';
USE ${dbname};
CREATE TABLE `user` (`id` INT  NOT NULL, `login` VARCHAR(11) NOT NULL,
`pass` VARCHAR(11) NOT NULL, `mail` VARCHAR(11) NOT NULL, PRIMARY KEY (`id`))
EOF;
			
			$db->exec($requests);
			$content = <<<EOF
<?php
	define("MYSQL_USER", "$login");
	define("MYSQL_PASSWORD", "$password");
	define("MYSQL_HOST", "$host");
	define("MYSQL_DBNAME", "$dbname");
?>
EOF;
			
			file_put_contents(SETTINGS_INI, $content);
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
		debug("End install");
	}
	
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