<?php
	$event = $g_display["event"];
	$tickets = $g_display["tickets"];
	$user = $g_display["user"];
?>
<div class="evt_title"><p><?php echo format_participate_title($event); ?></p></div>
{{Event:}} <?php echo $event->title; ?>
{{How many ticket do you want?}}
<form name="input" action="?action=participate&amp;event_id=<?php echo $event->id; ?>" method="POST">
	<table class="evt_table inline">
		<tr>
			<th>{{Rate}}</th>
			<th>{{Unit price}} (€)</th>
			<th>{{Quantity}}</th>
			<th>{{Total tax excluded}} (€)</th>
			<th>{{Tax}} (%)</th>
			<th>{{Tax amount}} (€)</th>
			<th>{{Total due}} (€)</th>
		</tr>
		<?php
			$i = 0;
			$tax_array = array();
			foreach ($tickets as $ticket) {
				$event_title = $event->title;
				$amount_ht = str_replace(",", "", curr($ticket->amount));
				$label = $ticket->name;
				$tax_rate = $ticket->tax_rate;
				$ticket_id = $ticket->id;
				$remaining = $ticket->get_remaining();
				if (!in_array($tax_rate, $tax_array)) {
					$tax_array[] = $tax_rate;
				}
		?>
		<tr>
			<td><?php echo $label; ?></td>
			<td id="unit_price_<?php echo $i; ?>" class="evt_curr"><?php echo $amount_ht; ?></td>
			<td>
				<input id="<?php echo $i; ?>_amount" type="number" min="1" max="<?php echo $remaining; ?>" name="ticket_<?php echo $i; ?>" value="<?php echo_default_value("ticket_${i}", 1); ?>" data-id="<?php echo $i; ?>" data-type="quantity"/><br/>
				<span id="max_quantity_info_<?php echo $i; ?>" class="form_help">{{Max:}} <?php echo $remaining; ?></span>
			</td>
			<td id="total_ht_<?php echo $i; ?>" class="evt_curr">0.00</td>
			<td id="tax_rate_<?php echo $i; ?>" class="evt_curr"><?php echo $tax_rate; ?></td>
			<td id="tax_amount_<?php echo $i; ?>" class="evt_curr">0.00</td>
			<td id="ttc_<?php echo $i; ?>" class="evt_curr">0.00</td>
		</tr>
		<?php
				$i++;
			}
			debug(sprint_r($tax_array));
		?>
		<tr>
			<th class="th_left" colspan="4">Total</th>
			<td id="sub_total" class="evt_curr">0.00</td>
		</tr>
	</table>

	<table id="tickets" class="evt_table inline">
		<tr>
			<th></th>
			<th>{{Amount}} (€)</th>
			<th>{{Taxes}} (€)</th>
			<th>{{Due}} (€)</th>
		</tr>
		<?php
			$i2 = 0;
			foreach ($tax_array as $tax_rate) {
		?>
		<tr>
			<th class="th_left">{{Tax}} (<?php echo $tax_rate; ?>%)</td>
			<td id="tax_base_<?php echo $i2; ?>" class="evt_curr">0.00</td>
			<td id="tax_total_<?php echo $i2; ?>" class="evt_curr">0.00</td>
			<td id="tax_total_due_<?php echo $i2; ?>" class="evt_curr">0.00</td>
		</tr>
		<?php
				$i2++;
			}
		?>
		<tr>
			<th class="th_left" colspan="2">{{Total taxes}}</td>
			<td id="tax_total" class="evt_curr">0.00</td>
		</tr>
		<tr>
			<th class="th_left" colspan="3"><b>{{Total due}}</b></td>
			<td id="total_due" class="evt_curr"><b>0.00</b></td>
		</tr>
	</table>

	<table id="total" class="evt_table_billing inline">
		<tr>
			<th class="th_left">{{Billing Entity name}}</th>
			<td><input type="text" name="username" value="<?php echo $user->firstname.' '.$user->lastname; ?>"/></td>
			<td class="help">{{The person or organisation name to be charged.}}</td>
		</tr>
		<tr>
			<th class="th_left" rowspan="5">{{Billing address}}</th>
			<td>
				<input type="text" name="address_street" value="<?php echo $user->street; ?>" placeholder="{{Street# and street name}}"/>
			</td>
			<td class="help">{{Street# and street name}}</td>
		</tr>
		<tr>
			<td>
				<input type="text" name="address_city" value="<?php echo $user->city; ?>" placeholder="{{City}}"/>
			</td>
			<td class="help">{{City}}</td>
		</tr>
		<tr>
			<td>
				<input type="text" name="address_zip" value="<?php echo $user->zip; ?>" placeholder="{{ZIP code}}"/>
			</td>
			<td class="help">{{ZIP code}}</td>
		</tr>
		<tr>
			<td>
				<select name="address_country">
					<?php echo form_get_country_options(default_value("country", $user->country)); ?>
				</select>
			</td>
			<td class="help">{{Country}}</td>
		</tr>
		<tr>
			<td>
				<input type="text" name="state" value="<?php echo $user->state; ?>" placeholder="{{State (optional)}}"/>
			</td>
			<td class="help">{{State (optional)}}</td>
		</tr>
		<tr>
			<th>VAT number (if applicable)</th>
			<td>
				<input type="text" name="vat" value="" placeholder="{{For EU companies only.}}"/>
			</td>
			<td class="help">{{For EU companies only.}}</td>
		</tr>
	</table>
	<input type="hidden" name="address" value=""/>
	<input type="checkbox" name="confirm"/> {{I have read the <a href="info/sales">Sales policies</a> and accept them.}}<br/><br/>
	<span class="form_cancel">
		<input type="button" class="evt_button evt_btn_small evt_btn_cancel" onclick="window.location='?action=retrieve&amp;type=event&amp;id=<?php echo $event->id; ?>'" value="{{Cancel}}" />
	</span>
	<span class="spacer"></span>
	<input class="evt_button evt_btn_small" type="submit" name="next" value="{{Next}}" disabled/>
</form>
<script>
	var max_reached = "{{Max quantity reached}}";
	var rate_nbr = <?php echo $i; ?>;
	var tax_nbr = <?php echo $i2; ?>;
	var taxes = new Array(
		<?php
			$is_first = TRUE;
			$i = 0;
			foreach ($tax_array as $tax_rate) {
				if ($is_first) {
					$is_first = FALSE;
				} else {
					echo ',';
				}
				echo "new Array('${tax_rate}', '${i}')";
				$i++;
			}
		?>
	);
	$('input[data-type="quantity"]').change(eb_sync_amount);
	$('input[data-type="quantity"]').keyup(eb_sync_amount);
	$('input[data-type="quantity"]').each(eb_sync_amount);

	$('input[type=checkbox]').ready(eb_sync_next_button);
	$('input[type=checkbox]').change(eb_sync_next_button);



	function eb_sync_address() {
		var state = $("input[name='state']").val();
		if (!state) {
			state = "";
		} else {
			state += " ";
		}
		$("input[name='address']").val(
			$("input[name='address_street']").val() + "\n" +
			state + $("input[name='address_zip']").val() + " " +
			$("input[name='address_city']").val() + " " +
			$("select[name='address_country']").val()
		);
		log($("input[name='address']").val());
	}

	function eb_sync_next_button() {
		eb_sync_address();
		var test = $('input[type=checkbox]').is(':checked');

		$("input[name*='address_']").each(function(){
			if ($(this).val() == "") {
				test = false;
			}
		});
		if (test) {
			$('input[name=next]').removeAttr('disabled');
		} else {
			$('input[name=next]').attr('disabled', 'disabled');
		}
	}
</script>