<?php
	global $g_display;
	global $g_error_msg;
	$event = $g_display["event"];
	$rates = $g_display["rates"];
	$user = $g_display["user"];

?>
<h1>NOMINATIVE</h1>
<span style="color:red;">
	<?php echo "$g_error_msg<br/>"; ?>
</span>
<table>
	<tr>
		<th>Event name</th>
		<th>Categories</th>
		<th>Rates</th>
		<th>Taxes</th>
		<th>Rate TTC</th>
	</tr>
	<?php
		$tax_array = array();
		foreach ($rates as $rate) {
			$event_title = $event->title;
			$amount_ht = curr($rate["amount"]);
			$label = $rate["label"];
			$tax_rate = $rate["tax_rate"];
			if (!in_array($tax_rate, $tax_array)) {
				$tax_array[] = $tax_rate;
			}
			$amount_ttc = number_format($amount_ht * (($tax_rate/100) + 1), 2);
	?>
	<tr>
		<td><?php echo $event_title; ?></td>
		<td><?php echo $label; ?></td>
		<td><?php echo $amount_ht; ?></td>
		<td><?php echo $tax_rate; ?></td>
		<td><?php echo $amount_ttc; ?></td>
		<td><button OnClick="add_ticket(<?php
			echo "'$event_title', '$label', $amount_ht, $tax_rate";
			?>)">Add</button></td>
	</tr>
	<?php
		}
		arsort($tax_array);
		debug(sprint_r($tax_array));
	?>
</table>
<br/>
<form name="input" action="?action=participate&amp;event_id=<?php echo $event->id; ?>" method="POST">
	<table id="tickets" style="display:none;">
		<tr>
			<th colspan="6">Event details</th>
			<th colspan="3">Participant details</th>
		</tr>
		<tr>
			<th>Event</th>
			<th>Rate</th>
			<th>Unit price</th>
			<th>Taxe rate</th>
			<th>Taxe amount</th>
			<th>TTC</th>
			<th>Title</th>
			<th>Lastname</th>
			<th>Firstname</th>
		</tr>
	</table>
	<br/>

	<table id="total" style="display:none;">
		<?php
			foreach ($tax_array as $tax_rate) {
				$tax_rate_id = str_replace(".", "_", $tax_rate);
		?>
		<tr id="row_total_<?php echo $tax_rate_id; ?>">
			<td>Tax(<?php echo $tax_rate; ?>)</td>
			<td id="total_ht_<?php echo $tax_rate_id; ?>">0.00</td>
			<td id="total_tax_<?php echo $tax_rate_id; ?>">0.00</td>
			<td id="total_ttc_<?php echo $tax_rate_id; ?>">0.00</td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td colspan="2">Total taxes</td>
			<td id="total_tax">0.00</td>
		</tr>
		<tr>
			<td colspan="3"><b>Total due</b></td>
			<td id="total_due"><b>0.00</b></td>
		</tr>
	</table>
	<br/>

	<table>
		<tr>
			<td>Billing Entity name: </td>
			<td><input type="text" name="username" value="<?php echo $user['firstname'].' '.$user['lastname']; ?>"/></td>
			<td class="help">The person or organisation name to be charged.</td>
		</tr>
		<tr>
			<td>Billing address: </td>
			<td><textarea  rows=3 name="address"><?php echo $user['address']; ?></textarea></td>
			<td class="help">numero - rue - code postal - ville - pays</td>
		</tr>
	</table>
	<input type="checkbox" name="confirm"/> I have read the <a href="terms.html">Terms and polices</a> and accept them.<br/>
	<input type="submit" value="Next" disabled/>
</form>
<form name="input" action="?action=retrieve&amp;type=event&amp;id=<?php echo $event->id; ?>" method="POST">
	<input type="submit" value="Cancel"/>
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

	$('input[type=checkbox]').ready(eb_sync_next_button);
	$('input[type=checkbox]').change(eb_sync_next_button);

	var ticket_counter = 0;

	function add_ticket(event, label, amount_ht, tax) {
		ticket_counter++;
		eb_display_tables();

		var tax_amount = amount_ht * (tax/100);
		var amount_ttc = amount_ht + tax_amount;
		var id = new Date().getTime();
		var content = 	"<tr id=\"ticket_" + id + "\">" +
							"<td>" + event + "</td>" +
							"<td>" +
								label +
								"<input type=\"hidden\" name=\"labels[]\" value=\"" + label + "\"/>" +
							"</td>" +
							"<td type=\"amount_ht\">" +
								eb_curr(amount_ht) +
								"<input type=\"hidden\" name=\"amount_ht[]\" value=\"" + eb_curr(amount_ht) + "\"/>" +
							"</td>" +
							"<td type=\"tax\" value=\"" + eb_curr(tax).replace(".", "_") + "\">" +
								eb_curr(tax) + "%" +
								"<input type=\"hidden\" name=\"taxes[]\" value=\"" + eb_curr(tax) + "\"/>" +
							"</td>" +
							"<td type=\"tax_amount\">" + eb_curr(tax_amount) + "</td>" +
							"<td>" + eb_curr(amount_ttc) + "</td>" +
							"<td><input type=\"text\" name=\"titles[]\" placeholder=\"(optional) ex: Professor\"/></td>" +
							"<td><input type=\"text\" name=\"lastnames[]\" placeholder=\"mandatory\"/></td>" +
							"<td><input type=\"text\" name=\"firstnames[]\" placeholder=\"mandatory\"/></td>" +
							"<td><input type=\"button\" value=\"Remove\" onClick=\"eb_remove_ticket('ticket_" + id + "')\"/></td>" +
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

	function eb_remove_ticket(id) {
		ticket_counter--;
		$("#" + id).remove();
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

		if (test) {
			$('input[value=Next]').removeAttr('disabled');
		} else {
			$('input[value=Next]').attr('disabled', 'disabled');
		}
	}
</script>