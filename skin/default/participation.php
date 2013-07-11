<?php
	$event = $g_display["event"];
	$tickets = $g_display["tickets"];
	$user = $g_display["user"];

	$tax_array = array();
	foreach ($tickets as $ticket) {
		$tax_rate = $ticket->tax_rate;
		if (!in_array($tax_rate, $tax_array)) {
			$tax_array[] = $tax_rate;
		}
	}
	arsort($tax_array);
	debug(sprint_r($tax_array));

	function command_nominative($tickets, $event, $user) {
?>
<table class="evt_table inline">
	<tr>
		<th>{{Categories}}</th>
		<th>{{Remaining}}</th>
		<th>{{Rate}} (€)</th>
		<th>{{Tax}}</th>
		<th>{{Total}} (€)</th>
	</tr>
<?php
		foreach ($tickets as $ticket) {
			$event_title_js = addslashes($event->title);
			$event_title = $event->title;
			$amount_ht = curr($ticket->amount);
			$label = $ticket->name;
			$tax_rate = $ticket->tax_rate;
			$ticket_id = $ticket->id;
			$amount_ttc = number_format($amount_ht * (($tax_rate/100) + 1), 2);
			$disable = "";
			$remaining = $ticket->get_remaining();
			if ($remaining <= 0) {
				$disable = "disabled";
			}
?>
	<tr>
		<td><?php echo $label; ?></td>
		<td id="<?php echo $ticket->id; ?>_remaining"><?php echo $remaining; ?></td>
		<td class="evt_curr"><?php echo $amount_ht; ?></td>
		<td class="evt_curr"><?php echo $tax_rate; ?>%</td>
		<td class="evt_curr"><?php echo $amount_ttc; ?></td>
		<td><input type="button" id="add_btn_<?php echo $ticket->id; ?>" value="{{Add}}" <?php echo $disable; ?> /></td>
	</tr>
<script>
$(function() {
	$('#add_btn_<?php echo $ticket->id; ?>').click(function() {
		eb_add_nominative_ticket(<?php echo "'$event_title_js', '$label', $amount_ht, $tax_rate, $ticket_id"; ?>);
		return false;
	});
});
</script>
<?php
		} // end foreach
?>
</table>

<table id="tickets" style="display:none;" class="evt_table inline">
	<tr>
		<th colspan="5">{{Event details}}</th>
		<th colspan="3">{{Attendee details}}</th>
	</tr>
	<tr>
		<th>{{Rate}}</th>
		<th>{{Unit price}} (€)</th>
		<th>{{Tax rate}}</th>
		<th>{{Tax amount}} (€)</th>
		<th>{{Due}} (€)</th>
		<th>{{Title}}</th>
		<th>{{Firstname}}</th>
		<th>{{Lastname}}</th>
	</tr>
</table>
<script>
	var ticket_remaining = {};
	<?php
		foreach ($tickets as $ticket) {
			$remaining = $ticket->get_remaining();
			echo "ticket_remaining[{$ticket->id}] = ${remaining};";
		}
	?>

	log(ticket_remaining);

	var ticket_counter = 0;

	function eb_add_nominative_ticket(event, label, amount_ht, tax, ticket_id) {
		ticket_remaining[ticket_id]--;
		eb_sync_add_btn();

		ticket_counter++;
		eb_display_tables();

		var tax_amount = amount_ht * (tax/100);
		var amount_ttc = amount_ht + tax_amount;
		var id = new Date().getTime();
		var content = 	"<tr id=\"ticket_" + id + "\">" +
							"<td>" +
								label +
								"<input type=\"hidden\" name=\"labels[]\" value=\"" + label + "\"/>" +
								"<input type=\"hidden\" name=\"ticket_ids[]\" value=\"" + ticket_id + "\" />" +
							"</td>" +
							"<td class=\"evt_curr\" data-type=\"amount_ht\">" +
								eb_curr(amount_ht) +
								"<input type=\"hidden\" name=\"amount_ht[]\" value=\"" + eb_curr(amount_ht) + "\"/>" +
							"</td>" +
							"<td class=\"evt_curr\" data-type=\"tax\" data-value=\"" + eb_curr(tax).replace(".", "_") + "\">" +
								eb_curr(tax) + "%" +
								"<input type=\"hidden\" name=\"taxes[]\" value=\"" + eb_curr(tax) + "\"/>" +
							"</td>" +
							"<td class=\"evt_curr\" data-type=\"tax_amount\">" + eb_curr(tax_amount) + "</td>" +
							"<td class=\"evt_curr\">" + eb_curr(amount_ttc) + "</td>" +
							"<td>" +
								"<select name=\"titles[]\">" +
									"<option value=\"\"></option>" +
									"<option value=\"Mr\">{{Mr}}</option>" +
									"<option value=\"Mme\">{{Ms}}</option>" +
								"</select>" +
							"</td>" +
							"<td><input type=\"text\" name=\"firstnames[]\" placeholder=\"mandatory\" value=\"<?php echo_default_value('firstname', $user->firstname); ?>\"/></td>" +
							"<td><input type=\"text\" name=\"lastnames[]\" placeholder=\"mandatory\" value=\"<?php echo_default_value('lastname', $user->lastname); ?>\"/></td>" +
							"<td><input type=\"button\" value=\"Remove\" onClick=\"eb_remove_nominative_ticket('ticket_" + id + "', " + ticket_id + ")\"/></td>" +
						"</tr>";
		$("#tickets").append(content);
		eb_update_total();
		$('input[type="text"]').keyup(eb_sync_next_button);
		eb_sync_next_button();
	}

	function eb_remove_nominative_ticket(id, ticket_id) {
		ticket_counter--;
		$("#" + id).remove();
		ticket_remaining[ticket_id]++;
		eb_sync_add_btn();

		if (ticket_counter <= 0) {
			$("#tickets").css("display", "none");
		}
		eb_update_total();
		eb_sync_next_button();
	}

	function eb_sync_add_btn() {
		for (ticket_id in ticket_remaining) {
			log("remaining for " + ticket_id + ": " + ticket_remaining[ticket_id]);
			$('#' + ticket_id + '_remaining').html(ticket_remaining[ticket_id]);

			if (ticket_remaining[ticket_id] <= 0) {
				log("disable ticket: " + ticket_id);
				$("#add_btn_" + ticket_id).attr('disabled', '');
			} else {
				log("enable ticket: " + ticket_id);
				$("#add_btn_" + ticket_id).removeAttr('disabled');
			}
		}
	}

	function eb_display_tables() {
		$("#tickets").removeAttr("style");
	}

	function eb_sync_next_button_spec() {
		var test = true;
		var c = $("input[name='lastnames[]']").length;
		test = test && (c > 0);
		$("input[name='firstnames[]']").each(function(){
			if ($(this).val() == "") {
				test = false;
			}
		});
		$("input[name='lastnames[]']").each(function(){
			if ($(this).val() == "") {
				test = false;
			}
		});
		return test;
	}
</script>
<?php
	}

	function command_regular($tickets, $event, $tax_array) {
?>
<table id="tickets" class="evt_table inline">
	<tr>
		<th>{{Ticket rate}}</th>
		<th>{{Unit price}} (€)</th>
		<th>{{Quantity}}</th>
		<th>{{Total tax excluded}} (€)</th>
		<th>{{Tax}} (%)</th>
		<th>{{Tax amount}} (€)</th>
		<th>{{Total due}} (€)</th>
	</tr>
	<?php
		$i = 0;
		foreach ($tickets as $ticket) {
			$event_title = $event->title;
			$amount_ht = str_replace(",", "", curr($ticket->amount));
			$label = $ticket->name;
			$tax_rate = $ticket->tax_rate;
			$remaining = $ticket->get_remaining();
	?>
	<tr id="ticket_<?php echo $i; ?>">
		<td><?php echo $label; ?></td>
		<td id="unit_price_<?php echo $i; ?>" class="evt_curr">
			<?php echo $amount_ht; ?>
		</td>
		<td>
			<input id="<?php echo $i; ?>_amount" type="number" min="0"
				max="<?php echo $remaining; ?>" name="ticket_<?php echo $i; ?>"
				value="<?php echo_default_value("ticket_{$ticket->id}", 0); ?>"
				data-id="<?php echo $i; ?>" data-type="quantity"
			/>
			<br/>
			<span id="max_quantity_info_<?php echo $i; ?>" class="form_help">
				{{Max:}} <?php echo $remaining; ?>
			</span>
		</td>
		<td id="total_ticket_ht_<?php echo $i; ?>" class="evt_curr" data-type="amount_ht">0.00</td>
		<td id="tax_rate_<?php echo $i; ?>" class="evt_curr" data-type="tax" data-value="<?php echo str_replace('.', '_', $tax_rate); ?>">
			<?php echo $tax_rate; ?>
		</td>
		<td id="tax_amount_<?php echo $i; ?>" class="evt_curr" data-type="tax_amount">0.00</td>
		<td id="ttc_<?php echo $i; ?>" class="evt_curr">0.00</td>
	</tr>
	<?php
			$i++;
		}
	?>
	<tr>
		<th class="th_left" colspan="3">Total</th>
		<td id="sub_total" class="evt_curr">0.00</td>
	</tr>
</table>

<script>
	var rate_nbr = <?php echo count($tickets); ?>;
	var max_reached = "{{Max quantity reached}}";
	var tax_nbr = <?php echo count($tax_array); ?>;
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
	$(function() {
		$('input[data-type="quantity"]').change(eb_sync_amount);
		$('input[data-type="quantity"]').keyup(eb_sync_amount);
		$('input[data-type="quantity"]').each(eb_sync_amount);
	});

	function eb_sync_amount() {
		var quantity = Math.abs($(this).val());

		var max_quantity = $(this).attr('max');
		if (quantity > max_quantity) {
			quantity = max_quantity;
		}
		$(this).val(quantity);

		var id = $(this).attr('data-id');
		var unit_price = $('#unit_price_' + id).html();
		var total_ht = quantity * unit_price;
		log($('#total_ticket_ht_' + id));
		$('#total_ticket_ht_' + id).html(eb_curr(total_ht));

		var tax_rate = $('#tax_rate_' + id).html();
		var tax_amount = eb_curr((tax_rate/100) * total_ht);
		$('#tax_amount_' + id).html(tax_amount);

		var ttc = parseFloat(tax_amount) + parseFloat(total_ht);
		log(tax_amount + '+' + total_ht + '=' + ttc);
		$('#ttc_' + id).html(eb_curr(ttc));

		var sub_total = 0;
		for (i = 0; i < rate_nbr; i++) {
			var current = $('#total_ticket_ht_' + i).html();
			sub_total = parseFloat(sub_total) + parseFloat(current);
		}
		$('#sub_total').html(eb_curr(sub_total));

		eb_update_total();
		eb_sync_next_button();
	}

	function eb_sync_next_button_spec() {
		var quantity_test = false;
		$("input[id*='_amount']").each(function(){
			if ($(this).val() > 0) {
				quantity_test = true;
			}
		});
		return quantity_test;
	}
</script>
<?php
	}

	function command_total($tickets, $event, $tax_array) {
?>
<!-- TOTAL -->
<table class="evt_table inline">
	<tr>
		<th></th>
		<th>{{Amount}} (€)</th>
		<th>{{Taxes}} (€)</th>
		<th>{{Due}} (€)</th>
	</tr>

<?php
	foreach ($tax_array as $tax_rate) {
		$tax_rate_id = str_replace(".", "_", $tax_rate);
?>
	<tr id="row_total_<?php echo $tax_rate_id; ?>">
		<th class="th_left">{{Tax}} (<?php echo $tax_rate; ?>%)</th>
		<td class="evt_curr" id="total_ht_<?php echo $tax_rate_id; ?>">0.00</td>
		<td class="evt_curr" id="total_tax_<?php echo $tax_rate_id; ?>">0.00</td>
		<td class="evt_curr" id="total_ttc_<?php echo $tax_rate_id; ?>">0.00</td>
	</tr>
<?php
	}
?>
	<tr>
		<th class="th_left" colspan="2">{{Total taxes}}</td>
		<td id="total_tax" class="evt_curr">0.00</td>
	</tr>
	<tr>
		<th class="th_left" colspan="3"><b>{{Total due}}</b></td>
		<td id="total_due" class="evt_curr"><b>0.00</b></td>
	</tr>
</table>
<?php
	}

	function command_billinfo($user) {
?>
<table class="evt_table_billing inline">
	<tr>
		<th class="th_left">{{Billing Entity name}}</th>
		<td><input type="text" name="client_name" value="<?php echo $user->get_company_name(); ?>"/></td>
		<td class="help">{{The person or organisation name to be charged.}}</td>
	</tr>
	<tr>
		<th class="th_left">{{Billing address}}</th>
		<td>
			<textarea class="addresspicker" name="billing_address"" placeholder="{{Street no, Street, Zip City, etc.}}"><?php echo default_value('billing_address', $user->address()); ?></textarea>
		</td>
		<td class="help">{{Street no, Street, Zip City, etc.}}</td>
	</tr>
	<tr>
		<th>{{VAT number (if applicable)}}</th>
		<td>
			<input type="text" name="client_vat" value="<?php echo default_value('client_vat', $user->vat); ?>" placeholder="{{For EU companies only.}}"/>
		</td>
		<td class="help">{{For EU companies only.}}</td>
	</tr>
</table>
<?php
	}
