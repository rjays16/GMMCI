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

<div id="approve_or_main_request" align="left">
	<div class="header"><span style="float:left">Approve OR Main Request</span>{{$close_approve}}<br style="clear:both" /></div>

	<div class="body">
		This request cannot be approved unless a reason for approval is provided. <br/>
		<!--After approval, this request can be scheduled.<br/> -->
		{{$form_approve}}
		{{$approve_reason_label}}
		{{$error_msg}}

		{{$approve_reason}}

		{{$or_approve_submit}}
		{{$or_approve_cancel}}
		{{$submitted}}
		{{$or_main_refno}}
		{{$mode}}
		<!--{{$patient_pid}}-->
	{{$encounter_nr}}
	{{$hospital_number}}
	{{$mode}}
	{{$submitted}}
	{{$dept_nr}}
	{{$op_room}}
	{{$op_nr}}
	{{$refno}}
	{{$or_request_nr}}


	<!-- Added by Cherry 05-13-10 -->
	{{$pid}}
	<!-- END -->
		{{$form_end}}
		<br style="clear:both" />

	</div>
</div>

<div id="disapprove_or_main_request" align="left">
	<div class="header"><span style="float:left">Disapprove OR Main Request</span>{{$close_disapprove}}<br style="clear:both" /></div>

	<div class="body">
		This request cannot be disapproved unless a reason for disapproval is provided. <br/>

		{{$form_disapprove}}
		{{$disapprove_reason_label}}
		{{$error_msg}}
		{{$disapprove_reason}}

		{{$or_disapprove_submit}}
		{{$or_disapprove_cancel}}
		{{$submitted}}
		{{$or_main_refno}}
		{{$mode}}
		<!--{{$patient_pid}} -->
	{{$encounter_nr}}
	{{$hospital_number}}
	{{$mode}}
	{{$submitted}}
	{{$dept_nr}}
	{{$op_room}}
	{{$op_nr}}
	{{$refno}}
	{{$or_request_nr}}
	<!-- Added by Cherry 05-13-10 -->
	{{$pid}}
	<!-- END -->
		{{$form_end}}
		<br style="clear:both" />

	</div>
</div>

