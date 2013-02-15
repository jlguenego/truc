<?php
	require_once("include/user.inc");
	require_once("include/event.inc");
	require_once("include/manage.inc");
	
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
	echo "Welcome to the profile of $lastname $name<br/>\n";
	if ($user['login'] == $_SESSION['login']) {
		echo "<a href=\"editprofilee.php?id=".$user['id']."\">Edit your profile</a><br/>\n";
	}
	echo "<h3>Events organize by $lastname $name</h3>\n";
	echo "<ul>\n";
	foreach (user_events($_GET["id"]) as $event) {
		echo "<li>";
		echo date("d M Y", $event["event_date"]).": ";
		echo "<a href=\"event.php?id=".$event['id']."\">".$event['title']."</a>";
		echo "</li>\n";
	}
	echo "</ul>\n";
	
	echo "<h3>You participating to:</h3>\n";
	echo "<ul>\n";
	$user_part = user_participations($_GET["id"]);
	foreach ($user_part as $participation) {
		$event = get_event($participation["id_event"]);
		echo "<li>";
		echo date("d M Y", $event["event_date"]).": ";
		echo "<a href=\"event.php?id=".$event['id']."\">".$event['title']."</a> (".$participation["quantity"]." tickets)";
		echo "</li>\n";
	}
	echo "</ul>\n";
?>
	
</html>