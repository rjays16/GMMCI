{{$sFormStart}}
<div style="width:630px;margin-top:20px;">
		<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center" style="font:12px Arial;">
			<tbody valign="middle">
				<tr>
					<td class="segPanelHeader" colspan="2">Patient Data</td>
				</tr>
				<tr>
					<td class="segPanel">
						<table border="0" cellpadding="2" cellspacing="2" width="100%" align="center"  style="font:12px Arial;color:#000000">
							<tr>
								<td width="135px"><label>HRN:</label></td>
								<td>{{$patient_id}}</td>
							</tr>
							<tr>
								<td><label>Case Number:</label></td>
								<td>{{$casenum}}</td>
							</tr>
							<tr>
								<td><label>Patient Name:</label></td>
								<td>{{$patient_name}}</td>
							</tr>
							<tr>
								<td><label>Birthday:</label></td>
								<td>{{$birthday}}</td>
							</tr>
							<tr>
								<td><label>Ward:</label></td>
								<td>{{$patient_ward}}</td>
							</tr>
							<!--<tr>
								<td><label>Room:</label></td>
								<td></td>
							</tr>-->
							<tr>
								<td>&nbsp;</td>
							</tr>
						</table>
						<table border="0" cellpadding="2" cellspacing="2" width="100%" align="center"  style="font:12px Arial;color:#000000">
							<tr>
								<td width="20%" style="font:bold 12px Arial;" align="center"><label><strong>Options:</strong></label></td>
								<td width="10%">&nbsp;</td>
								<td width="70%">&nbsp;</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>{{$patient_details}}</td>
								<td>{{$patient_details_info}}</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>{{$nurse_notes}}</td>
								<td>{{$nurse_notes_info}}</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>{{$patient_transfer}}</td>
								<td>{{$patient_transfer_info}}</td>
							</tr>
							<!--<tr>
								<td>&nbsp;</td>
								<td>{{$patient_discharge}}</td>
								<td>{{$patient_discharge_info}}</td>
							</tr>-->
							<tr>
								<td>&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<!--<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td align="center">{{$cancelBtn}} </td>
				</tr>   -->
			</tbody>
		</table>
</div>
{{$submitted}}
{{$encounter_nr}}
{{$pid}}
{{$ward}}
{{$ward_nr}}
{{$sHiddenInputs}}
{{$jsCalendarSetup}}
{{$sFormEnd}}
{{$sTailScripts}}