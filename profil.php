<?php
	require_once("include/user.inc");
	require_once("include/event.inc");
	
	if (!isset($_GET["id"])) {
		redirect_to("index.php");
	}
	
	$user = get_user($_GET["id"]);
	$lastname = strtoupper($user['lastname']);
	$name = ucfirst($user['name']);
?>
<html>
	<head>
		<title><?php echo "Profil of $lastname $name"; ?></title>
	</head>
	
	<a href="index.php">Go back to index</a><br/><br/>
	
<?php 
	echo "Welcome to the profil of $lastname $name<br/>\n";
	if ($user['login'] == $_SESSION['login']) {
		echo "<a href=\"editprofile.php?id=".$user['id']."\">Edit your profil</a><br/>\n";
	}
	$user_events = user_events($_GET["id"]);
	echo "<h3>Events organize by $lastname $name</h3>\n";
	echo "<ul>\n";
	foreach ($user_events as $event) {
		echo "<li>";
		echo date("d M Y", $event["event_date"]).": ";
		echo "<a href=\"event.php?id=".$event['id']."\">".$event['title']."</a>";
		echo "</li>\n";
	}
	echo "</ul>\n";
?>
	
</html>