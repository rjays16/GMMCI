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
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:4000"></div>  
<div id="misc_charge" align="left"> <!-- miscellaneous start -->
	<div id="header" class="jqDrag"><span style="float:left">Miscellaneous Charge</span>{{$close_misc_charge}}<br style="clear:both" /></div>
	
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
<table id="misc_charge_table" align="left"></table>
</div>
		
	</div>
	{{$resize}}
</div> <!-- miscellaneous end -->
																							 
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

<div id="other_accommodation" align="left"> <!-- accommodation start -->
	<div id="header" class="jqDrag"><span style="float:left">OR Accommodation Charges</span>{{$close_other_accommodation}}<br style="clear:both" /></div>
					
	<div id="body">
		 <table border="0" cellspacing="2" cellpadding="2" align="center" width="100%">
			 <tbody>
				 <tr>
					 <td align="right" width="30%">O.R. Ward :</td>
					 <td align="left">
							<!--<select id="opwardlist" name="opwardlist" onchange="jsOpAccChrgOptionsChange(this, this.options[this.selectedIndex].value)">-->
							<select id="opwardlist" name="opwardlist">
								<option value="0">- Select O.R. Ward -</option>
							</select>
					 </td>
				 </tr>
				 <tr>
					 <td align="right">Room :</td>
					 <td>
							<!--<select id="orlist" name="orlist" onchange="jsOpAccChrgOptionsChange(this, this.options[this.selectedIndex].value)">-->
							<select id="orlist" name="orlist">
								<option value="0">- Select Operating Room -</option>
							</select>
					 </td>
				 </tr>
				 <tr>
					 <td align="right">Total RVU :</td>
					 <td>
							<input style="text-align:right;" disabled="disabled" id="total_rvu" name="total_rvu" size="30" value="" />&nbsp;<span style="vertical-align:top">{{$sSelectOps}}</span>
					 </td>
				 </tr>
				 <tr>
					 <td align="right">Multiplier :</td>
					 <td>
							<input style="text-align:right" disabled="disabled" id="multiplier" name="multiplier" size="30" value="" />
					 </td>
				 </tr>  
				 <tr>
					 <td align="right">Charge :</td>
					 <td>
							<input style="text-align:right" onblur="trimString(this); genChkDecimal(this);" onFocus="this.select();" id="oprm_chrg" name="oprm_chrg" size="30" value="" />
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

