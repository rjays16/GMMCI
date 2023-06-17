{{* grant.tpl  Form template for Grants module *}}
{{$sFormStart}}
{{$sEntryId}}
<div style="width:420px; margin-top:5px" align="left">
	{{$sSelectMode}}
	<table border="0" cellspacing="1" cellpadding="2" align="center" width="100%;margin:4px">
		<tr>
			<td class="segPanel" align="left" valign="top">
				<table width="95%" border="0" cellpadding="0" cellspacing="2" style="font:normal 12px Arial; margin:2px" >
					<tr>
						<td width="1" style="padding-right:10px" align="left" valign="middle">
							<label>Bill number</label>
						</td>
						<td width="*"  valign="middle">
							{{$sBillNr}}
						</td>
					</tr>
					<tr>
						<td width="1" style="padding-right:10px; white-space:nowrap" align="left" valign="middle">
							<label>HRN</label>
						</td>
						<td valign="middle">
							{{$sPID}}
						</td>
					</tr>
					<tr>
						<td width="1" style="padding-right:10px; white-space:nowrap" align="left" valign="middle">
							<label>Patient name</label>
						</td>
						<td valign="middle">
							{{$sPatientName}}
						</td>
					</tr>
					<tr>
						<td width="1" style="padding-right:10px; white-space:nowrap" align="left" valign="middle">
							<label >Lingap control no</label>
							<span class="required">*</span>
						</td>
						<td valign="middle">
							{{$sControlNr}}
						</td>
					</tr>
					<tr>
						<td width="1" style="padding-right:10px; white-space:nowrap" align="left" valign="middle">
							<label>Adv. purchase</label>
						</td>
						<td valign="middle">
							{{$sIsAdvance}}
						</td>
					</tr>
					<tr><td></td></tr>
					<tr>
						<td width="1" style="padding-right:10px; white-space:nowrap" align="left">
							<label>Entry date</label>
							<span class="required">*</span>
						</td>
						<td>
							{{$sEntryDate}}
							{{$sCalendarIcon}}
							{{$jsCalendarSetup}}
						</td>
					</tr>
					<tr>
						<td width="1" style="padding-right:10px; white-space:nowrap">
							<label>Remarks</label>
						</td>
						<td>
							{{$sRemarks}}
						</td>
					</tr>
					<tr>
						<td width="1" style="padding-right:10px; white-space:nowrap" align="left" valign="middle">
							<label>Amount due</label>
						</td>
						<td valign="middle">
							{{$sAmountDue}}
						</td>
					</tr>
					<tr>
						<td width="1" style="padding-right:10px; white-space:nowrap">
							<label>Grant amount<label>
						</td>
						<td>
							{{$sGrantAmount}}
							{{$sPartialGrant}}
							{{$sFullGrant}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<div style="text-align:left; padding:2px">
		{{$sContinueButton}}
		{{$sBreakButton}}
	</div>
</div>
<div id="hidden-inputs" style="display:none">
{{$sHiddenInputs}}
</div>
{{$sFormEnd}}
{{$sTailScripts}}