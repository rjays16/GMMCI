<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>
{{$check_date_string}}
{{$or_main_css}}
{{foreach from=$javascript_array item=js}}
				{{$js}}
{{/foreach}}

</head>
<body>

<div id="or_main_schedule" align="left">
		{{$form_start}}

		<!--<div id="toggler" onclick="toggle_details()">Request Details [Please click this bar to hide/unhide the request details]</div>
		<fieldset id="request_details">  -->
		<fieldset>
		<legend>Request Details</legend>
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
				</tr>-->
				<tr>
						<td>Transaction</td>
						<td>:</td>
						<td><span class="value">{{$or_transaction_type}}</span></td>
				</tr>
				<!--<tr>
						<td>Priority</td>
						<td>:</td>
						<td><span class="value">{{$or_request_priority}}</span></td>
				</tr>-->
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
				</tr>-->
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
				</tr>-->
				 <tr>
						<td>Date and Time of Operation</td>
						<td>:</td>
						<td><span class="value">{{$or_operation_date}}</span></td>
				</tr>
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
		<!--<br/>
		<fieldset>
				<legend>Operation Details</legend>
				<table>
						<tr>
								<td valign="bottom"><label>Date and time of operation:</label></td>
								<td valign="bottom">{{$or_operation_date_display}}{{$or_operation_date_value}}</td>
								<td>{{$or_operation_dt_picker}}</td>
								<td>{{$or_operation_calendar_script}}</td>
						</tr>

				</table>
		</fieldset> -->

		<br/>
		<fieldset>
				<legend>Pre-operation Checklist</legend>  
				
				<span><font size="1" color="#6464FF">Mandatory checklist items are marked with (*).<br/></font></span>
				<br/>
				<div id="preopchecklist">
					{{html_checkboxes name="question" options=$questions selected=$questions_selected separator="<br/>"}}
					{{$checkboxes_with_details}}                                                        
				</div>                                                   
		 <br/>
				 <fieldset>
					<legend>Vital Signs</legend>
				 <table  style="font: normal 12px Verdana">

					<tr>
						<td><label>Temperature</label></td>
						<td>:</td>
						<td>{{$temperature}}</td>
						<td><span id="temp_msg">{{$error_input}}</span></td>
					</tr>
					<tr>
						<td>Pulse Rate</td>
						<td>:</td>
						<td>{{$pulse}}</td>
						<td><span id="pulse_msg">{{$error_input}}</span></td>
					</tr>
					<tr>
						<td>Respiratory Rate</td>
						<td>:</td>
						<td>{{$respiratory}}</td>
						<td><span id="resp_msg">{{$error_input}}</span></td>
					</tr>
					<tr>
						<td>Blood Pressure</td>
						<td>:</td>
						<td>{{$bp_systol}}/{{$bp_diastol}}</td>
						<td align="left"><span id="bp_systole">{{$error_input}}</span><span id="bp_diastole">{{$error_input}}</span></td>
					</tr>
				</table>
				</fieldset>
		</fieldset>
		<br/>
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
														 {{$surgeon_list_body}}
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
														 {{$asst_surgeon_list_body}}
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
																				{{$anesthesiologist_list_body}}
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
																				 {{$scrub_nurse_list_body}}
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
																						{{$rotating_nurse_list_body}}
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


		<!--{{$submit_schedule}}
		{{$cancel_schedule}}-->
		{{$submit_pre_operation}}
		{{$cancel_pre_operation}}
		{{$or_asu_info_sheet_report}}
		{{$patient_pid}}
		{{$pid}}
		{{$encounter_nr}}
		{{$hospital_number}}
		{{$mode}}
		{{$submitted}}
		{{$dept_nr}}
		{{$op_room}}
		{{$op_nr}}
		{{$refno}}
		{{$or_request_nr}}
		{{$or_main_refno}}
		{{$list_surgeon}}
		{{$list_asst_surgeon}}
		{{$checkbox_ids}}
		{{$detail_ids}}
		{{$form_end}}
</div>

</body>
</html>