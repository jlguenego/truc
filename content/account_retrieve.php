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
		echo "<a href=\"?action=update&amp;type=account\">Edit your profile</a><br/>\n";
	}
	echo "<h3>Events organized by $lastname $name</h3>\n";
	echo "<ul>\n";
	foreach (user_events($_GET["id"]) as $event) {
		echo "<li>";
		echo date("d M Y", $event["event_date"]).": ";
		echo "<a href=\"?action=retrieve&amp;type=event&amp;id=".$event['id']."\">".$event['title']."</a>";
		echo "</li>\n";
	}
	echo "</ul>\n";
	
	echo "<h3>You are participating to:</h3>\n";
	echo "<ul>\n";
	$user_part = user_participations($_GET["id"]);
	foreach ($user_part as $participation) {
		$event = get_event($participation["id_event"]);
		echo "<li>";
		echo date("d M Y", $event["event_date"]).": ";
		echo "<a href=\"?action=retrieve&amp;type=event&amp;id=".$event['id']."\">".$event['title']."</a> (".$participation["quantity"]." tickets)";
		echo "</li>\n";
	}
	echo "</ul>\n";
?>
	
</html>