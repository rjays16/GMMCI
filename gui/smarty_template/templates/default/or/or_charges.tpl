<div>

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:4000"></div>
<div id="other_accommodation" align="left"> <!-- accommodation start -->
	<div id="header" class="jqDrag"><span style="float:left">Accommodation Charges</span>{{$close_other_accommodation}}<br style="clear:both" /></div>
	<div id="body">
		 <table border="0" cellspacing="2" cellpadding="2" align="center" width="100%">
			 <tbody>
				 <tr>
					 <td align="right" width="30%">Ward :</td>
					 <td align="left">
							<!--<select id="opwardlist" name="opwardlist" onchange="jsOpAccChrgOptionsChange(this, this.options[this.selectedIndex].value)">-->
							<select id="opwardlist" name="ward_list" onchange="populate_room_list();" class="segInput">
								<option value="0">- Select a ward -</option>
							</select>
					 </td>
				 </tr>
				 <tr>
					 <td align="right">Room :</td>
					 <td>
							<!--<select id="orlist" name="orlist" onchange="jsOpAccChrgOptionsChange(this, this.options[this.selectedIndex].value)">-->
							<select id="orlist" name="room_list" class="segInput">
								<option value="0">- Select a room -</option>
							</select>
					 </td>
				 </tr>
				 <tr>
					 <td align="right">OR usage? </td>
					 <td>
							<input type="radio" id="or_usage_yes" name="or_usage" value="1" checked="checked" onclick="changeUsageOptions(this.value);">Yes
							<input type="radio" id="or_usage_no" name="or_usage" value="0" onclick="changeUsageOptions(this.value);"> No
					 </td>
				 </tr>
				 <tr id="row_rvu" style="display">
					 <td align="right">Total RVU :</td>
					 <td>
							<input style="text-align:right" class="segInput" disabled="disabled" id="total_rvu" name="total_rvu" size="30" value="" type="text"/>&nbsp;<span style="vertical-align:top">{{$sSelectOps}}</span>
					 </td>
				 </tr>
				 <tr id="row_multiplier" style="display">
					 <td align="right">Multiplier :</td>
					 <td>
							<input style="text-align:right" class="segInput" disabled="disabled" id="multiplier" name="multiplier" size="30" value=""  type="text"/>
					 </td>
				 </tr>
				 <tr id="row_days" style="display:none">
						<td align="right">Day(s) :</td>
						<td>
							<input style="text-align:right" class="segInput" id="total_days" name="total_days" size="30" maxlength="3" value=""  onblur="trimString(this); num_check(this.id, this.value);"  type="text"/>
						</td>
				 </tr>
				 <tr id="row_hours" style="display:none">
						<td align="right">Hour(s) :</td>
						 <td>
								<input style="text-align:right"  class="segInput" id="total_hours" name="total_hours" size="30"  maxlength="3" value=""  onblur="trimString(this); num_check(this.id, this.value);" type="text"/>
						 </td>
				 </tr>
				 <tr>
					 <td align="right">Charge :</td>
					 <td>
							<input  class="segInput" style="text-align:right" onblur="trimString(this); genChkDecimal(this);" onFocus="this.select();" id="oprm_chrg" name="oprm_chrg" size="30" value="" type="text" />
					 </td>
				 </tr>
				 <tr>
					 <td colspan="2">
						 {{$room_type}}
						 {{$add_accommodation}}{{$add_accommodation_cancel}}
					 </td>
				 </tr>
			 </tbody>
		 </table>
	 </div>
	{{$resize}}
</div> <!-- accommodation end -->