<div id="or_main_schedule" align="left">
	{{$form_start}}

	<div id="toggler" onclick="toggle_details()">Request Details [Please click this bar to hide/unhide the request details]</div>
	<fieldset id="request_details">
		<fieldset>
			<legend>Patient Information</legend>
				<table>
					<tr>
						<td width="210px"><label>Patient Name:</label><!-- {{$required_mark}}--></td>
						<td width="160px"><strong>{{$patient_name}}</strong></td>
						<td>{{$patient_select_button}}</td>
						<td><span id="patient_name_msg">{{$error_input}}</span></td>
					</tr>
					<tr>
						<td><label>Patient Gender:</label></td>
						<td>{{$patient_gender}}</td>
						<td>{{$error_input}}</td>
					</tr>

					<tr>
						<td><label>Patient Age:</label></td>
						<td>{{$patient_age}}</td>
						<td>{{$error_input}}</td>
					</tr>
					<tr>
						<td><label>Patient Address:</label></td>
						<td>{{$patient_address}}</td>
						<td>{{$error_input}}</td>
					</tr>
					<tr>
						<td><label>Hospital Number:</label></td>
						<td>{{$patient_hospital_number}}</td>
						<td>{{$error_input}}<td>
					</tr>
				</table>
		</fieldset>

		<fieldset>
		<legend>Other Details</legend>
			<table>
				<tr>
					<td><label>Department</label> {{$required_mark}} </td>
						<td>{{$or_request_department}}</td>
				</tr>
				<tr>
					<td valign="middle"><label>Transaction:</label> {{$required_mark}}</td>
					<td>{{$or_transaction_type}}</td>
					<td valign="middle"><span id="transaction_type_msg">{{$error_input}}</span></td>
					<td></td>
				</tr>
				<tr>
					<td valign="middle"><label>OR Type:</label> {{$required_mark}}</td>
					<td>{{$or_type}}</td>
					<td valign="middle"><span id="transaction_type_msg">{{$error_input}}</span></td>
					<td></td>
				</tr>
				<tr>
					<td valign="middle"><label>Priority:</label> {{$required_mark}}</td>
					<td>{{$or_request_priority}}</td>
					<td valign="middle"><span id="priority_msg">{{$error_input}}</span></td>
					<td></td>
				</tr>

				<tr>
					<td><label>Date and Time Requested:</label></td>
					<td>{{$or_request_date_display}}{{$or_request_date}}</td>
					<td>{{$or_request_dt_picker}}</td>
					<td>{{$or_request_calendar_script}}</td>
				</tr>

				<tr>
					<td><label>Ward:</label></td>
					<td>{{$ward}}</td>
				</tr>

				<!--Added by Cherry 04-28-10-->
				<tr>
					<td><label>Requesting SROD/Surgeon:</label> {{$required_mark}} </td>
						<td>
								{{$or_doctor}}
						</td>
						 <td valign="middle"><span id="or_doctor_msg">{{$error_input}}</span></td>
				</tr>

				<tr>
					<td><label>Date and Time Received:</label></td>
					<td>{{$or_received_date_time}}</td>
				</tr>

			</table>
		</fieldset>

		<fieldset>
		<legend>Pre-operation Details</legend>
		<table>
			<tr>
				<td width="210px"><label>Date and Time of Operation:</label></td>
				<td width="160px">{{$or_operation_date}}</td>
			</tr>

			<tr>
				<td><label>Procedure:</label></td>
				<td>{{$package_name}}</td>
				<td></td>
			</tr>

			<tr>
				<td valign="middle"><label>Pre-operative diagnosis:</label></td>
				<td>{{$pre_operative_diagnosis}}</td>
				<td></td>
			</tr>

			<tr>
				<td valign="middle"><label>Remarks:</label></td>
				<td>{{$remarks}}</td>
				<td></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>Requirements</legend>
		<table>
			<tr>
				<td width="210px" valign="middle"><label>Special requirements:</label> {{$required_mark}}</td>
				<td width="180px">{{html_checkboxes name="or_special_requirements" options=$or_special_requirements separator="<br/>" selected=$or_special_requirements_selected}}</td>
				<td valign="middle"><span id="special_req_msg">{{$error_input}}</span></td>
			</tr>
		</table>
	</fieldset>

	</fieldset>
	<!--<fieldset id="request_details">
	<table>
		<tr>
			<td>Department</td>
			<td>:</td>
			<td><span class="value">{{$or_request_department}}</span></td>
		</tr>
		<tr>
			<td>Operating Room</td>
			<td>:</td>
			<td><span class="value">{{$or_op_room}}</span></td>
		</tr>
		<tr>
			<td>Transaction</td>
			<td>:</td>
			<td><span class="value">{{$or_transaction_type}}</span></td>
		</tr>
		<tr>
			<td>Priority</td>
			<td>:</td>
			<td><span class="value">{{$or_request_priority}}</span></td>
		</tr>
		<tr>
			<td>Date Requested</td>
			<td>:</td>
			<td><span class="value">{{$or_request_date}}</span></td>
		</tr>
		<tr>
			<td>Consent Signed</td>
			<td>:</td>
			<td><span class="value">{{$or_consent_signed}}</span></td>
		</tr>
		<tr>
			<td>Case</td>
			<td>:</td>
			<td><span class="value">{{$or_request_case}}</span></td>
		</tr>
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
	</fieldset> -->
	<br/>
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

	<fieldset align="center">
		<legend>Approve/Disapprove Request</legend>
		<table>
			<tr>
				<td width="210px"><label>Remarks:</label></td>
				<td width="160px">{{$reason}}</td>
			</tr>
		</table>
	</fieldset>

	{{$or_approve_submit}}
	{{$or_disapprove_submit}}
	<!--{{$submit_schedule}}-->
	{{$cancel_schedule}}
	{{$submitted}}
	{{$or_main_refno}}
	{{$mode}}
	{{$patient_pid}}
	{{$encounter_nr}}
	{{$hospital_number}}
	{{$mode}}
	{{$submitted}}
	{{$dept_nr}}
	{{$op_room}}
	{{$op_nr}}
	{{$refno}}
	{{$or_request_nr}}
	{{$form_end}}
</div>

</body>
</html>