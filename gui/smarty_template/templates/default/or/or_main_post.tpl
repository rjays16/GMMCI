<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>
{{$check_date_string}}
{{$or_main_css}}
{{foreach from=$javascript_array item=js}}
		{{$js}}
{{/foreach}}
<style>
select {
	border: 1px #CAC9B9 solid;
	margin: 0px;
}
</style>
</head>
<body>
<!-- Commented by Cherry 06-18-10
<div id="or_main_equipment" align="left">
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
</div>   -->


<div id="or_main_anesthesia" align="left">
	<div id="header" class="jqDrag"><span style="float:left">Select Anesthesia Procedure</span>{{$close_anesthesia}}<br style="clear:both" /></div>

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
<table id="or_anesthesia_table" align="left"></table>
</div>

	</div>
	{{$resize}}
</div>

<div id="or_main_schedule" align="left">
	{{$form_start}}

	<div id="toggler" onclick="toggle_details()">Request Details [Please click this bar to hide/unhide the request details]</div>
	<fieldset id="request_details">
	<table>
		<!--<tr>
			<td>Department</td>
			<td>:</td>
			<td><span class="value">{{$or_request_department}}</span></td>
		</tr>
		<tr>
			<td>Operating Room</td>
			<td>:</td>
			<td><span class="value">{{$or_op_room}}</span></td>
		</tr> -->
		<tr>
			<td>Transaction</td>
			<td>:</td>
			<td><span class="value">{{$or_transaction_type}}</span></td>
		</tr>
		<!--<tr>
			<td>Priority</td>
			<td>:</td>
			<td><span class="value">{{$or_request_priority}}</span></td>
		</tr> -->
		<tr>
			<td>Date Requested</td>
			<td>:</td>
			<td><span class="value">{{$or_request_date}}</span></td>
		</tr>
		<!--<tr>
			<td>Consent Signed</td>
			<td>:</td>
			<td><span class="value">{{$or_consent_signed}}</span></td>
		</tr>
		<tr>
			<td>Case</td>
			<td>:</td>
			<td><span class="value">{{$or_request_case}}</span></td>
		</tr> -->
		<tr>
			<td>Patient Name</td>
			<td>:</td>
			<td><span class="value">{{$patient_name}}</span></td>
		</tr>
		<tr>
			<td>Patient Gender</td>
			<td>:</td>
			<td><span class="value">{{$patient_gender}}</span></td>
		</tr>
		<tr>
			<td>Patient Age</td>
			<td>:</td>
			<td><span class="value">{{$patient_age}}</span></td>
		</tr>
		<tr>
			<td>Patient Address</td>
			<td>:</td>
			<td><span class="value">{{$patient_address}}</span></td>
		</tr>
		<tr>
			<td>Date and Time of Operation</td>
			<td>:</td>
			<td><span class="value">{{$or_operation_date}}</span></td>
		</tr>
		<!--<tr>
			<td>Estimated length of operation</td>
			<td>:</td>
			<td><span class="value">{{$or_est_op_length}}</span></td>
		</tr>
		<tr>
			<td>Case classification</td>
			<td>:</td>
			<td><span class="value">{{$or_case_classification}}</span></td>
		</tr>
		<tr>
			<td>Pre-operative diagnosis</td>
			<td>:</td>
			<td><span class="value">{{$pre_operative_diagnosis}}</span></td>
		</tr> -->
		<tr>
			<td>Operation procedure</td>
			<td>:</td>
			<td><span class="value">{{$operation_procedure}}</span></td>
		</tr>
		<tr>
			<td>Special requirements</td>
			<td>:</td>
			<td><span class="value">{{$or_special_requirements}}</span></td>
		</tr>
	</table>
	</fieldset>
	<br/>
	<div id="toggler" onclick="toggle_pre_op()">Pre-operative Details [Please click this bar to hide/unhide the pre_operative details]</div>
	<fieldset id="pre_op_details" style="display:none">
		<table>
			<tr>
				<td width="20%">
					<table>
						{{$pre_op_table}}
					</table>
				</td>
				<td width="50%" valign="top">
					Vital Signs Record:
					<table class="segList" width="100%" id="vital_sign_list">
					<thead>
						<tr>
							<th align="left" nowrap="nowrap" >Date Taken</th>
							<th align="left" nowrap="nowrap">Temperature</th>
							<th align="left" nowrap="nowrap">Pulse Rate</th>
							<th align="left" nowrap="nowrap">Respiratory Rate</th>
							<th align="left" nowrap="nowrap">Blood Pressure</th>
						</tr>
					</thead>
					<tbody>
						{{$vital_signs_table}}
					</tbody>
				</table>
				</td>
			</tr>

		</table>

	</fieldset>

	<span id="reminder">Required fields are marked with {{$required_mark}}</span>

	<fieldset id="post_operative">
		<legend>Post Operative Details</legend>
		<table>
			<tr>
				<td><label>Time Started:</label>{{$required_mark}}</td>
				<td>{{$post_time_started}}{{html_options name="pts_meridian" options=$pts_meridian selected="$pts_meridian_selected"}}</td>
				<td><span id="time_started_msg">{{$error_input}}</span></td>
			</tr>

			<tr>
				<td><label>Time Finished:</label>{{$required_mark}}</td>
				<td>{{$post_time_finished}}{{html_options name="ptf_meridian" options=$ptf_meridian selected="$ptf_meridian_selected"}}</td>
				<td><span id="time_finished_msg">{{$error_input}}</span></td>
			</tr>

			<tr>
				<td><label>Post Operative Diagnosis:</label>{{$required_mark}}</td>
				<td>{{$post_operative_diagnosis}}</td>
				<td><span id="post_op_msg">{{$error_input}}</span></td>
			</tr>
			<tr>
				<td><label>Operation Performed:</label>{{$required_mark}}</td>
				<td>{{$operation_performed}}</td>
				<td></td>
			</tr>
			<tr>
				<td><label>O.R. Technique:</label>{{$required_mark}}</td>
				<td>{{$or_technique}}</td>
				<td></td>
			</tr>
			<!--<tr>
				<td><label>Transferred to:</label></td>
				<td>{{html_options name="transferred_to" options=$transferred_to}}</td>
				<td></td>
			</tr> -->

		</table>
	</fieldset>


	<fieldset align="center">
		<legend>Surgeons</legend>
		<table width="100%">
			<tr>
				<td align="center" valign="top" width="50%">
				<table id="surgeon_list" class="segList" width="100%">
						<thead>
						<tr id="surgeon_list_header">
							<th colspan="3">Name of Surgeon(s)</th>
						</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
					{{$add_surgeon}}
				</td>
				<td align="center" valign="top" width="50%">
				<table id="assistant_surgeon_list" class="segList" width="100%">
						<thead>
						<tr id="assistant_surgeon_list_header">
							<th colspan="3">Name of Assistant Surgeon(s)</th>
						</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
					{{$add_assistant_surgeon}}
				</td>
			</tr>
		</table>
	</fieldset>

	<table width="100%">
		<tr>
			<td align="center" valign="top">
			 <fieldset>
					<legend>Anesthesiologists</legend>
					<table width="100%">
						<tr>
							<td align="center">
								<table id="anesthesiologist_list" class="segList" width="100%">
									<thead>
										<tr id="anesthesiologist_list_header">
											<th colspan="3">Name of Anesthesiologist(s)</th>
										</tr>
									</thead>
									<tbody>

									</tbody>
								</table>
								{{$add_anesthesiologist}}
							</td>

						</tr>
					</table>
				</fieldset>

			</td>
			<td align="center" valign="top">
				<fieldset>
					<legend>Nurses</legend>
					<table width="100%">
						<tr>
							<td align="center" valign="top">
								<table id="scrub_nurse_list" class="segList" width="100%">
									<thead>
										<tr id="scrub_nurse_list_header">
											<th colspan="3">Name of Scrub Nurse(s)</th>
										</tr>
									</thead>
									<tbody>

									</tbody>
								</table>
								{{$add_scrub_nurse}}
							</td>
							<td align="center" valign="top">
								<table id="circulating_nurse_list" class="segList" width="100%">
									<thead>
										<tr id="circulating_nurse_list_header">
											<th colspan="3">Name of Circulating Nurse(s)</th>
										</tr>
									 </thead>
									 <tbody>

									 </tbody>
								 </table>
								 {{$add_circulating_nurse}}
							</td>
						</tr>
					</table>
				</fieldset>
			</td>

		</tr>
	</table>
