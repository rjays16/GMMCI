{{$form_start}}
<div style="width:100%;height:90%">
	<table border="0" cellspacing="1" cellpadding="0" width="90%" align="center" style="">
		<tbody>
			<tr>
				<td colspan="4" class="segPanelHeader">Update Room Status</td>
			</tr>
			<tr>
				<td class="segPanel">
					<table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
						<tbody>
							<tr>
								<td colspan="3" nowrap="nowrap" align="left" style="width:50%">{{$s_or_main_refno}}</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="left" style="width:30%"><b>Case Number</b></td>
								<td nowrap="nowrap" align="left" style="width:1%"><b>:</b></td>
								<td nowrap="nowrap" align="left" style="width:50%">{{$s_case_number}}</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="left" style="width:30%"><b>Room Name</b></td>
								<td nowrap="nowrap" align="left" style="width:1%"><b>:</b></td>
								<td nowrap="nowrap" align="left" style="width:50%">{{$s_room_name}}</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="left" style="width:30%"><b>Patient Name</b></td>
								<td nowrap="nowrap" align="left" style="width:1%"><b>:</b></td>
								<td nowrap="nowrap" align="left" style="width:50%">{{$s_patient_name}}</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="left" style="width:30%"><b>Date of Operation</b></td>
								<td nowrap="nowrap" align="left" style="width:1%"><b>:</b></td>
								<td nowrap="nowrap" align="left" style="width:50%">{{$s_operation_date}}</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="left" style="width:30%"><b>Done Date</b></td>
								<td nowrap="nowrap" align="left" style="width:1%"><b>:</b></td>
								<td nowrap="nowrap" align="left" style="width:50%">{{$s_done_date}}</td>
							</tr>
							<tr>
								<td nowrap="nowrap" align="left" style="width:30%"><b>Room Status</b></td>
								<td nowrap="nowrap" align="left" style="width:1%"><b>:</b></td>
								<td nowrap="nowrap" align="left" style="width:50%">{{$s_room_status}}</td>


							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">{{$s_room_save}}&nbsp;&nbsp;{{$s_room_cancel}}</td>
			</tr>

		</tbody>
	</table>
</div>
{{$form_end}}