{{$form_start}}
	<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%">
		<tbody>
			<tr><td class="segPanelHeader" width="*">Transaction Details</td></tr>
			<tr>
				<td class="segPanel">
					<table border="0" width="100%" class="transaction_details_table" cellpadding="3" cellspacing="0">
						<tr>
							<td width="20%" align="right"><b>Transaction Type:</b></td>
							<td width="80%">
								<div style="font:bold 18px Arial; color:#006000">
									{{html_radios name="transaction_type" options=$transaction_types selected=$transaction_type id="transaction_type"}}
								</div>
							</td>
						</tr>
						<tr>
							<td align="right"><b>Transaction Date:</b></td>
							<td>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td valign="middle">{{$transaction_date_display}}</td>
										<td>{{$transaction_date_picker}}{{$transaction_date}}{{$transaction_date_calendar_script}}</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td align="right"><b>Patient Name:</b></td>
							<td>{{$patient_name}}</td>
						</tr>
						<tr>
							<td align="right"><b>Patient Type:</b></td>
							<td>{{$encounter_type}}</td>
						</tr>
						<tr>
							<td align="right"><b>Patient Classification:</b></td>
							<td>{{$patient_classification}}</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<div id="or_main_schedule" align="left">
	<br/>
	<!--<fieldset>
		<legend>Equipments</legend>
		<table class="segList" width="100%" id="equipment_list">
			<thead>
				<tr>
					<th width="1%" nowrap="nowrap">&nbsp;</th>
					<th width="20%" nowrap="nowrap" align="center">Equipment</th>
					<th width="*" nowrap="nowrap" align="center">Equipment Description</th>
					<th width="10%" align="center" nowrap="nowrap">Number of Usage</th>
					<th width="10%" align="center" nowrap="nowrap">Price(Orig)</th>
					<th width="10%" align="center" nowrap="nowrap">Price(Adj)</th>
					<th width="10%" align="center" nowrap="nowrap">Acc. Total</th>
				</tr>
			</thead>
			<tbody>
				<tr id="empty_equipment_row">
					<td colspan="7">Equipment order items is currently empty..</td>
				</tr>
			</tbody>
		</table>
		<table width="100%">
			<tr>
				<td align="right" width="50%">{{$add_equipment}}</td>
				<td align="left" width="50%">{{$empty_equipment}}</td>
			</tr>
		</table>
		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
			<tbody>
				<tr>
					<td width="*" align="right" style="padding:4px" height=""><strong>Sub-Total</strong></th>
					<td id="equipment_subtotal" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
				<tr>
					<td align="right" style="padding:4px"><strong>Discount</strong></th>
					<td id="equipment_discount_total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
				<tr>
					<td align="right" style="padding:4px"><strong>Net Total</strong></th>
					<td id="equipment_net_total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
			 </tbody>
		</table>
	</fieldset>-->
	<fieldset id="or_accomodation_field" style="display">
		<legend>Room Use</legend>
		<table class="segList" width="100%" id="accommodation_list">
			<thead>
				<tr>
					<th width="1%"></th>
					<th align="left" width="*">Room</th>
					<th align="left" width="10%">RVU</th>
					<th align="left" width="10%">Multiplier</th>
					<th align="left" width="10%">Days</th>
					<th align="left" width="10%">Hours</th>
					<th align="left" width="10%">Total Charge</th>
				</tr>
			</thead>
			<tbody>
				<tr id="empty_accommodation_row"><td colspan="7">No room accomodation charged...</td></tr>
			</tbody>
		</table>
		<table width="100%">
				<tr>
					<td align="right" width="50%">{{$add_room}}</td>
					<td align="left" width="50%">{{$empty_room}}</td>
				</tr>
		</table>
		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
		<tbody>
			<tr>
				<td width="*" align="right" style="padding:4px" height=""><strong>Sub-Total</strong></th>
				<td id="accommodation_subtotal" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold"></td>
			</tr>
			<tr>
				<td align="right" style="padding:4px"><strong>Discount</strong></th>
				<td id="accommodation_discount_total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold"></td>
			</tr>
			<tr>
				<td align="right" style="padding:4px"><strong>Net Total</strong></th>
				<td id="accommodation_net_total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold"></td>
			</tr>
		 </tbody>
	</table>
	</fieldset>
	<fieldset>
		<legend>Medicine and Supplies</legend>
		<table class="segList" width="100%" id="supplies-list">
			<thead>
				<tr>
					<th width="1%" nowrap="nowrap">&nbsp;</th>
					<th width="10%" nowrap="nowrap" align="center">Item Code</th>
					<th width="*" nowrap="nowrap" align="center">Item Description</th>
					<th width="4%" nowrap="nowrap" align="center" style="display:none">Consigned</th>
					<th width="10%" align="center" nowrap="nowrap">Quantity</th>
					<th width="10%" align="center" nowrap="nowrap">Price(Orig)</th>
					<th width="10%" align="center" nowrap="nowrap">Price(Adj)</th>
					<th width="10%" align="center" nowrap="nowrap">Acc. Total</th>
					<th width="10%" align="center" nowrap="nowrap">Status</th>
				</tr>
			</thead>
			<tbody>
				<tr><td colspan="9">Order list is currently empty...</td></tr>
			</tbody>
		</table>
		<table width="100%">
			<tr>
				<td align="right" width="50%">{{$supplies_add_button}}</td>
				<td align="left" width="50%">{{$supplies_empty_button}}</td>
			</tr>
		</table>
		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
			<tbody>
				<tr>
					<td width="*" align="right" style="padding:4px" height=""><strong>Sub-Total</strong></th>
					<td id="show-sub-total" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
				<tr>
					<td align="right" style="padding:4px"><strong>Discount</strong></th>
					<td id="show-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
				<tr>
					<td align="right" style="padding:4px"><strong>Net Total</strong></th>
					<td id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
			 </tbody>
		</table>
	</fieldset>
	<fieldset>
		<legend>Miscellaneous Charges</legend>
		<table  width="100%" class="transaction_details_table" cellpadding="3" cellspacing="0">
			<tr>
				<td width="1%" align="right" nowrap="nowrap">Select type</td>
				<td width="*"><select class="segInput" name="misc_service_type" id="misc_service_type">{{$miscServiceTypes}}</select></td>
			</tr>
		</table>
		<table class="segList" width="100%" id="misc_list">
			<thead>
				<tr>
					<th></th>
					<th align="center">Service Code</th>
					<th align="center">Type</th>
					<th align="center">Item Name</th>
					<th align="center">Quantity</th>
					<th align="center">Unit Price</th>
					<th align="center">Total Charge</th>
					<th align="center">Status</th>
				</tr>
			</thead>
			<tbody>
				<tr id="empty_misc_row"><td colspan="8">Miscellaneous charges is empty...</td></tr>
			</tbody>
		</table>
		<table width="100%">
				<tr>
					<td align="right" width="50%">{{$add_misc}}</td>
					<td align="left" width="50%">{{$empty_misc}}</td>
				</tr>
		</table>
		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
		<tbody>
			<tr>
				<td width="*" align="right" style="padding:4px" height=""><strong>Sub-Total</strong></th>
				<td id="misc_subtotal" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold"></td>
			</tr>
			<tr>
				<td align="right" style="padding:4px"><strong>Discount</strong></th>
				<td id="misc_discount_total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold"></td>
			</tr>
			<tr>
				<td align="right" style="padding:4px"><strong>Net Total</strong></th>
				<td id="misc_net_total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold"></td>
			</tr>
		 </tbody>
	</table>
	</fieldset>
	</div>
<div id="opstaken" style="display:none;"></div>
<div id="or_main_schedule">
{{$other_charges_submit}}
{{$other_charges_cancel}}
</div>
{{$pharma_area}}
{{$discountid}}
{{$discount}}
{{$issc}}
{{$pid}}
{{$encounter_nr}}
{{$billdate}}
{{$confinement_type}}
{{$submitted}}
{{$mode}}
{{$ward}}
{{$pharma_req_src}}
{{$form_end}}

</div>