<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
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

<div id="approve_or_main_request" align="left">
	<div class="header"><span style="float:left">Approve OR Main Request</span>{{$close_approve}}<br style="clear:both" /></div>

	<div class="body">
		<!--This request cannot be approved unless a reason for approval is provided. <br/> -->
		<!--After approval, this request can be scheduled.<br/> -->
		{{$approve_label_msg}}   <br />
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

<div id="or_main_approve_request" align="left">
	{{$form_start}}


	<fieldset>
		<legend>Request Details</legend>
	<table>

		<tr>
			<td>Department</td>
			<td>:</td>
			<td><span class="value">{{$or_request_department}}</span></td>
		</tr>
		<!--<tr>
			<td>Operating Room</td>
			<td>:</td>
			<td><span class="value">{{$or_op_room}}</span></td>
		</tr> -->
		<tr>
			<td>Transaction</td>
			<td>:</td>
			<td><span id="transtype" class="value">{{$or_transaction_type}}</span></td>
		</tr>
		<!--<tr> Commented by Cherry 05-06-10
			<td>Priority</td>
			<td>:</td>
			<td><span class="value">{{$or_request_priority}}</span></td>
		</tr>-->
		<tr>
			<td>Date Requested</td>
			<td>:</td>
			<td><span class="value">{{$or_request_date}}</span></td>
		</tr>
		<!--<tr> Commented by Cherry 05-06-10
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
			<td>Date of Operation</td>
			<td>:</td>
			<td><span class="value">{{$or_operation_date}}</span></td>
		</tr>
		<!--<tr> Commented by Cherry 05-06-10
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

	<fieldset>
		<legend>Schedule Details</legend>
		<table>
			<tr>
				<td width="30%" align="left">OR Receipt{{$required_mark}}</td>
				<td width="20%" align="center">:</td>
				<td width="50%" align="left"><span class="value">{{$or_receipt}}</span></td>
			</tr>
			<tr>
				<td width="30%" align="left">OP Room{{$required_mark}}</td>
				<td width="20%" align="center">:</td>
				<td width="50%" align="left">
					<span class="value"> <select name="or_room" onchange="setBed();" id="or_room">
						{{html_options options=$or_room selected=$or_room_selected}}
					</select></span>
				</td>
			</tr>
			<tr>
				<td width="30%" align="left">Bed{{$required_mark}}</td>
				<td width="20%" align="center">:</td>
				<td width="50%" align="left"><span class="value">{{$sbed}}</span></td>
			</tr>
			<tr>
				<td width="30%" align="left">Remarks</td>
				<td width="20%" align="center">:</td>
				<td width="50%" align="left"><span class="value">{{$remarks}}</span></td>
			</tr>
		</table>
	</fieldset>

	{{$or_main_approve}}
	{{$or_main_disapprove}}
	<!--{{$or_approve_submit}}
	{{$or_disapprove_submit}}-->
	{{$or_main_cancel}}
	{{$current_bed_nr}}
	{{$submitted}}
	{{$or_main_refno}}
	{{$encounter_nr}}
	{{$hospital_number}}
	{{$mode}}
	{{$refno}}
	{{$or_request_nr}}
	{{$dept_nr}}
	{{$stat}}
	{{$pid}}
	{{$date_operation}}

	{{$form_end}}
</div>

</body>
</html>