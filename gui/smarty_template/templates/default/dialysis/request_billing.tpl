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
							<td width="5%"><label class="segInput">Select patient</label></td>
							<td>
								{{$patientOptions}}
								<span id="p_name" style="display;">{{$pSearchName}}</span>
								<span id="p_pid" style="display:none">{{$pSearchId}}</span>
								<span id="p_enc" style="display:none">{{$pSearchEnc}}</span>
							</td>
						</tr>
						<tr>
							<td width="50" align="right">{{$dateCheck}}</td>
							<td width="5%"><label class="segInput">Select date</label></td>
							<td>
								{{$dateOptions}}
								<span id="specific" style="display:none">{{$specificDate}}{{$specificDate_js}}</span>
								<span id="between" style="display:none">{{$seldate_from}}{{$seldatefrom_js}}&nbsp;&nbsp;{{$seldate_to}}{{$seldateto_js}}</span>
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
<div class="dashlet" style="margin-top:10px; width:700px">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 11px Tahoma;">
		<tbody>
			<tr>
				<td width="30%" valign="top"><h1 style="white-space:nowrap">Request list</h1></td>
			</tr>
		</tbody>
	</table>
</div>
<div id="show_requests" align="center">
</div>
{{$form_end}}