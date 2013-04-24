<?php
	$event = $g_display["event"];
	$link = HOST."?action=retrieve&amp;type=event&amp;id=".$event->id;
?>
<p>
	Dear Administrator,<br/>
	A request for pubication has been sent: <br/>
	<ul>
		<li><b>Event id:</b> <?php echo $event->id; ?></li>
		<li><b>Event title:</b> <?php echo $event->title; ?></li>
		<li><b>Event link:</b> <a href="<?php echo $link; ?>"><?php echo $link; ?></a></li>
	</ul>
</p>