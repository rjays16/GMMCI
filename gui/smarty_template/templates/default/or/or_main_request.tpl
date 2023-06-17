<!DOCTYPE html PUBLIC "//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>
{{$check_date_string}}
{{$or_main_css}}
{{foreach from=$javascript_array item=js}}
		{{$js}}
{{/foreach}}
<script>


</script>
</head>
<body onload="preset();">

<div id="or_main_request" align="left">
	{{$form_start}}
	<span id="reminder">Required fields are marked with {{$required_mark}}</span>


	<fieldset>
	<legend>Patient Information</legend>
	<table>
		<tr>
			<td width="210px"><label>Patient Name:</label> {{$required_mark}}</td>
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
		<legend>Request Details</legend>
	<table>
		<tr>
			<td><label>Department</label> {{$required_mark}} </td>
				<td>
					<select name="or_request_department" id="or_request_department">
						{{html_options options=$or_request_department selected=$or_request_department_selected}}
					</select>
				</td>
					<td valign="middle"><span id="or_request_department_msg">{{$error_input}}</span></td>
		</tr>
		<tr>
			<td valign="middle"><label>Transaction:</label> {{$required_mark}}</td>
			<td>{{html_radios name="or_transaction_type" options=$or_transaction_type selected=$or_transaction_type_selected id="or_transaction_type"}}</td>
			<td valign="middle"><span id="transaction_type_msg">{{$error_input}}</span></td>
			<td></td>
		</tr>
		<tr>
			<td valign="middle"><label>OR Type:</label> {{$required_mark}}</td>
			<td>
				<select name="or_type" id="or_type">
					{{html_options options=$or_type selected=$or_type_selected}}
				</select>
			</td>
			<td valign="middle"><span id="transaction_type_msg">{{$error_input}}</span></td>
			<td></td>
		</tr>
		<tr>
			<td valign="middle"><label>Priority:</label> {{$required_mark}}</td>
			<td>{{html_radios name="or_request_priority" options=$or_request_priority separator="<br/>" selected=$or_request_priority_selected id="or_request_priority"}}</td>
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
			<td>
					<select name="ward" id="ward">
						{{html_options options=$ward selected=$ward_selected}}
					</select>
			</td>
		</tr>

		<!--Added by Cherry 04-28-10-->
		<tr>
			<td><label>Requesting SROD/Surgeon:</label> {{$required_mark}} </td>
				<td>
					<select name="or_doctor" id="or_doctor">
						{{html_options options=$or_doctor selected=$or_doctor_selected}}
					</select>
				</td>
				 <td valign="middle"><span id="or_doctor_msg">{{$error_input}}</span></td>
		</tr>

		<tr>
			<td><label>Date and Time Received:</label></td>
			<td>{{$or_received_date_display}}{{$or_received_date}}</td>
			<td>{{$or_received_dt_picker}}</td>
			<td>{{$or_received_calendar_script}}</td>
		</tr>
	</table>
	</fieldset>

	<fieldset>
		<legend>Pre-operation</legend>
		<table>
			<tr>
				<td width="210px"><label>Date and Time of Operation:</label>{{$required_mark}}</td>
				<td width="160px">{{$or_operation_date_display}}{{$or_operation_date}}</td>
				<td>{{$or_operation_dt_picker}}</td>
				<td>{{$or_operation_calendar_script}}</td>
			</tr>

			<tr>
				<td><label>Procedure:</label>{{$required_mark}}</td>
				<td>{{$package_name}}</td>
				<td>{{$procedure_select}}</td>
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
				<td width="180px">{{html_checkboxes name="or_special_requirements" options=$or_special_requirements separator="<br/>" selected=$or_special_requirements_selected id="or_special_requirements"}}</td>
				<td valign="middle"><span id="special_req_msg">{{$error_input}}</span></td>
			</tr>
		</table>
	</fieldset>

	{{$package_id}}

	{{$or_main_submit}}
	{{$or_main_cancel}}
	{{$or_main_print}}
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