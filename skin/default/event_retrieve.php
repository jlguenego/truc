<?php
	$event = $g_display["event"];
	$author = $g_display["author"];
	echo "<h1 class=\"evt_title\">".$event->title."</h1>";
	echo $event->short_description;
?>
<br/>
<?php
	if (user_can_administrate_event($event)) {
?>
<div id="evt_administration">
	<div class="evt_administration_title">
		Administration
	</div>
	<div class="evt_administration_body">
		<ul>
<?php
		if ($event->is_published()) {
?>
			<li>This event is published.</li>
<?php
		} else {
?>
			<li>This event is not published.</li>
<?php
		}
		echo "<li>".$event->funding_acquired."€/".$event->funding_needed."€ funding acquired.</li>";
?>
		<li><a href="?action=list&amp;type=participation&amp;id=<?php echo $event->id ?>">View registrations</a></li>
		<li><a href="?action=get_form&amp;type=event&amp;id=<?php echo $event->id ?>">Edit event</a></li>
		<li><a href="?action=delete&amp;type=event&amp;id=<?php echo $event->id ?>">Delete event</a></li>
		</ul>
	</div>
</div>
<?php
	}
	$rates = events_rates($event->id);
?>

<table class="evt_rate_table">
	<tr>
		<td class="evt_rate_title" colspan="4">Rates</td>
<?php
		if ($event->can_participate()) {
?>
		<td class="evt_participate"rowspan="<?php echo (count($rates)+2); ?>">
			<a href="?action=get_form&amp;type=participation&amp;event_id=<?php echo $event->id ?>">
				<button>Participate</button>
			</a>
		</td>
<?php
	}
?>
	</tr>
		<tr>
			<th>Categories</th>
			<th>Unit price</th>
			<th>Taxes</th>
			<th>Total due</th>
		</tr>
<?php
	foreach ($rates as $rate) {
		$tax = $rate['tax_rate'];
		$label = $rate['label'];
		$amount_ht = curr($rate['amount']);
		$amount_ttc = curr($amount_ht * (($tax/100) + 1));
?>
		<tr>
			<td class="evt_category"><?php echo $label ?></td>
			<td class="evt_curr"><?php echo curr($amount_ht) ?>€</td>
			<td class="evt_curr"><?php echo curr($tax) ?>%</td>
			<td class="evt_curr"><?php echo curr($amount_ttc) ?>€</td>
		</tr>
<?php
	}
?>
</table>
<div id="evt_status">
			<?php
	if ((time() + 86400) >= s2t($event->happening_t, "%Y-%m-%d")) {
?>
			This event has already happened.
<?php
	} else if ($event->is_confirmed()) {
?>
			Will append. Enough persons have registered.
<?php
	} else if ($event->is_cancelled()) {
?>
			This event is cancelled.
<?php
	} else {
?>
			This event needs to be confirmed to happen. More people needed.
<?php
	}
	debug("event->happening_t=".$event->happening_t);
?>
</div>
<div id="evt_general_info">
	<div class="evt_general_info_title">
		General informations
	</div>
	<div class="evt_general_info_body">
		<ul>
			<li><b>Organizer: </b><?php echo $event->organizer_name; ?></li>
			<li><b>Location:</b> <?php echo $event->location; ?></li>
			<li><b>Happening date:</b> <?php echo format_date($event->happening_t); ?></li>
			<li><b>Confirmation date:</b> <?php echo format_date($event->confirmation_t); ?></li>
			<li><b>Participation opening date:</b> <?php echo format_date($event->open_t); ?></li>
			<li><b>Event website:</b> <a href="<?php echo $event->link; ?>"><?php echo $event->link; ?></a></li>
		</ul>
	</div>
</div>
<br/>
<?php
	echo $event->long_description;
?>