<!-- Commented by Cherry 06-27-10
	<fieldset>
		<legend>Anesthesia Procedure(s)</legend>
		<table id="anesthesia_procedure_list" class="segList" width="100%">
			<thead id="anesthesia_procedure_list_header">
				<tr>
					<th colspan="2">Anesthesia</th>
					<th>Anesthetics</th>
					<th>Time Begun</th>
					<th>Time Ended</th>
				</tr>
			</thead>
			<tbody>

			</tbody>
		</table>
		 <div align="center">
		{{$add_anesthesia_procedure}}
		</div>
		<fieldset id="post_operative">
			<legend>Complications attributable to anesthetic agent</legend>
				<table>
					<tr>
						<td>Intra Operative</td>
						<td>:</td>
						<td>{{$anesthetic_intra_operative}}</td>
					</tr>
				 <tr>
					 <td>Post Operative</td>
					 <td>:</td>
					 <td>{{$anesthetic_post_operative}}</td>
				 </tr>
				 <tr>
					 <td>Status of patient before <br/>leaving the OR theater</td>
					 <td>:</td>
					 <td>{{$anesthetic_patient_status}}</td>
				 </tr>

				</table>
		</fieldset>
	</fieldset> -->

	<fieldset>
		<legend>Anesthesia Procedure(s)</legend>
		<table id="anesthesia_procedure_list" class="segList" width="100%">
			<thead id="anesthesia_procedure_list_header">
				<tr>
					<th colspan="2">Anesthesia</th>
					<th>Time Begun</th>
					<th>Time Ended</th>
					<th colspan="2">Anesthetics</th>

				</tr>
			</thead>
			<tbody id="anesthesia_procedure_list_body">

			</tbody>
			<input type="hidden"	id="anesthesia_cnt" name="anesthesia_cnt"/>
		</table>
		<div align="center">
		{{$add_anesthesia_procedure}}
		</div>
		<fieldset id="post_operative">
			<legend>Complications attributable to anesthetic agent</legend>
				<table>
					<tr>
						<td>Intra Operative</td>
						<td>:</td>
						<td>{{$anesthetic_intra_operative}}</td>
					</tr>
				 <tr>
					 <td>Post Operative</td>
					 <td>:</td>
					 <td>{{$anesthetic_post_operative}}</td>
				 </tr>
				 <tr>
					 <td>Status of patient before <br/>leaving the OR theater</td>
					 <td>:</td>
					 <td>{{$anesthetic_patient_status}}</td>
				 </tr>

				</table>
		</fieldset>
	</fieldset>

	<fieldset>
		<legend>ICPM</legend>
			<table id="order-list" class="segList" width="100%">
				<thead>
					<tr>
						<th colspan="2">Code</th>
						<th colspan="2">Description</th>
						<th>RVU</th>
						<th>Multiplier</th>
						<th>Charge</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			<table width="100%">
				<tr>
					<td align="right" width="50%">{{$add_icpm}}</td>
					<td align="left" width="50%">{{$empty_icpm}}</td>
				</tr>
			</table>
	</fieldset>


	<fieldset>
		<legend>Medicine and Supplies</legend>

				<table class="segList" width="100%" id="supplies-list">
					<thead>
						<tr class="nav">
							<!-- <th colspan="9" align="left">List of current orders</th> -->
							<th colspan="9" align="left">List of current orders</th>
						</tr>
						<tr>
							<th width="1%" nowrap="nowrap">&nbsp;</th>
							<th width="10%" nowrap="nowrap" align="left">Item No.</th>
							<th width="*" nowrap="nowrap" align="left">Item Description</th>
							<th width="4%" nowrap="nowrap" align="center">Consigned</th>
							<th width="10%" align="center" nowrap="nowrap">Quantity</th>
							<th width="10%" align="right" nowrap="nowrap">Price(Orig)</th>
							<th width="10%" align="right" nowrap="nowrap">Price(Adj)</th>
							<th colspan="2" width="10%" align="right" nowrap="nowrap">Acc. Total</th>
							<!-- Added by Cherry 06-29-10 -->
							<!--<th width="10%" align="center" nowrap="nowrap">Dosage</th>  -->
							<!--<th width="10%" align="right" nowrap="nowrap">Added by</th>
							<th width="10%" align="right" nowrap="nowrap">Date added</th>  -->
						</tr>
					</thead>
					<tbody>
						<tr><td colspan="8">Order list is currently empty...</td></tr>
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

	<!-- Commented by Cherry 06-18-10
	<fieldset>
		<legend>Equipments</legend>

				<table class="segList" width="100%" id="equipment_list">
					<thead>
						<tr>
							<th width="1%" nowrap="nowrap">&nbsp;</th>
							<th width="10%" nowrap="nowrap" align="left">Equipment</th>
							<th width="*" nowrap="nowrap" align="left">Equipment Description</th>
							<th width="10%" align="center" nowrap="nowrap">Number of Usage</th>
							<th width="10%" align="right" nowrap="nowrap">Price(Orig)</th>
							<th width="10%" align="right" nowrap="nowrap">Price(Adj)</th>
							<th width="10%" align="right" nowrap="nowrap">Acc. Total</th>
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
	</fieldset>   -->


	<fieldset>
		<legend>Sponge Count</legend>
		<table class="segList" width="100%" id="sponge_list" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th>Sponge Type</th>
					<th>Initial Count</th>
					<th align="center" width="180px">
						<table>
							<tr>
								<td colspan="3" align="center"><b>First Count</b></td>
							</tr>
							<tr>
								<td align="center" width="52px">On Table</td>
								<td align="center" width="52px">On Floor</td>
								<td align="center" width="52px">TTL</td>
							</tr>
						</table>
					</th>
					<th align="center">
						<table>
							<tr>
								<td colspan="3" align="center"><b>Second Count</b></td>
							</tr>
							<tr>
								<td align="center" width="52px">On Table</td>
								<td align="center" width="52px">On Floor</td>
								<td align="center" width="52px">TTL</td>
							</tr>
						</table>
					</th>
				</tr>
			</thead>

			<tbody id="sponge_item_tbody">
			</tbody>

		</table>
	</fieldset>

	<!--<fieldset id="post_operative">
		<legend>Others</legend>
		<table>

			<tr>
				<td><label>Sponge Count</label></td>
				<td>:</td>
				<td>{{$sponge_count}}</td>
			</tr>

			<tr>
				<td><label>Needles</label></td>
				<td>:</td>
				<td>{{$needle_count}}</td>
			</tr>
			<tr>
				<td><label>Instruments</label></td>
				<td>:</td>
				<td>{{$instrument_count}}</td>
			</tr>
		</table>
	</fieldset> -->

	<fieldset id="post_operative">
		<legend>Others</legend>
		<table>
			<tr>
				<td><label>Sponge Count</label></td>
				<td>:</td>
				<td>{{$sponge_count}}</td>
			</tr>
			<!--added code by angelo
			start here-->
			<tr>
				<td><label>Sutures</label></td>
				<td>:</td>
				<td>{{$sutures}}</td>
			</tr>
			<tr>
				<td><label>OS</label></td>
				<td>:</td>
				<td>{{$sponge_os}}</td>
			</tr>
			<tr>
				<td><label>AP</label></td>
				<td>:</td>
				<td>{{$sponge_ap}}</td>
				<td>&nbsp;CB</td>
				<td>:</td>
				<td>{{$sponge_cb}}</td>
			</tr>
			<tr>
				<td><label>PP</label></td>
				<td>:</td>
				<td>{{$sponge_pp}}</td>
				<td>&nbsp;PEANUTS</td>
				<td>:</td>
				<td>{{$sponge_peanuts}}</td>
			</tr>
