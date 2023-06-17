{{$form_start}}
<div style="margin-top:20px; width:600px">
	<table width="100%" cellpadding="2" cellspacing="1" align="center" border="0">
		<tbody>
			<tr>
				<td colspan="2" class="segPanelHeader">Search options</td>
			</tr>
			<tr>
				<td class="segPanel" nowrap="nowrap" align="right">
					<table width="100%" cellpadding="2" cellspacing="1" border="0">
						<tr>
							<td width="50" align="right">{{$patientCheck}}</td>
							<td width="5%"><label class="segInput" for="patient_check">Select patient</label></td>
							<td>
								{{$patientOptions}}
								<span id="p_name" style="display;">{{$pSearchName}}</span>
							</td>
						</tr>
						<tr>
							<td width="50" align="right">{{$dateCheck}}</td>
							<td width="5%"><label class="segInput" for="date_check">Select date</label></td>
							<td>
									{{$dateOptions}}
									<span id="d_specific" style="display:none">{{$dateSpecific}}{{$dateSpecificIcon}}{{$dateSpecificJs}}</span>
									<span id="d_between1" style="display:none">{{$dateBetween1}}{{$dateBetween1Icon}}{{$dateBetween1Js}}</span>
									<span id="d_between2" style="display:none">{{$dateBetween2}}{{$dateBetween2Icon}}{{$dateBetween2Js}}</span>
							</td>
						</tr>
						<tr>
							<td></td>
							<td colspan="2"><button class="segButton" onclick="startSearch();return false;"><img src="../../gui/img/common/default/folder_explore.png"/>Search</button></td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="dashlet" style="margin-top:10px; width:770px">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 11px Tahoma;">
		<tbody>
			<tr>
				<td width="30%" valign="top"><h1 style="white-space:nowrap">List of walkin</h1></td>
			</tr>
		</tbody>
	</table>
</div>
<div id="show_walkin" align="center">
</div>
{{$form_end}}