{{$or_main_css}}
{{foreach from=$javascript_array item=js}}
	{{$js}}
{{/foreach}}
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
<div id="or_main_equipment" align="left" style="top:5%;left:5%"> <!-- equipment start -->
	<div id="header" class="jqDrag"><span style="float:left">Select Equipment</span>{{$close_equipment}}<br style="clear:both" /></div>

	<div id="body">
		 <div id="select_or">
			 <br/>
			 <div id="search_bar" align="left">
					{{$search_field}}{{$search_button}}
			 </div>
			 <div id="navigation">
			 <div class="group"><select name="number_of_pages">{{html_options options=$number_of_pages}}</select></div>
			 <div id="button_separator"></div>
		<div class="group">
			<div id="first" class="button"><span></span></div>
			<div id="prev" class="button"><span></span></div>
		</div>
		<div id="button_separator"></div>
		<div class="group"><span id="control">Page {{$page_number}} of <span></span></span></div>
		<div id="button_separator"></div>
		<div class="group">
			<div id="next" class="button"><span></span></div>
			<div id="last" class="button"><span></span></div>
		</div>
		<div id="button_separator"></div>
		<div class="group">
			<div id="reloader" class="pre_load button loading"><span></span></div>
		</div>
		<div id="button_separator"></div>
		<div class="group"><span id="page_stat">Processing, please wait...</span></div>
</div>
<table id="or_equipment_table" align="left"></table>
</div>

	</div>
	{{$resize}}
</div> <!-- equipment end -->

<div id="or_main_oxygen" align="left"> <!-- oxygen start -->
	<div id="header" class="jqDrag"><span style="float:left">Select Oxygen Serial Number</span>{{$close_oxygen}}<br style="clear:both" /></div>

	<div id="body">
		 <div id="select_or">
			 <br/>
			 <div id="search_bar" align="left">
					{{$search_field}}{{$search_button}}
			 </div>
			 <div id="navigation">
			 <div class="group"><select name="number_of_pages">{{html_options options=$number_of_pages}}</select></div>
			 <div id="button_separator"></div>
		<div class="group">
			<div id="first" class="button"><span></span></div>
			<div id="prev" class="button"><span></span></div>
		</div>
		<div id="button_separator"></div>
		<div class="group"><span id="control">Page {{$page_number}} of <span></span></span></div>
		<div id="button_separator"></div>
		<div class="group">
			<div id="next" class="button"><span></span></div>
			<div id="last" class="button"><span></span></div>
		</div>
		<div id="button_separator"></div>
		<div class="group">
			<div id="reloader" class="pre_load button loading"><span></span></div>
		</div>
		<div id="button_separator"></div>
		<div class="group"><span id="page_stat">Processing, please wait...</span></div>
</div>
<table id="or_oxygen_table" align="left"></table>
</div>

	</div>
	{{$resize}}
</div> <!-- oxygen end -->

	<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%">
		<tbody>
			<tr><td class="segPanelHeader" width="*">Transaction Details</td></tr>
			<tr>
				<td class="segPanel">
					<table  width="100%" class="transaction_details_table" cellpadding="3" cellspacing="0">
						<tr>
							<td width="20%" align="right"><b>Transaction Type:</b></td>
							<td width="80%">{{html_radios name="transaction_type" options=$transaction_type selected=1 id="transaction_type"}}</td>
						</tr>
						<tr>
							<td align="right"><b>Transaction Date:</b></td>
							<td><table cellpadding="0" cellspacing="2"><tr><td valign="bottom">{{$transaction_date_display}}</td><td>{{$transaction_date_picker}}{{$transaction_date}}{{$transaction_date_calendar_script}}</td></tr></table></td>
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
	<fieldset>
		<legend>Miscellaneous Charges</legend>
		<table  width="100%" class="transaction_details_table" cellpadding="3" cellspacing="0">
			<tr>
				<td width="27%" align="left"><b>Select miscellaneous service type : </b></td>
				<td width="70%"><select class="segInput" name="misc_service_type" id="misc_service_type">{{$miscServiceTypes}}</select></td>
			</tr>
		</table>
		<br/>
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
	<fieldset>
		<legend>Medicine and Supplies</legend>
		<table class="segList" width="100%" id="supplies-list">
			<thead>
				<tr>
					<th width="1%" nowrap="nowrap">&nbsp;</th>
					<th width="10%" nowrap="nowrap" align="center">Item No.</th>
					<th width="*" nowrap="nowrap" align="center">Item Description</th>
					<th width="4%" nowrap="nowrap" align="center">Consigned</th>
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


