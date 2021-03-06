<?php
	$event = $g_display["event"];
	$organizer = $g_display["author"];
	$blocks = array();

	$block = new Block();
	$block->side = 'right';
	$block->title = 'Short description';
	$block->content = nl2br($event->short_description);
	$blocks[] = $block;

	if (is_admin_logged() && !$event->is_inactivated()) {
		$content = <<<EOF
<ul>
	<li>Organizer: <a href="?action=retrieve&amp;type=account&amp;id={$organizer->id}">{$organizer->email}</a></li>
</ul><br/>
<br/>
EOF;
		$bill_id = $event->get_organizer_invoice_id();
		if ($bill_id != null) {
			$content = <<<EOF
<a href="?action=retrieve&amp;type=bill&amp;id=${bill_id}">{{Get Invoice}}</a>
EOF;
		} else {
			$content = <<<EOF
<a href="?action=generate&amp;type=invoice&amp;id={$event->id}"><button class="evt_button evt_btn_small">{{Generate Invoice}}</button></a>
EOF;
		}
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

	if ($event->is_organized_by() && $event->is_published()) {
		$img_uri = HOST.'/'.SKIN_DIR.'/images/FacebookLogo.png';
		$content = <<<EOF
<ul>
	<li>
		<img src="${img_uri}" alt="Facebook logo"/>&nbsp;&nbsp;
EOF;
		if (!$event->has_flag(EVENT_FLAG_FACEBOOK_EVENT_CREATED)) {
			$content .= <<<EOF
<a href="?action=create&amp;type=facebook_event&amp;id={$event->id}">{{Declare this event on Facebook}}</a>
EOF;
		} else {
			$content .= <<<EOF
<a href="https://www.facebook.com/events/{$event->facebook_event_id}" target="_blank">{{See your event on Facebook}}</a>
EOF;
		}
		$content .= <<<EOF
	</li>
</ul>
EOF;
		$block = new Block();
		$block->side = 'left';
		$block->title = 'Social Networking';
		$block->content = $content;
		$block->css_class = 'evt_administration';
		$blocks[] = $block;
	}

	if ($event->can_be_administrated() && !$event->is_inactivated()) {
		$content = "";
		if ($event->is_inactivated()) {
			$content .= <<<EOF
<div id="evt_retrieve_publish" class="evt_shadowed">
	{{This event is inactivated}}
</div>
EOF;
		}
		if (!$event->is_published() && !$event->is_inactivated()) {
			$content .= <<<EOF
<div id="evt_retrieve_publish" class="evt_shadowed">
	{{Your event is not published.}}
EOF;
			if (!$event->is_ready_for_publication()) {
			$content .= <<<EOF
	<br/>
	{{When ready, please}}
	<a href="?action=request_for_publication&amp;id={$event->id}">
		{{request its publication to our support.}}
	</a>
EOF;
			} else {
				$content .= <<<EOF
	<br/>
	{{A request for publication has been done. Our support team is going to process it very soon.}}
EOF;
			}
		$content .= <<<EOF
</div>
EOF;
		}
		$content .= <<<EOF
<ul>
EOF;
		if ($event->is_published()) {
			$content .= <<<EOF
	<li>{{This event is published.}}</li>
EOF;
		} else {
			$content .= '<li>{{This event is not published.}}</li>';
		}
		$report = deal_generate_report($event);
		$content .= '<li>'.curr($report['total']).'€/'.$event->funding_needed.'€ '._t('funding acquired').'.</li>';
		$id = $event->id;
		if ($event->type == EVENT_TYPE_NOMINATIVE) {
			$content .= '<li>{{Tickets indicate attendee name.}}</li>';
		} else {
			$content .= '<li>{{Tickets do not indicate attendee name.}}</li>';
		}

		$phone = $event->phone;
		if (!is_null_or_empty($phone)) {
			$content .= <<<EOF
	<li>{{Organizer phone}}: ${phone}</li>
EOF;
		}
		$billing_address = Address::get_from_id($event->billing_address_id);
		$typed_billing_address = nl2br($billing_address->address);
		$content .= <<<EOF
	<li>{{Billing:}} <br/>{$event->organizer_name}<br/>${typed_billing_address}</li>
EOF;

		if ($event->type == EVENT_TYPE_NOMINATIVE) {
			$content .= '<li><a href="?action=list&amp;type=participation&amp;id='.$id.'">{{View registrations}}</a></li>';
		}

		$content .= <<<EOF
	<li><a href="?action=generate&amp;type=report&amp;id=${id}">{{View report}}</a></li>
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

		$discounts_html = <<<EOF
<table class="evt_table inline" >
	<tr>
		<th>{{Code}}</th>
		<th>{{Expiration date}}</th>
		<th>{{Rule}}</th>
	</tr>
EOF;
		foreach ($event->get_discounts() as $discount) {
			$rule = '-'.$discount->amount.'€';
			if ($discount->class == DISCOUNT_CLASS_PERCENTAGE) {
				$rule = '-'.$discount->percentage.'%';
			}
			$discounts_html .= <<<EOF
	<tr>
		<td>{$discount->code}</td>
		<td>{$discount->expiration_t}</td>
		<td>${rule}</td>
	</tr>
EOF;
		}
		$discounts_html .= <<<EOF
</table>
EOF;
		$block = new Block();
		$block->side = 'left';
		$block->title = 'Discounts';
		$block->content = $discounts_html;
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

		$ticket_table .= <<<EOF
			<a href="?action=get_form&amp;type=participation&amp;event_id=${id}">
				<button class="evt_button">{{Order tickets!}}</button>
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

	if ($event->is_published()) {
		$block = new Block();
		$block->side = 'right';
		$block->title = 'Comment on Facebook';
		$block->content = facebook_comment($event->get_url());
		$blocks[] = $block;
	}

	$confirmation_date = format_date($event->get_confirmation_date());
	$happening_t = format_date($event->happening_t);
	$generale_info = <<<EOF
<ul>
	<li><b>{{Organizer}}<br/></b>{$event->organizer_name}</li>
	<li><b>{{Confirmation date}}<br/></b>${confirmation_date}</li>
EOF;
	if (url_exists($event->link)) {
		$generale_info .= <<<EOF
	<li><b>{{Event website}}<br/></b><a href="{$event->link}" target="_blank">{$event->link}</a></li>
EOF;
	}
	$generale_info .= <<<EOF
</ul>
EOF;
	$block = new Block();
	$block->side = 'left';
	$block->title = 'General informations';
	$block->content = $generale_info;
	$blocks[] = $block;

	$address = Address::get_from_id($event->location_address_id);
	$typed_address = nl2br($address->address);
	$location = <<<EOF
<div id="event_location_map" class="map"></div>
<br/>
<b>{{Location}}</b>
<hr/>
${typed_address}<br/>
EOF;
	if (TEST_MODE) {
		$google_address = $address->google_address();
		$location .=<<<EOF
<br/>
<span class="gray">${google_address}</span>
EOF;
	}
	$block = new Block();
	$block->side = 'left';
	$block->title = 'Location';
	$block->content = $location;
	$blocks[] = $block;

	$happening = format_date($event->happening_t);
	$happening_t = date("d-m-Y", s2t($event->happening_t));
	$event_url = urlencode($event->get_url());
	$date_content = <<<EOF
${happening}<br/>
<br/>
<a href="${event_url}" title="Add to Calendar" class="addthisevent">
    {{Add to Calendar}}
    <span class="_start">${happening_t} 00:00:00</span>
    <span class="_end">${happening_t} 23:59:59</span>
    <span class="_zonecode">40</span>
    <span class="_summary">{$event->title}</span>
    <span class="_description">{{More informations at}} ${event_url}</span>
    <span class="_location">{$event->location()}</span>
    <span class="_organizer">{$event->organizer_name}</span>
    <span class="_all_day_event">true</span>
    <span class="_date_format">DD/MM/YYYY</span>
</a>
EOF;
	$block = new Block();
	$block->side = 'left';
	$block->title = 'Happening date';
	$block->content = $date_content;
	$blocks[] = $block;

	$block = new Block();
	$block->side = 'right';
	$block->title = 'Long description';
	$block->content = $event->long_description;
	$blocks[] = $block;
?>

<div class="evt_title"><p><?php echo $event->title; ?></p></div>

<?php
	echo format_columns($blocks)
?>
<div style="clear: both;"></div>
<script>
	$(document).ready(function () {
		var lat = <?php echo $address->lat; ?>;
		var lng = <?php echo $address->lng; ?>;
		var map_canvas = document.getElementById('event_location_map');
		var map_options = {
			center: new google.maps.LatLng(lat, lng),
			zoom: 15,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		var map = new google.maps.Map(map_canvas, map_options);
		var latLng = new google.maps.LatLng(lat, lng);
		var marker = new google.maps.Marker({
			position: latLng,
			map: map,
		});
	});
</script>