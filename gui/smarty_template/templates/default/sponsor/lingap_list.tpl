{{$sFormStart}}
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
							<td width="5%"><label for="patient_options" class="segInput">Search patient</label></td>
							<td>
								<div id="patient_options_panel">
									<span id="p_name" style="">{{$patientName}}</span><br/>
									<span style="font-family:Arial">Enter PID or the first few letters of patient's last name</span>
								</div>
							</td>
						</tr>
						<tr>
							<td width="50" align="right">{{$statusCheck}}</td>
							<td width="5%"><label for="cost_center_options" class="segInput">Select cost center</label></td>
							<td>
								<div id="cost_center_options_panel">
									{{$costCenterOptions}}
								</div>
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
<div class="dashlet" style="margin-top:10px; width:800px;">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 11px Tahoma;">
		<tbody>
			<tr>
				<td valign="top"><h1 style="white-space:nowrap">Request list</h1></td>
			</tr>
		</tbody>
	</table>
	<div id="show_requests" align="center"></div>
</div>
{{$sFormEnd}}