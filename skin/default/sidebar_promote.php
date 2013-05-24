<?php
	$event = Event::get_from_id($_SESSION["event_id"]);
?>
<a href="?action=retrieve&type=event&id=<?php echo $_SESSION["event_id"]; ?>">Event title: <?php echo $event->title; ?></a>
<br/><br/>
<div class="evt_sidebar">
	<ul>
		<li><a href="?action=promote_event&amp;id=<?php echo $event->id ?>">{{Help}}</a></li>
		<li><a href="?action=manage&type=guest">Manage guests</a></li>
		<li><a href="?action=manage&type=advertisement">Manage advertisements</a></li>
		<li><a href="?action=manage&type=task">Manage tasks</a></li>
		<li><a href="?action=run_tasks">Run tasks</a></li>
	</ul>
</div>