<?php
	function mail_html_publication_request($event) {
		$link = HOST."?action=retrieve&amp;type=event&amp;id=".$event->id;
		ob_start();
		include("header.php");
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
<?php
		include("footer.php");
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
?>