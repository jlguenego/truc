<?php
	$event = $g_display["event"];
	$organizer = $g_display["author"];

	echo "<div class=\"evt_title\"><p>".$event->title."</p></div>";

	if ($event->is_inactivated()) {
?>
	<div id="evt_retrieve_publish" class="evt_shadowed">
		{{This event is inactivated.}}
	</div>
<?php
	}
	if (!$event->is_published() && !$event->is_inactivated()) {
?>
<div id="evt_retrieve_publish" class="evt_shadowed">
	{{Your event is not published.}}
<?php
		if (!$event->is_ready_for_publication()) {
?>
	<br/>{{Please click}} <a href="?action=request_for_publication&amp;id=<?php echo $event->id; ?>">{{here}}</a> {{to request its publication to our support.}}
<?php
		} else {
?>
	<br/>{{A request for publication has been done. Our support team is going to process it very soon.}}
<?php
	}
?>
</div>
<?php
	}
	if (is_admin_logged() && !$event->is_inactivated()) {
?>
<div id="evt_administration" class="evt_shadowed">
	<div class="evt_administration_title evt_admin">
		Event Biller Admin
	</div>
	<div class="evt_administration_body">
		<ul>
			<li>Organizer: <a href="?action=retrieve&amp;type=account&amp;id=<?php echo $organizer->id; ?>"><?php echo $organizer->email; ?></a></li>
		</ul><br/>
		<br/>
<?php
		$publish_button_grey = "";
		if ($event->status == EVENT_STATUS_INACTIVATED
			|| (!$event->is_ready_for_publication())) {
			$publish_button_grey = "disabled";
		}
		if (!$event->is_published()) {
?>
			<form action="?action=publish_event&amp;id=<?php echo $event->id ?>" method="POST">
				<input type="submit" value="Publish event" <?php echo $publish_button_grey ?>/>
			</form>
<?php
		} else {
?>
			<form name="unpublish" action="?action=unpublish_event&amp;id=<?php echo $event->id ?>" method="POST">
				<input type="submit" value="Unpublish event" <?php echo $publish_button_grey ?>/>
				<input type="hidden" name="reason" />
			</form>
			<div id="dialog" style="display: none;" title="Unpublish reason">
				<textarea id="dialog_textarea">The reason is ...</textarea>
			</div>
<?php
		}
?>
	</div>
</div>
<?php
	}
	if ($event->can_be_administrated() && !$event->is_inactivated()) {
?>
<div id="evt_administration" class="evt_shadowed">
	<div class="evt_administration_title">
		{{Administration}}
	</div>
	<div class="evt_administration_body">
		<ul>
<?php
		if ($event->is_published()) {
?>
			<li>{{This event is published.}}</li>
<?php
		} else {
?>
			<li>{{This event is not published.}}</li>
<?php
		}
		echo "<li>".$event->funding_acquired."€/".$event->funding_needed."€ "._t("funding acquired").".</li>";
		if ($event->type == EVENT_TYPE_NOMINATIVE) {
?>
			<li>{{Tickets indicate attendee name.}}</li>
<?php
		} else {
?>
			<li>{{Tickets do not indicate attendee name.}}</li>
<?php
		}
?>
		<li>{{Organizer phone}}: <?php echo $event->phone; ?></li>
		<li><a href="?action=promote_event&amp;id=<?php echo $event->id ?>">{{Promote your event}}</a></li>
<?php
		if ($event->type == EVENT_TYPE_NOMINATIVE) {
?>
			<li><a href="?action=list&amp;type=participation&amp;id=<?php echo $event->id ?>">{{View registrations}}</a></li>
<?php
		}
?>
		<li><a href="?action=get_form&amp;type=event&amp;id=<?php echo $event->id ?>">{{Edit event}}</a></li>
		<li><a href="?action=delete&amp;type=event&amp;id=<?php echo $event->id ?>">{{Delete event}}</a></li>
		</ul>
	</div>
</div>
<?php
	}
	echo $event->short_description;
?>
<br/>
<?php
	if ($event->can_be_administrated()) {
?>
<?php
	}
	$tickets = $event->get_tickets();
?>

<table class="evt_rate_table">
	<tr>
		<td class="evt_rate_title" colspan="5">{{Tickets}}</td>
<?php
		if ($event->can_participate()) {
?>
		<td class="evt_participate" rowspan="<?php echo (count($tickets)+2); ?>">
			<a href="?action=get_form&amp;type=participation&amp;event_id=<?php echo $event->id ?>">
				<button class="evt_button"><?php echo format_participate_button($event); ?></button>
			</a>
		</td>
<?php
		} else if ($event->get_remaining_tickets_amount() <= 0) {
?>
		<td class="evt_participate" rowspan="<?php echo (count($tickets)+2); ?>">
			{{Sorry, but all tickets were sold...}}
		</td>
<?php
		}
?>
	</tr>
		<tr>
			<th>{{Categories}}</th>
			<th>{{Unit price}}</th>
			<th>{{Taxes}}</th>
			<th>{{Total due}}</th>
			<th>{{Remaining}}</th>
		</tr>
<?php
	foreach ($tickets as $ticket) {
		$tax = $ticket->tax_rate;
		$label = $ticket->name;
		$amount_ht = curr($ticket->amount);
		$amount_ttc = curr($amount_ht * (($tax/100) + 1));
		$remaining = $ticket->get_remaining();
		if ($remaining <= 0) {
			$remaining = 0;
		}
?>
		<tr>
			<td class="evt_category"><?php echo $label ?></td>
			<td class="evt_curr"><?php echo curr($amount_ht) ?>€</td>
			<td class="evt_curr"><?php echo curr($tax) ?>%</td>
			<td class="evt_curr"><?php echo curr($amount_ttc) ?>€</td>
			<td class="evt_curr"><?php echo $remaining ?></td>
		</tr>
<?php
	}
?>
</table>
<?php
	if ($event->can_be_administrated()) {
?>
<div id="evt_status" class="evt_shadowed">
<?php
		if ((time()) >= s2t($event->happening_t, "%Y-%m-%d") + 86400) {
?>
				{{This event has already happened.}}
<?php
		} else if ($event->is_confirmed()) {
?>
				{{This event is confirmed. It will happen!}}
<?php
		} else if ($event->is_cancelled()) {
?>
				{{Unfortunately this event is cancelled.}}
<?php
		} else {
?>
				{{This event needs to be confirmed to happen. More people needed.}}
<?php
		}
?>
</div>
<?php
	}
	debug("event->happening_t=".$event->happening_t);
?>
<div id="evt_general_info">
	<div class="evt_general_info_title">
		{{General informations}}
	</div>
	<div class="evt_general_info_body">
		<ul>
			<li><b>{{Organizer}}: </b><?php echo $event->organizer_name; ?></li>
			<li><b>{{Location}}:</b> <?php echo $event->location; ?></li>
			<li><b>{{Happening date}}:</b> <?php echo format_date($event->happening_t); ?></li>
			<li><b>{{Confirmation date}}:</b> <?php echo format_date($event->get_confirmation_date()); ?></li>
			<li><b>{{Event website}}:</b> <a href="<?php echo $event->link; ?>"><?php echo $event->link; ?></a></li>
		</ul>
	</div>
</div>
<br/>
<?php
	echo $event->long_description;
?>
<?php echo facebook_comment(); ?>
<script>
	$(document).ready(eb_unpublish);
</script>