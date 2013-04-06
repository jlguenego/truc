<?php
	define("BASE_DIR", ".");
	require_once(BASE_DIR . '/include/install.inc');
	require_once(BASE_DIR . '/include/misc.inc');
	session_start();

	if (is_installed()) {
		redirect_to("index.php");
	}

	$error_msg = '';
	if (isset($_POST['login'])) {
		try {
			$_SESSION["profile"] = $_POST["profile"];
			install();
			$install_done = <<<EOF
<html>
	<head>
		<title>Install Done</title>
	</head>
	<body>
		<a href="index.php">Go to index</a>
	</body>
<html>
EOF;
			println($install_done);
		} catch (Exception $e) {
			println("Install failed: " . $e->getMessage());
		}
	} else {
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Installer</title>
		<script type="text/javascript" src="jscript/misc.js"></script>
		<script type="text/javascript" src="ext/jquery-ui-1.10.1.custom/js/jquery-1.9.1.js"></script>
	</head>
	<body>
		<?php
			echo $error_msg;
		?>
		<form action="" method="POST">
			<select name="profile">
			</select>
		</form>

		Please enter the database connection parameters:
		<form name="input" action="install.php" method="POST">
			<table>
				<tr>
					<td>Username: </td>
					<td><input type="text" name="login"></td>
				</tr>
				<tr>
					<td>Password: </td>
					<td><input type="password" name="password"></td>
				</tr>
				<tr>
					<td>Database Name: </td>
					<td><input type="text" name="dbname"></td>
				</tr>
				<tr>
					<td>Hostname: </td>
					<td><input type="text" name="host"></td>
				</tr>
				<tr>
					<td>Contact mail: </td>
					<td><input type="email" name="contact_email"></td>
				</tr>
				<tr>
					<td>Payment type:</td>
					<td>
						<select name="payment_type">
							<option value="paypal">Paypal</option>
							<option value="paypal_sandbox">Paypal Sandbox</option>
							<option value="intern">Intern</option>
						</select>
					</td>
				</tr>
				<tr>
					<td><input type="checkbox" name="test_mode" value="true"/>Test Mode</td>
				</tr>
				<tr>
					<td><input type="submit" value="Submit"></td>
				</tr>
				<input type="hidden" name="profile"/>
			</table>
		</form>
		<script>
			var profile_array = <?php require_once("profile.json") ?>;
			console.log(profile_array);
			var combo = "";
			$(document).ready(function() {
				for (i in profile_array) {
					combo += '<option value="' + i + '">' + profile_array[i].name + '</option>';
				}
				$("select[name=profile]").html(combo);
			});
			$("select[name=profile]").change(update_profile);
			$("select[name=profile]").ready(update_profile);

			function update_profile() {
				var i = $(this).val();
				if (!i) {
					i = 0;
				}
				console.log(i);
				$("input[name=login]").val(profile_array[i].MYSQL_USER);
				$("input[name=password]").val(profile_array[i].MYSQL_PASSWORD);
				$("input[name=host]").val(profile_array[i].MYSQL_HOST);
				$("input[name=dbname]").val(profile_array[i].MYSQL_DBNAME);
				$("input[name=contact_email]").val(profile_array[i].CONTACT_MAIL);
				$("select[name=payment_type]").val(profile_array[i].PAYMENT_PROVIDER);
				if (profile_array[i].TEST_MODE) {
					$("input[name=test_mode]").attr("checked", "checked");
				} else {
					$("input[name=test_mode]").removeAttr("checked");
				}
				$("input[name=profile]").val(i);
			}
		</script>
	</body>
</html>
<?php
	}
?>