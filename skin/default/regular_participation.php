<?php
	$event = $g_display["event"];
	$rates = $g_display["rates"];
	$user = $g_display["user"];
?>
How many ticket do you want?
<form name="input" action="?action=participate&amp;event_id=<?php echo $event->id; ?>" method="POST">
	<table class="evt_table">
		<tr>
			<th>Event</th>
			<th>Rate</th>
			<th>Unit price (€)</th>
			<th>Quantity</th>
			<th>Total tax excluded (€)</th>
			<th>Taxe rate (%)</th>
			<th>Taxe amount (€)</th>
			<th>Total due (€)</th>
		</tr>
		<?php
			$i = 0;
			$tax_array = array();
			foreach ($rates as $rate) {
				$event_title = $event->title;
				$amount_ht = str_replace(",", "", curr($rate["amount"]));
				$label = $rate["label"];
				$tax_rate = $rate["tax_rate"];
				if (!in_array($tax_rate, $tax_array)) {
					$tax_array[] = $tax_rate;
				}
		?>
		<tr>
			<td><?php echo $event_title; ?></td>
			<td><?php echo $label; ?></td>
			<td id="unit_price_<?php echo $i; ?>" class="evt_curr"><?php echo $amount_ht; ?></td>
			<td><input id="<?php echo $i; ?>" type="number" name="ticket_<?php echo $i; ?>" value="<?php echo_default_value("ticket_${i}", 1) ?>"/></td>
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
	<br/>

	<table id="tickets" class="evt_table">
		<tr>
			<th></th>
			<th>Amount (€)</th>
			<th>Taxes (€)</th>
			<th>Due (€)</th>
		</tr>
		<?php
			$i2 = 0;
			foreach ($tax_array as $tax_rate) {
		?>
		<tr>
			<th class="th_left">Tax(<?php echo $tax_rate; ?>%)</td>
			<td id="tax_base_<?php echo $i2; ?>" class="evt_curr">0.00</td>
			<td id="tax_total_<?php echo $i2; ?>" class="evt_curr">0.00</td>
			<td id="tax_total_due_<?php echo $i2; ?>" class="evt_curr">0.00</td>
		</tr>
		<?php
				$i2++;
			}
		?>
		<tr>
			<th class="th_left" colspan="2">Total taxes</td>
			<td id="tax_total" class="evt_curr">0.00</td>
		</tr>
		<tr>
			<th class="th_left" colspan="3"><b>Total due</b></td>
			<td id="total_due" class="evt_curr"><b>0.00</b></td>
		</tr>
	</table>
	<br/>

	<table id="total" class="evt_table_billing">
		<tr>
			<th class="th_left">Billing Entity name</th>
			<td><input type="text" name="username" value="<?php echo $user->firstname.' '.$user->lastname; ?>"/></td>
			<td class="help">The person or organisation name to be charged.</td>
		</tr>
		<tr>
			<th class="th_left" rowspan="5">Billing address</th>
			<td>
				<input type="text" name="address_street" value="<?php echo $user->street; ?>" placeholder="street# and street name"/>
			</td>
			<td class="help">Street# and street name</td>
		</tr>
		<tr>
			<td>
				<input type="text" name="address_city" value="<?php echo $user->city; ?>" placeholder="City"/>
			</td>
			<td class="help">City</td>
		</tr>
		<tr>
			<td>
				<input type="text" name="address_zip" value="<?php echo $user->zip; ?>" placeholder="ZIP code"/>
			</td>
			<td class="help">Zip code</td>
		</tr>
		<tr>
			<td>
				<select name="address_country">
					<?php echo form_get_country_options(default_value("country", $user->country)); ?>
				</select>
			</td>
			<td class="help">Country</td>
		</tr>
		<tr>
			<td>
				<input type="text" name="state" value="<?php echo $user->state; ?>" placeholder="State (optional)"/>
			</td>
			<td class="help">State (optional)</td>
		</tr>
	</table>
	<input type="hidden" name="address" value=""/>
	<input type="checkbox" name="confirm"/> I have read the <a href="CGV.pdf">CGV</a> and accept them.<br/>
	<input type="submit" value="Next" disabled/>
</form>
<form name="input" action="?action=retrieve&amp;type=event&amp;id=<?php echo $event->id; ?>" method="POST">
	<input type="submit" value="Cancel"/>
</form>
<script>
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
	$('input').change(eb_sync_amount);
	$('input').keyup(eb_sync_amount);
	$('input[id]').each(eb_sync_amount);

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
			$('input[value=Next]').removeAttr('disabled');
		} else {
			$('input[value=Next]').attr('disabled', 'disabled');
		}
	}
</script>