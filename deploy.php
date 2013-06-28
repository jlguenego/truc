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

			$address = new Address();
			$address->lat = $_POST["address_lat"];
			$address->lng = $_POST["address_lng"];
			$address->street_number = $_POST["address_street_number"];
			$address->route = $_POST["address_route"];
			$address->postal_code = $_POST["address_postal_code"];
			$address->locality = $_POST["address_locality"];
			$address->administrative_area_level_2 = $_POST["address_administrative_area_level_2"];
			$address->administrative_area_level_1 = $_POST["address_administrative_area_level_1"];
			$address->country = $_POST["address_country"];
			$address->store();

			$user->address_id = $address->id;
			$user->store();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Install Done</title>
	</head>
	<body>
		<a href="index.php">Go to index</a>
	</body>
<html>
<?php
		} catch (Exception $e) {
			println("Install failed: " . $e->getMessage());

			echo '<pre>';
			print_r($e);
			echo '</pre>';
		}
	} else {
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Installer</title>
		<meta charset="utf-8"/>
		<script src="_ext/jquery-ui-1.10.3.custom/js/jquery-1.9.1.js"></script>
		<script src="_ext/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.js"></script>
		<link href="_ext/jquery-ui-1.10.3.custom/css/ui-lightness/jquery-ui-1.10.3.custom.css" rel="stylesheet">
		<!-- ADDRESS PICKER START -->
		<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script src="_ext/jquery-addresspicker/src/jquery.ui.addresspicker.js"></script>
		<!-- ADDRESS PICKER END -->
		<script src="_ext/sha1.js"></script>
		<script src="jscript/misc.js"></script>
		<script src="jscript/eb_addresspicker.js"></script>
		<link rel="stylesheet" href="skin/default/default.css">
		<script>
		</script>
	</head>
	<body>
		<?php
			echo $g_error_msg;
		?>
		<a href="support/restore_db.php">Restore the database</a><br/>
		<br/>
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
					<td>Address:</td>
					<td><input type="text" class="addresspicker" name="address"/></td>
				</tr>
				<tr>
					<td><input type="submit" value="Submit"></td>
					<td></td>
				</tr>
			</table>
			<input type="hidden" name="password" value=""/>
			<input type="hidden" name="password2" value=""/>
		</form>

		<script>
			var hash_salt = "<?php echo RANDOM_SALT ?>";
			$(document).ready(function() {
				update_profile();
				addresspicker_init();
				eb_sync_hash('clear_password', 'password');
				eb_sync_hash('clear_password2', 'password2');
			});
			$("form").submit(function() {
				$('input[name*=clear_]').val("");
				$(this).find($('input')).each(function() {
					log($(this).attr('name') + ": " + $(this).val());
				});
			});

			function update_profile() {
				$("input[name=email]").val(profile_array[i].admin_email);
				$("input[name=firstname]").val(profile_array[i].admin_firstname);
				$("input[name=lastname]").val(profile_array[i].admin_lastname);
				$("input[name=clear_password]").val(profile_array[i].admin_password);
				$("input[name=clear_password2]").val(profile_array[i].admin_password);
				$("input[name=address]").val(profile_array[i].admin_address);
			}
		</script>
<?php
	layout_trace();
?>
	</body>
</html>
<?php
	}
?>