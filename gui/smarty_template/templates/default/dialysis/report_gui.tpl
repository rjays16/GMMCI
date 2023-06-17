{{$sFormStart}}
<div style="width:450px" align="center">
	<table width="100%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="2" cellpadding="2">
		<tbody>
			<tr>
				<td align="left" class="segPanelHeader" colspan="2"><strong>Report options</strong></td>
			</tr>
			<tr>
				<td class="segPanel">
					<table width="100%" border="0" cellpadding="2" cellspacing="2" style="font:12px Arial;color:#000000">
						<tr align="center">
							<td width="150px" align="right"><label>Select Report Type: </label></td>
							<td align="left">
								<select class="segInput" name="selreport" id="selreport" onchange="selOnChange()">
									<option value="daily">Daily Transaction</option>
									<option value="income">Income Report</option>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2"><hr width="95%" size="1" style="opacity:0.5"/></td>
						</tr>
						<tr id="seldatefrom" style="display:">
								<td align="right">Date From</td>
								<td>{{$seldate_from}}{{$seldatefrom_js}}</td>
						</tr>
						<tr id="seldateto" style="display:">
								<td align="right" >Date To</td>
								<td>{{$seldate_to}}{{$seldateto_js}}</td>
						</tr>
						<tr id="seltimefrom" style="display:none">
							<td align="right"><label>Time From</label></td>
							<td>{{$sShift_from}}</td>
						</tr>
						<tr id="seltimeto" style="display:none">
							<td align="right"><label>Time From</label></td>
							<td>{{$sShift_to}}</td>
						</tr>
						<tr>
							<td colspan="2" align="center">{{$view_btn}}</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
{{$sFormEnd}}