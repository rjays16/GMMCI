{{$sFormStart}}
<div style="width:530px;margin-top:20px;">
		<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center" style="font:12px Arial;">
			<tbody>
				<tr>
					<td class="segPanelHeader" colspan="2">Transaction Details</td>
				</tr>
				<tr>
					<td class="segPanel">
						<table border="0" cellpadding="2" cellspacing="2" width="100%" align="center"  style="font:12px Arial;color:#000000">
							<tr>
								<td width="135px"><label>HRN:</label></td>
								<td>{{$patient_id}}</td>
							</tr>
							<tr>
								<td><label>Reference No:</label></td>
								<td>{{$reference_no}}</td>
							</tr>
							<tr>
								<td><label>Transaction Date:</label></td>
								<td>{{$requestDate}}{{$sCalendarIcon}}{{$jsCalendarSetup}}</td>
							</tr>
							<!--<tr>
								<td><label>No. of Visits:</label></td>
								<td>{{$visit_no}}</td>
							</tr>-->
							<tr>
								<td><label>Patient Name:</label></td>
								<td>{{$patient_name}}</td>
							</tr>
							<tr>
								<td><label>Requesting Doctor</label></td>
								<td>{{$requestDoctors}}</td>
							</tr>
							<tr>
								<td><label>Attending Nurse</label></td>
								<td>{{$requestNurses}}</td>
							</tr>
							<tr>
								<td><label>Request Type</label></td>
								<td>{{$requestDialysisType}}</td>
							</tr>
							<tr>
								<td><label>Remarks</label></td>
								<td>{{$requestRemarks}}</td>
							</tr>
							<tr>
								<td><label>Encoded by</label></td>
								<td>{{$requestEncoder}}</td>
							</tr>
							<tr>
								<td><label>Session</label></td>
								<td>{{$requestSession}}</td>
							</tr>
							<tr>
								<td><label>Machine No.</label></td>
								<td>{{$machineList}}</td>
							</tr>
							<tr>
								<td colspan="2"><strong>VITAL SIGNS</strong>{{$vitalsign_no}}</td>
							</tr>
							<tr valign="top">
								<td align="right" nowrap="nowrap"><label>Blood Pressure</label></td>
								<td align="left" nowrap="nowrap" valign="middle">{{$bp_systole}}&nbsp;/&nbsp;{{$bp_diastole}}&nbsp;<span style="font: 11px Arial;">mm Hg</span></td>
							</tr>
							<tr valign="top">
								<td align="right" nowrap="nowrap"><label>Temperature</label></td>
								<td align="left" nowrap="nowrap" valign="middle">{{$temperature}}&nbsp;<span style="font: 11px Arial;">°C</span></td>
							</tr>
							<tr valign="top">
								<td align="right" nowrap="nowrap"><label>Weight</label></td>
								<td align="left" nowrap="nowrap" valign="middle">{{$weight}}&nbsp;<span style="font: 11px Arial;">kg</span></td>
							</tr>
							<tr valign="top">
								<td align="right" nowrap="nowrap"><label>Resp. Rate</label></td>
								<td align="left" nowrap="nowrap" valign="middle">{{$resp_rate}}&nbsp;<span style="font: 11px Arial;">br/m</span></td>
							</tr>
							<tr valign="top">
								<td align="right" nowrap="nowrap"><label>Pulse Rate</label></td>
								<td align="left" nowrap="nowrap" valign="middle">{{$pulse_rate}}&nbsp;<span style="font: 11px Arial;">b/m</span></td>
							</tr>
							<tr>
								<td align="right" nowrap="nowrap" colspan="4">
									{{$submitBtn}}{{$cancelBtn}}{{$historyBtn}}{{$continuousReportBtn}}
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
</div>
{{$submitted}}
{{$encounter_nr}}
{{$pid}}
{{$doctor_nr}}
{{$nurse_nr}}
{{$log_reason}}
{{$requestStatus_saved}}
{{$sHiddenInputs}}
{{$jsCalendarSetup}}
{{$sFormEnd}}
{{$sTailScripts}}