?>

<div class="evt_title"><p>{{Order tickets}}</p></div>
{{Event:}} <?php echo $event->title; ?>

<form name="input" action="?action=participate&amp;event_id=<?php echo $event->id; ?>" method="POST">

<?php
	if ($event->type == EVENT_TYPE_NOMINATIVE) {
		command_nominative($tickets, $event, $user);
	} else { // EVENT_TYPE_REGULAR
		command_regular($tickets, $event, $tax_array);
	}

	command_total($tickets, $event, $tax_array);
	command_billinfo($user);
?>
	<input type="hidden" name="participation_type" value="<?php echo $event->type; ?>"/>
	<input type="checkbox" name="confirm"/> {{I have read the <a href="info/sales" target="_blank">Sales policies</a>, the <a href="info/terms" target="_blank">Terms and Conditions</a> and accept them.}}<br/>
	<br/>
	<span class="form_cancel">
		<input type="button" class="evt_button evt_btn_small evt_btn_cancel" onclick="window.location='?action=retrieve&amp;type=event&amp;id=<?php echo $event->id; ?>'" value="{{Cancel}}" />
	</span>
	<span class="spacer"></span>
	<input class="evt_button evt_btn_small" type="submit" name="next" value="{{Next}}" disabled/>
</form>

<script>
	$('input[type=checkbox]').ready(eb_sync_next_button);
	$('input').change(eb_sync_next_button);
	$('textarea').change(eb_sync_next_button);
	$(document).ready(addresspicker_init);

	function eb_sync_next_button() {
		var test = true;
		var sales_policies_test = $('input[type=checkbox]').is(':checked');
		test &= sales_policies_test;

		test &= eb_sync_next_button_spec();

		if ($("textarea[name='billing_address']").val() == "") {
			test = false;
		}

		if (test) {
			$('input[name=next]').removeAttr('disabled');
		} else {
			$('input[name=next]').attr('disabled', 'disabled');
		}
	}

	function eb_update_total() {
		$('[id*="total_tax_"]').html("0.00");
		$('[id*="total_ht_"]').html("0.00");
		$('[id*="total_ttc_"]').html("0.00");

		$('#total_tax').html("0.00");
		$('#total_due').html("0.00");

		$('#tickets').find($('tr[id*="ticket_"]')).each(function() {
			var amount_ht = $(this).find($("td[data-type='amount_ht']")).html();
			var tax = $(this).find($("td[data-type='tax']")).attr("data-value");
			var tax_amount = $(this).find($("td[data-type='tax_amount']")).html();

			var total_tax = eb_curr(parseFloat($("#total_tax_" + tax).html()) + parseFloat(tax_amount));
			$("#total_tax_" + tax).html(total_tax);
			var total_ht = eb_curr(parseFloat($("#total_ht_" + tax).html()) + parseFloat(amount_ht));
			$("#total_ht_" + tax).html(total_ht);
			var total_ttc = eb_curr(parseFloat($("#total_ttc_" + tax).html()) + parseFloat(amount_ht) + parseFloat(tax_amount));
			$("#total_ttc_" + tax).html(total_ttc);

			var total_taxes = eb_curr(parseFloat($("#total_tax").html()) + parseFloat(tax_amount));
			$("#total_tax").html(total_taxes);
			var total_due = eb_curr(parseFloat($("#total_due").html()) + parseFloat(amount_ht) + parseFloat(tax_amount));
			$("#total_due").html(total_due);
		});

		$('td[id*="total_ht_"]').each(function() {
			var id = $(this).attr("id");
			var tax_id = id.replace("total_ht_", "");
			var total_ht = parseFloat($(this).html());
		});
	}
</script>