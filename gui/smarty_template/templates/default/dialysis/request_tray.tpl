<style>
	* {
		font: normal 12px Arial;
	}
	table.transaction_details_table {
		border-collapse: collapse;

	}
	table.transaction_details_table tr td {
		/**border: 1px #000000 solid;    **/
	}
	table.transaction_details_table table tr td {
		border: none;
	}
	#transaction_date_display {
		background: #FFFFFF;
		padding: 3px;
		border: 1px #99BBE8 solid;
		width: 170px;
	}
	.date_time_picker {
		cursor: pointer;
	}
</style>
<div>
{{$form_start}}
<div style="width:775px; margin-top:10px" align="center">
	<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%">
		<tbody>
			<tr>
				<td class="segPanelHeader" width="*">Transaction Details</td>
			</tr>
			<tr>
				<td class="segPanel" align="left" valign="top">
					<table  width="100%" class="transaction_details_table" cellpadding="3" cellspacing="0" style="font:normal 12px Arial; padding:4px" >
						<tr>
							<td align="right"><b>Patient Name:</b></td>
							<td>{{$patient_name}}</td>
							<td align="right"><b>Transaction Date:</b></td>
							<td><table cellpadding="0" cellspacing="2"><tr><td valign="bottom">{{$transaction_date_display}}</td><td>{{$transaction_date_picker}}{{$transaction_date}}{{$transaction_date_calendar_script}}</td></tr></table></td>
						</tr>
						<tr>
							<td align="right"><b>Patient Type:</b></td>
							<td>{{$encounter_type}}</td>
							<td align="right"><b>Transaction Type:</b></td>
							<td>{{$transaction_type}}{{$sChargeTyp}}</td>
						</tr>
						<tr>
							<td align="right"><b>Gender:</b></td>
							<td>{{$patient_gender}}</td>
							<td align="right"><b>From RDU?</b></td>
							<td>{{$sRDU}}</td>
						</tr>
						<tr>
							<td align="right"><b>Age:</b></td>
							<td>{{$patient_age}}</td>
							<td align="right"><b>Priority:</b></td>
							<td>{{$sPriority}}</td>
						</tr>
						<tr>
							<td align="right"><b>Comments:</b></td>
							<td>{{$sComments}}</td>
						</tr>

						<!--<tr>
							<td align="right"><b>Patient Classification:</b></td>
							<td>{{$patient_classification}}</td>
						</tr>-->
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<div id="or_main_schedule" align="left">
	<br/>
	<fieldset>
		<legend>Laboratory Service</legend>
		<table width="100%" cellpadding="3" cellspacing="0">
			<tr>
				<td align="left" colspan="2"><b>Reference No. </b>&nbsp;{{$lab_reference_no}}</td>
				<td align="left" colspan="2"><b>PHIC Coverage: </b>&nbsp;{{$lab_phic}}</td>
				<td align="right" colspan="2">{{$add_lab_btn}}&nbsp;{{$empty_lab_btn}}</td>
			</tr>
		</table>
		<table class="segList" width="100%" id="lab-list">
			<thead>
				<tr>
					<th width="5%" nowrap="nowrap">Cnt : <span id="counter">0</span></th>
					<th width="10%" nowrap="nowrap">Result</th>
					<th width="10%" align="center" nowrap="nowrap">Status</th>
					<th width="10%" nowrap="nowrap" align="center">Code</th>
					<th width="*" nowrap="nowrap" align="center">Service Description</th>
					<th width="10%" align="center" nowrap="nowrap">Original Price</th>
					<th width="10%" align="center" nowrap="nowrap">Net Price</th>
				</tr>
			</thead>
			<tbody>
				<tr><td colspan="10">Request list is currently empty...</td></tr>
			</tbody>
		</table>
		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
			<tbody>
				<tr>
					<td width="*" align="right" style="padding:4px" height=""><strong>Sub-Total</strong></th>
					<td id="lab-sub-total" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
				<tr>
					<td align="right" style="padding:4px"><strong>Discount</strong></th>
					<td id="lab-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
				<tr>
					<td align="right" style="padding:4px"><strong>Net Total</strong></th>
					<td id="lab-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
			 </tbody>
		</table>
	</fieldset>

	<fieldset>
		<legend>Blood Bank Service</legend>
		<table width="100%" cellpadding="3" cellspacing="0">
			<tr>
				<td align="left" colspan="2"><b>Reference No. </b>&nbsp;{{$blood_reference_no}}</td>
				<td align="left" colspan="2"><b>PHIC Coverage: </b>&nbsp;{{$blood_phic}}</td>
				<td align="right" colspan="2">{{$add_blood_btn}}&nbsp;{{$empty_blood_btn}}</td>
			</tr>
		</table>
		<table class="segList" width="100%" id="blood-list">
			<thead>
				<tr>
					<th width="5%" nowrap="nowrap">Cnt : <span id="blood-counter">0</span></th>
					<th width="10%" nowrap="nowrap" align="center">Code</th>
					<th width="*" nowrap="nowrap" align="center">Service Description</th>
					<th width="10%" align="center" nowrap="nowrap">Quantity</th>
					<th width="10%" align="center" nowrap="nowrap">Original Price</th>
					<th width="10%" align="center" nowrap="nowrap">Net Price</th>
				</tr>
			</thead>
			<tbody>
				<tr><td colspan="10">Request list is currently empty...</td></tr>
			</tbody>
		</table>
		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
			<tbody>
				<tr>
					<td width="*" align="right" style="padding:4px" height=""><strong>Sub-Total</strong></th>
					<td id="blood-sub-total" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
				<tr>
					<td align="right" style="padding:4px"><strong>Discount</strong></th>
					<td id="blood-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
				<tr>
					<td align="right" style="padding:4px"><strong>Net Total</strong></th>
					<td id="blood-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
			 </tbody>
		</table>
	</fieldset>

	<fieldset>
		<legend>Radiology Service</legend>
		<table width="100%" cellpadding="3" cellspacing="0">
			<tr>
				<td align="left" colspan="2"><b>Reference No. </b>&nbsp;{{$radio_reference_no}}</td>
				<td align="left" colspan="2"><b>PHIC Coverage: </b>&nbsp;{{$radio_phic}}</td>
				<td align="right" colspan="2">{{$add_radio_btn}}&nbsp;{{$empty_radio_btn}}</td>
			</tr>
		</table>
		<table class="segList" width="100%" id="radio-list">
			<thead>
				<tr>
					<th width="5%" nowrap="nowrap">Cnt : <span id="radio-counter">0</span></th>
					<th width="1%" nowrap="nowrap" align="center">&nbsp;</th>
					<th width="20%" nowrap="nowrap" align="center">Code</th>
					<th width="*" nowrap="nowrap" align="center">Service Description</th>
					<th width="10%" align="center" nowrap="nowrap">Original Price</th>
					<th width="10%" align="center" nowrap="nowrap">Net Price</th>
				</tr>
			</thead>
			<tbody>
				<tr><td colspan="10">Request list is currently empty...</td></tr>
			</tbody>
		</table>
		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
			<tbody>
				<tr>
					<td width="*" align="right" style="padding:4px" height=""><strong>Sub-Total</strong></th>
					<td id="radio-sub-total" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
				<tr>
					<td align="right" style="padding:4px"><strong>Discount</strong></th>
					<td id="radio-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
				<tr>
					<td align="right" style="padding:4px"><strong>Net Total</strong></th>
					<td id="radio-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
			 </tbody>
		</table>
	</fieldset>

	<fieldset>
		<legend>Medicine and Supplies</legend>
		<table width="100%" cellpadding="3" cellspacing="0">
			<tr>
				<td align="left" colspan="2"><b>Reference No. </b>&nbsp;{{$pharma_reference_no}}</td>
				<td align="left" colspan="2"><b>PHIC Coverage: </b>&nbsp;{{$pharma_phic}}</td>
				<td align="right" colspan="2">{{$add_pharma_btn}}&nbsp;{{$empty_pharma_btn}}</td>
			</tr>
		</table>
		<table class="segList" width="100%" id="pharma-list">
			<thead>
				<tr>
					<th width="5%" nowrap="nowrap">Cnt : <span id="pharma-counter">0</span></th>
					<th width="10%" nowrap="nowrap" align="center">Code</th>
					<th width="*" nowrap="nowrap" align="center">Item Description</th>
					<th width="10%" align="center" nowrap="nowrap">Quantity</th>
					<th width="10%" align="center" nowrap="nowrap">Price(Orig)</th>
					<th width="10%" align="center" nowrap="nowrap">Price(Adj)</th>
					<th width="10%" align="center" nowrap="nowrap">Total</th>
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
					<td id="pharma-sub-total" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
				<tr>
					<td align="right" style="padding:4px"><strong>Discount</strong></th>
					<td id="pharma-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
				<tr>
					<td align="right" style="padding:4px"><strong>Net Total</strong></th>
					<td id="pharma-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold"></td>
				</tr>
			 </tbody>
		</table>
	</fieldset>

	<fieldset>
		<legend>Miscellaneous Charges</legend>
		<table width="100%" cellpadding="3" cellspacing="0">
			<tr>
				<td align="left" colspan="2"><b>Reference No. </b>&nbsp;{{$misc_reference_no}}</td>
				<td align="left" colspan="2"><b>PHIC Coverage: </b>&nbsp;{{$misc_phic}}</td>
				<td align="right" colspan="2">{{$add_misc_btn}}&nbsp;{{$empty_misc_btn}}</td>
			</tr>
			<!--<tr>
				<td width="27%" align="left" colspan="6">
				<b>Select miscellaneous service type : </b>&nbsp;
				<select class="segInput" name="misc_service_type" id="misc_service_type">{{$miscServiceTypes}}</select>
				</td>
			</tr>-->
		</table>
		<table class="segList" width="100%" id="misc-list">
			<thead>
				<tr>
					<th></th>
					<th width="5%" nowrap="nowrap">Cnt : <span id="misc-counter">0</span></th>
					<th align="center">Code</th>
					<th align="center">Type</th>
					<th align="center">Item Description</th>
					<th align="center">Quantity</th>
					<th align="center">Unit Price</th>
					<th align="center">Total</th>
				</tr>
			</thead>
			<tbody>
				<tr id="empty_misc_row"><td colspan="8">Miscellaneous charges is empty...</td></tr>
			</tbody>
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
{{$submitted}}
{{$mode}}
{{$ward}}
{{$form_end}}

</div>


