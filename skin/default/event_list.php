<ul>
<?php
		foreach ($g_display["events"] as $event) {
			if (!$event->is_published() && !user_can_administrate_event($event)) {
				debug("Event not displayed.");
			} else {
?>
	<li>
		<?php echo $event->happening_t.": "; ?>
		<a href="?action=retrieve&amp;type=event&amp;id=<?php echo $event->id ?>"><?php echo $event->title ?></a>
		<?php
				if (!$event->is_published()) {
					echo "(Non Published)";
				}
		?>
	</li>
<?php
			}
		}
?>
</ul>