<!--  original accomodation charges
<div id="other_accommodation" align="left">
	<div id="header" class="jqDrag"><span style="float:left">Additional Accommodation</span>{{$close_other_accommodation}}<br style="clear:both" /></div>
					
	<div id="body">
		 <table border="0" cellspacing="2" cellpadding="2" align="center" width="100%">
			 <tbody>
				 <tr>
					 <td align="right" width="20%">Ward:</td>
					 <td><select name="ward_list" id="ward_list" onchange="populate_room_list()">{{html_options options=$ward_list}}</select></td>
				 </tr>
				 <tr>
					 <td align="right">Room:</td>
					 <td><select name="room_list" id="room_list" disabled="true" onchange="get_room_rate()">{{html_options options=$room_list}}</select></td>
				 </tr>
				 <tr>
					 <td align="right">Rate/Charge:</td>
					 <td>{{$room_rate}}</td>
				 </tr>
				 <tr>
					 <td align="right">Days:</td>
					 <td>{{$room_days}}</td>
				 </tr>  
				 <tr>
					 <td align="right">Excess(hrs):</td>
					 <td>{{$room_hours}}</td>
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
</div>
-->

	<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%">
		<tbody>
			<tr><td class="segPanelHeader" width="*">Transaction Details</td></tr>
			<tr>
				<td class="segPanel">
					<table  width="100%" class="transaction_details_table" cellpadding="3" cellspacing="0">
						<tr>
							<td width="20%" align="right"><b>Charge Area:</b></td>
							<td width="80%"><select name="charge_area_list" id="charge_area_list" onchange="change_charge_area()"><!--{{html_options options=$charge_area}}-->{{$charge_area}}</select></td>
						</tr>
						<tr>
							<td width="20%" align="right"><b>Transaction Type:</b></td>
							<td width="80%">{{html_radios name="transaction_type" options=$transaction_type selected=0 id="transaction_type"}}</td>
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
					<div id="opstaken" style="display:none;">
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<div id="or_main_schedule" align="left">
	<br/>
	
	<fieldset id="or_accomodation_field" style="display">
		<legend>Operating/Delivery Room Use</legend>
		<table class="segList" width="100%" id="accommodation_list">
			<thead>
				<tr>
					<th width="1%"></th>
					<th align="left" width="30%">Operating/Delivery Room</th>
					<th align="left" width="10%">RVU</th>
					<th align="left" width="10%">Multiplier</th>
					<th align="left" width="10%">Total Charge</th>
					<th align="right">Created by</th>
					<th align="right">Modified by</th>
					<th align="right">Date requested</th>
				</tr>
			</thead>
			<tbody>
				<tr id="empty_accommodation_row"><td colspan="10">No O.R. accomodation charged...</td></tr>      
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
	
	<!--original accomodation field
		<fieldset id="or_accomodation_field" style="display">
		<legend>Additional Accommodation</legend>
		<table class="segList" width="100%" id="accommodation_list">
			<thead>
				<tr>
					<th></th>
					<th align="left" width="10%">Room No.</th>
					<th align="left" width="40%">Room Type</th>
					<th align="left" width="10%">Rate</th>
					<th align="left" width="10%">No. of Days</th>
					<th align="left" width="10%">Excess(hrs)</th>
					<th align="right" width="20%">Total</th>
				</tr>
			</thead>
			<tbody>
				<tr id="empty_accommodation_row"><td colspan="7">Additional accommodation empty...</td></tr>      
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
	-->

	
	<fieldset id="or_misc_field" style="display">
		<legend>Miscellaneous Charges</legend>
		<table class="segList" width="100%" id="misc_list">
			<thead>
				<tr>
					<th></th>
					<th align="left">Service Code</th>
					<th align="left">Item Name</th>
					<th align="left">Description</th>
					<th align="left">Quantity</th>
					<th align="left">Unit Price</th>
					<th align="right">Total</th>
					<th align="right">Created by</th>
					<th align="right">Modified by</th>
					<th align="right">Date requested</th>
				</tr>
			</thead>
			<tbody>
				<tr id="empty_misc_row"><td colspan="10">Miscellaneous charges empty...</td></tr>
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
	 
	<fieldset id="or_medicine_field" style="display">
		<legend>Medicine and Supplies</legend>
		
				<table class="segList" width="100%" id="supplies-list">
					<thead>
						<tr>
							<th width="1%" nowrap="nowrap">&nbsp;</th>
							<th width="10%" nowrap="nowrap" align="left">Item No.</th>
							<th width="15%" nowrap="nowrap" align="left">Item Description</th>
							<th width="4%" nowrap="nowrap" align="center">Consigned</th>
							<th width="5%" align="center" nowrap="nowrap">Quantity</th>
							<th width="7%" align="right" nowrap="nowrap">Price(Orig)</th>
							<th width="7%" align="right" nowrap="nowrap">Price(Adj)</th>
							<th width="7%" align="right" nowrap="nowrap">Acc. Total</th>
							<th width="10%" align="center" nowrap="nowrap">Dosage</th>
							<th align="right">Created by</th>
							<th align="right">Modified by</th>
							<th align="right">Date requested</th>
						</tr>
					</thead>
					<tbody>
						<tr><td colspan="12">Order list is currently empty...</td></tr>
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
	
	<fieldset id="or_equipment_field" style="display">
		<legend>Equipments</legend>
		
				<table class="segList" width="100%" id="equipment_list">
					<thead>
						<tr>
							<th width="1%" nowrap="nowrap">&nbsp;</th>
							<th width="10%" nowrap="nowrap" align="left">Equipment</th>
							<th width="15%" nowrap="nowrap" align="left">Equipment Description</th>
							<th width="7%" align="center" nowrap="nowrap">Number of Usage</th>
							<th width="7%" align="right" nowrap="nowrap">Price(Orig)</th>
							<th width="7%" align="right" nowrap="nowrap">Price(Adj)</th>
							<th width="7%" align="right" nowrap="nowrap">Acc. Total</th>
							<th align="right">Created by</th>
							<th align="right">Modified by</th>
							<th align="right">Date requested</th>
						</tr>
					</thead>
					<tbody>

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
{{$confinement_type}}
{{$submitted}}
{{$mode}}
{{$is_laboratory}}   
{{$equipment_refno}}
{{$pharma_refno}}
{{$ward}}
{{$sess_userid}}
{{$sess_add_dt}}
	{{$form_end}}
	
</div>

