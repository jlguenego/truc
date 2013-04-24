<?php
	$user = $g_display["user"];
	$event = $g_display["event"];
	$link = HOST."?action=retrieve&amp;type=event&amp;id=".$event->id;
?>
<p>
	Dear <?php echo $user->get_name(); ?>,<br/>
	Your event (<a href="<?php echo $link; ?>"><?php echo $event->title; ?></a>) has been published.<br/>
</p>