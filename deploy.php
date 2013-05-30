<?php
	define("BASE_DIR", ".");



	require_once(BASE_DIR . "/include/constants.inc");
	require_once(BASE_DIR . "/include/globals.inc");
	require_once(BASE_DIR . "/include/misc.inc");
	require_once(BASE_DIR . "/include/authentication.inc");
	require_once(BASE_DIR . "/include/layout.inc");
	require_once(BASE_DIR . '/include/misc.inc');
	require_once(BASE_DIR . '/include/format.inc');
	session_start();

	if (!is_installed()) {
		redirect_to("install.php");
	}
	if (admin_exists()) {
		redirect_to("index.php");
	}

	if (isset($_POST['email'])) {
		try {
			if (!check_mail($_POST["email"])) {
				throw new Exception("Invalid mail");
			}
			seq_create('quotation', 1000);
			seq_create('invoice', 10000);
			seq_create('object', 100000);
			$user = new User();
			$user->id = create_id();
			$user->email = $_POST["email"];
			$user->password = $_POST["password"];
			$user->lastname = format_lastname($_POST["lastname"]);
			$user->firstname = format_firstname($_POST["firstname"]);
			$user->role = ROLE_ADMIN;
			$user->activation_status = ACTIVATION_STATUS_ACTIVATED;
			$user->generate_activation_key();
			$user->street = $_POST["street"];
			$user->zip = $_POST["zip"];
			$user->city = $_POST["city"];
			$user->country = $_POST["country"];
			$user->state = $_POST["state"];
			$user->store();
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
<html>
	<head>
		<title>Installer</title>
		<meta charset="utf-8"/>
		<script type="text/javascript" src="jscript/misc.js"></script>
		<script type="text/javascript" src="_ext/jquery-ui-1.10.1.custom/js/jquery-1.9.1.js"></script>
		<script src="_ext/sha1.js"></script>
	</head>
	<body>
		<?php
			echo $g_error_msg;
		?>
		Please enter the admin user info:<br/>
		Profile: <span id="profile"></span>
		<script>
			var profile_array = <?php require_once("profile.json") ?>;
			var i = <?php echo $_SESSION["profile"]; ?>;
			$("#profile").html(profile_array[i].name);
		</script>
		<form name="input" action="deploy.php" method="POST">
			<table>
				<tr>
					<td>Mail: </td>
					<td><input type="email" name="email"/></td>
				</tr>
				<tr>
					<td>Firstame: </td>
					<td><input type="text" name="firstname"></td>
				</tr>
				<tr>
					<td>Lastname: </td>
					<td><input type="text" name="lastname"></td>
				</tr>
				<tr>
					<td>Password: </td>
					<td><input type="password" name="clear_password"></td>
				</tr>
				<tr>
					<td>Retype Password: </td>
					<td><input type="password" name="clear_password2"></td>
				</tr>
				<tr>
					<td>Street: </td>
					<td><input type="text" name="street"></td>
				</tr>
				<tr>
					<td>Zip: </td>
					<td><input type="text" name="zip"></td>
				</tr>
				<tr>
					<td>City: </td>
					<td><input type="text" name="city"></td>
				</tr>
				<tr>
					<td>Country: </td>
					<td><input type="text" name="country"></td>
				</tr>
				<tr>
					<td>State: </td>
					<td><input type="text" name="state"></td>
				</tr>
				<tr>
					<td><input type="submit" value="Submit"></td>
				</tr>
			</table>
			<input type="hidden" name="password" value=""/>
			<input type="hidden" name="password2" value=""/>
		</form>
		<script>
			log(profile_array);
			var hash_salt = "<?php echo RANDOM_SALT ?>";
			$(document).ready(function() {
				update_profile();
				eb_sync_hash('clear_password', 'password');
				eb_sync_hash('clear_password2', 'password2');
			});
			$("form").submit(function() {
				$('input[name*=clear_]').val("");
			});

			function update_profile() {
				log(i);
				$("input[name=email]").val(profile_array[i].admin_email);
				$("input[name=firstname]").val(profile_array[i].admin_firstname);
				$("input[name=lastname]").val(profile_array[i].admin_lastname);
				$("input[name=clear_password]").val(profile_array[i].admin_password);
				$("input[name=clear_password2]").val(profile_array[i].admin_password);
				$("input[name=street]").val(profile_array[i].admin_street);
				$("input[name=city]").val(profile_array[i].admin_city);
				$("input[name=zip]").val(profile_array[i].admin_zip);
				$("input[name=country]").val(profile_array[i].admin_country);
				$("input[name=state]").val(profile_array[i].admin_state);
			}
		</script>
	</body>
</html>
<?php
	}
	layout_trace();
?>