<!--			end here-->




			<tr>
				<td><label>Needles</label></td>
				<td>:</td>
				<td>{{$needle_count}}</td>
			</tr>
			<tr>
				<td><label>Instruments</label></td>
				<td>:</td>
				<td>{{$instrument_count}}</td>
			</tr>
			<tr>
				<td><label>Fluids</label></td>
				<td>:</td>
				<td>{{$fluids}}</td>
			</tr>
			<tr>
				<td><label>Drain Inserted</label></td>
				<td>:</td>
				<td>{{$drain_inserted}}</td>
			</tr>
			<tr>
				<td><label>Packs Inserted</label></td>
				<td>:</td>
				<td>{{$packs_inserted}}</td>
			</tr>
			<tr>
				<td><label>Blood Replacement</label></td>
				<td>:</td>
				<td>{{$blood_replacement}}</td>
			</tr>
			<tr>
				<td><label>Calculated Blood Loss</label></td>
				<td>:</td>
				<td>{{$blood_loss}}</td>
			</tr>
			<tr>
				<td><label>Tissues Removed</label></td>
				<td>:</td>
				<td>{{$tissues_removed}}</td>
			</tr>
			<tr>
				<td><label>Remarks</label></td>
				<td>:</td>
				<td>{{$remarks}}</td>
			</tr>
		</table>
	</fieldset>

	{{$or_main_submit}}
	{{$or_main_cancel}}
	{{$or_main_surgical_memo_report}}
	{{$clinical_summary}}
	{{$op_technique_form}}
	{{$patient_pid}}
	{{$encounter_nr}}
	{{$hospital_number}}
	{{$submitted}}
	{{$dept_nr}}
	{{$op_room}}
	{{$op_nr}}
	{{$refno}}
	{{$or_request_nr}}
	{{$or_main_refno}}
	{{$mode}}
	{{$pharma_refno}}
	{{$equipment_refno}}
	{{$is_in_or_main_post}}
	{{$pid}}

	{{$pharma_area}}
	{{$issc}}
	{{$discountid}}
	{{$discount}}
	{{$transaction_type}}
	{{$pharma_req_src}}
	{{$form_end}}


</div>


	<div id="billed_notification" style="display:none; cursor: default;padding:10px" align="left">
	The encounter of this patient is already billed.
	To edit the OR record of this patient, the billed encounter should be deleted first.
	Please ask assistance from the billing department for this matter.

	<br style="clear:both" /><br/>
	<input type="button" value="Cancel" id="cancel" />
</div>

</body>
</html>