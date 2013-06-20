<?php
	$event = $g_display["event"];
	$organizer = $g_display["author"];
	$blocks = array();

	$block = new Block();
	$block->side = 'right';
	$block->title = 'Short description';
	$block->content = $event->short_description;
	$blocks[] = $block;

	if (is_admin_logged() && !$event->is_inactivated()) {
		$content = <<<EOF
<ul>
	<li>Organizer: <a href="?action=retrieve&amp;type=account&amp;id={$organizer->id}">{$organizer->email}</a></li>
</ul><br/>
<br/>

EOF;
		$publish_button_grey = "";
		if ($event->status == EVENT_STATUS_INACTIVATED
			|| (!$event->is_ready_for_publication())) {
			$publish_button_grey = "disabled";
		}
		if (!$event->is_published()) {
			$content .= <<<EOF
<form action="?action=publish_event&amp;id={$event->id}" method="POST">
	<input type="submit" value="Publish event" ${publish_button_grey}/>
</form>
EOF;
		} else {
			$content .= <<<EOF
<form name="unpublish" action="?action=unpublish_event&amp;id={$event->id}" method="POST">
	<input type="submit" value="Unpublish event" ${publish_button_grey}/>
	<input type="hidden" name="reason" />
</form>
<div id="dialog" style="display: none;" title="Unpublish reason">
	<textarea id="dialog_textarea">The reason is ...</textarea>
</div>
EOF;
		}
		$block = new Block();
		$block->side = 'left';
		$block->title = 'Event Biller Admin';
		$block->content = $content;
		$block->css_class = 'evt_administration';
		$blocks[] = $block;
	}

	if ($event->can_be_administrated() && !$event->is_inactivated()) {
		$content = <<<EOF
<ul>
EOF;
		if ($event->is_published()) {
			$content .= '<li>{{This event is published.}}</li>';
		} else {
			$content .= '<li>{{This event is not published.}}</li>';
		}

		$content .= '<li>'.$event->funding_acquired.'€/'.$event->funding_needed.'€ '._t('funding acquired').'.</li>';
		$id = $event->id;
		if ($event->type == EVENT_TYPE_NOMINATIVE) {
			$content .= '<li>{{Tickets indicate attendee name.}}</li>';
		} else {
			$content .= '<li>{{Tickets do not indicate attendee name.}}</li>';
		}

		$phone = $event->phone;
		$content .= <<<EOF
	<li>{{Organizer phone}}: ${phone}</li>
	<li><a href="?action=promote_event&amp;id=${id}">{{Promote your event}}</a></li>
EOF;
		if ($event->type == EVENT_TYPE_NOMINATIVE) {
			$content .= '<li><a href="?action=list&amp;type=participation&amp;id='.$id.'">{{View registrations}}</a></li>';
		}

		$content .= <<<EOF
	<li><a href="?action=get_form&amp;type=event&amp;id=${id}">{{Edit event}}</a></li>
	<li><a href="?action=delete&amp;type=event&amp;id=${id}">{{Delete event}}</a></li>
</ul>
EOF;
		$block = new Block();
		$block->side = 'left';
		$block->title = 'Administration';
		$block->content = $content;
		$block->css_class = 'evt_administration';
		$blocks[] = $block;
	}

	$ticket_table = <<<EOF
<table class="evt_rate_table">
	<tr>
		<td class="evt_participate" colspan="5">
EOF;
	if ($event->can_participate()) {
		$id = $event->id;
		$button_label = format_participate_button($event);

		$ticket_table .= <<<EOF
			<a href="?action=get_form&amp;type=participation&amp;event_id=${id}">
				<button class="evt_button">${button_label}</button>
			</a>
EOF;
	} else if ($event->get_remaining_tickets_amount() <= 0) {
		$ticket_table .= <<<EOF
			{{Sorry, but all tickets were sold...}}
EOF;
	}
	$ticket_table .= <<<EOF
		</td>
	<tr>
	<tr>
		<td class="evt_rate_title" colspan="5">{{Tickets}}</td>
EOF;
		$tickets = $event->get_tickets();
		$rowspan = (count($tickets)+2);

		$ticket_table .= <<<EOF
	</tr>
	<tr>
		<th>{{Categories}}</th>
		<th>{{Unit price}}</th>
		<th>{{Taxes}}</th>
		<th>{{Total due}}</th>
		<th>{{Remaining}}</th>
	</tr>
EOF;
	foreach ($tickets as $ticket) {
		$tax = curr($ticket->tax_rate);
		$label = $ticket->name;
		$amount_ht = curr($ticket->amount);
		$amount_ttc = curr($amount_ht * (($tax/100) + 1));
		$remaining = $ticket->get_remaining();
		if ($remaining <= 0) {
			$remaining = 0;
		}
		$ticket_table .= <<<EOF
	<tr>
		<td class="evt_category">${label}</td>
		<td class="evt_curr">${amount_ht}€</td>
		<td class="evt_curr">${tax}%</td>
		<td class="evt_curr">${amount_ttc}€</td>
		<td class="evt_curr">${remaining}</td>
	</tr>
EOF;
	}
	$ticket_table .= <<<EOF
</table>
EOF;
	$block = new Block();
	$block->side = 'right';
	$block->title = 'Tickets';
	$block->content = $ticket_table;
	$blocks[] = $block;

	$url = '/event/'.$event->id.'/'.str_replace("+", "-", urlencode($event->title));
	$block = new Block();
	$block->side = 'right';
	$block->title = 'Comment on Facebook';
	$block->content = facebook_comment($url);
	$blocks[] = $block;

	$confirmation_date = format_date($event->get_confirmation_date());
	$happening_t = format_date($event->happening_t);
	$general_info = <<<EOF
<ul>
	<li><b>{{Organizer}}<br/></b>{$event->organizer_name}</li>
	<li><b>{{Happening date}}<br/></b>${happening_t}</li>
	<li><b>{{Confirmation date}}<br/></b>${confirmation_date}</li>
	<li><b>{{Event website}}<br/></b><a href="{$event->link}">{$event->link}</a></li>
</ul>
EOF;
	$block = new Block();
	$block->side = 'left';
	$block->title = 'General informations';
	$block->content = $general_info;
	$blocks[] = $block;

	$address = $event->location;
	$location = <<<EOF
<iframe
	width="278"
	height="300"
	frameborder="0"
	scrolling="no"
	marginheight="0"
	marginwidth="0"
	src="https://maps.google.fr/maps?safe=off
		&amp;q=${address}
		&amp;ie=UTF8
		&amp;hq=
		&amp;hnear=${address}
		&amp;gl=fr
		&amp;t=m
		&amp;z=14
		&amp;output=embed
		&amp;iwloc=near">
</iframe><br/>
<br/>
<b>{{Location}}</b><br/>
${address}
EOF;
	$block = new Block();
	$block->side = 'left';
	$block->title = 'Location';
	$block->content = $location;
	$blocks[] = $block;

	$block = new Block();
	$block->side = 'right';
	$block->title = 'Long description';
	$block->content = $event->long_description;
	$blocks[] = $block;
?>

<div class="evt_title"><p><?php echo $event->title; ?></p></div>

<?php
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
	echo format_columns($blocks)
?>
<div style="clear: both;"></div>