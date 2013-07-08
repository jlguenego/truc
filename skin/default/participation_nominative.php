<?php
	$event = $g_display["event"];
	$tickets = $g_display["tickets"];
	$user = $g_display["user"];
?>
<div class="evt_title"><p><?php echo format_participate_title($event); ?></p></div>
{{Event:}} <?php echo $event->title; ?>
<table class="evt_table inline">
	<tr>
		<th>{{Categories}}</th>
		<th>{{Remaining}}</th>
		<th>{{Rate}} (€)</th>
		<th>{{Tax}}</th>
		<th>{{Total}} (€)</th>
	</tr>
	<?php
		$tax_array = array();
		foreach ($tickets as $ticket) {
			$event_title_js = addslashes($event->title);
			$event_title = $event->title;
			$amount_ht = curr($ticket->amount);
			$label = $ticket->name;
			$tax_rate = $ticket->tax_rate;
			$ticket_id = $ticket->id;
			if (!in_array($tax_rate, $tax_array)) {
				$tax_array[] = $tax_rate;
			}
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
		<td><button id="add_btn_<?php echo $ticket->id; ?>" OnClick="add_ticket(<?php
			echo "'$event_title_js', '$label', $amount_ht, $tax_rate, $ticket_id";
			?>)" <?php echo $disable; ?>>{{Add}}</button></td>
	</tr>
	<?php
		}
		arsort($tax_array);
		debug(sprint_r($tax_array));
	?>
</table>
<form name="input" action="?action=participate&amp;event_id=<?php echo $event->id; ?>" method="POST">
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

	<table id="total" style="display:none;" class="evt_table inline">
		<tr>
			<th>&nbsp;</th>
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
			<th class="th_left" colspan="2">{{Total taxes}}</th>
			<td class="evt_curr" id="total_tax">0.00</td>
		</tr>
		<tr>
			<th class="th_left" colspan="3"><b>{{Total due}}</b></th>
			<td class="evt_curr" id="total_due"><b>0.00</b></td>
		</tr>
	</table>

	<table class="evt_table_billing inline">
		<tr>
			<th class="th_left">{{Billing Entity name}}</th>
			<td><input type="text" name="username" value="<?php echo $user->firstname.' '.$user->lastname; ?>"/></td>
			<td class="help">{{The person or organisation name to be charged.}}</td>
		</tr>
		<tr>
			<th class="th_left">{{Billing address}}</th>
			<td>
				<textarea class="addresspicker" name="billing_address"" placeholder="{{Street no, Street, Zip City, etc.}}"><?php echo default_value('address', $user->address()); ?></textarea>
			</td>
			<td class="help">{{Street no, Street, Zip City, etc.}}</td>
		</tr>
		<tr>
			<th>{{VAT number (if applicable)}}</th>
			<td>
				<input type="text" name="vat" value="" placeholder="{{For EU companies only.}}"/>
			</td>
			<td class="help">{{For EU companies only.}}</td>
		</tr>
	</table>
	<input type="hidden" name="address" value=""/>
	<input type="checkbox" name="confirm"/> {{I have read the <a href="info/sales">Sales policies</a> and accept them.}}<br/>
	<br/>
	<span class="form_cancel">
		<input type="button" class="evt_button evt_btn_small evt_btn_cancel" onclick="window.location='?action=retrieve&amp;type=event&amp;id=<?php echo $event->id; ?>'" value="{{Cancel}}" />
	</span>
	<span class="spacer"></span>
	<input class="evt_button evt_btn_small" type="submit" name="next" value="{{Next}}" disabled/>
</form>
<script>
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

	var ticket_remaining = {};
	<?php
		foreach ($tickets as $ticket) {
			$remaining = $ticket->get_remaining();
			echo "ticket_remaining[{$ticket->id}] = ${remaining};";
		}
	?>

	log(ticket_remaining);

	$('input[type=checkbox]').ready(eb_sync_next_button);
	$('input').change(eb_sync_next_button);
	$('textarea').change(eb_sync_next_button);
	$(document).ready(addresspicker_init);

	var ticket_counter = 0;

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

	function add_ticket(event, label, amount_ht, tax, ticket_id) {
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
							"<td class=\"evt_curr\" type=\"amount_ht\">" +
								eb_curr(amount_ht) +
								"<input type=\"hidden\" name=\"amount_ht[]\" value=\"" + eb_curr(amount_ht) + "\"/>" +
							"</td>" +
							"<td class=\"evt_curr\" type=\"tax\" value=\"" + eb_curr(tax).replace(".", "_") + "\">" +
								eb_curr(tax) + "%" +
								"<input type=\"hidden\" name=\"taxes[]\" value=\"" + eb_curr(tax) + "\"/>" +
							"</td>" +
							"<td class=\"evt_curr\" type=\"tax_amount\">" + eb_curr(tax_amount) + "</td>" +
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
							"<td><input type=\"button\" value=\"Remove\" onClick=\"eb_remove_ticket('ticket_" + id + "', " + ticket_id + ")\"/></td>" +
						"</tr>";
		$("#tickets").append(content);
		eb_update_total();
		$('input[type="text"]').keyup(eb_sync_next_button);
		eb_sync_next_button();
	}


	function eb_display_tables() {
		$("#total").removeAttr("style");
		$("#tickets").removeAttr("style");
	}

	function eb_remove_ticket(id, ticket_id) {
		ticket_counter--;
		$("#" + id).remove();
		ticket_remaining[ticket_id]++;
		eb_sync_add_btn();

		if (ticket_counter <= 0) {
			$("#total").css("display", "none");
			$("#tickets").css("display", "none");
		}
		eb_update_total();
		eb_sync_next_button();
	}

	function eb_update_total() {
		$('[id*="total_tax_"]').html("0.00");
		$('[id*="total_ht_"]').html("0.00");
		$('[id*="total_ttc_"]').html("0.00");

		$('#total_tax').html("0.00");
		$('#total_due').html("0.00");

		$('#tickets').find($('tr[id*="ticket_"]')).each(function() {
			var amount_ht = $(this).find($("td[type='amount_ht']")).html();
			var tax = $(this).find($("td[type='tax']")).attr("value");
			var tax_amount = $(this).find($("td[type='tax_amount']")).html();

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

			if (total_ht == 0) {
				$('#row_total_' + tax_id).css("display", "none");
			} else {
				$('#row_total_' + tax_id).css("display", "");
			}
		});
	}

	function eb_sync_next_button() {
		var test = $('input[type=checkbox]').is(':checked');
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
		if ($("textarea[name='billing_address']").val() == "") {
			test = false;
		}

		if (test) {
			$('input[name=next]').removeAttr('disabled');
		} else {
			$('input[name=next]').attr('disabled', 'disabled');
		}
	}
</script>