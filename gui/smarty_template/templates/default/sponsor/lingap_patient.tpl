{{$sFormStart}}

<div style="width:660px; margin-top:10px" align="center">
	<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%;">
		<tr>
			<td class="segPanelHeader">Registration information</td>
		</tr>
		<tr>
			<td class="segPanel" align="left" valign="top">
				<table width="100%" border="0" cellpadding="0" cellspacing="2" style="font:normal 12px Arial; padding:4px" >
					<tr>
						<td width="1" valign="top">
							<table style="font:normal 12px Arial;" cellpadding="0" cellspacing="0">
								<tr>
									<td><label>PID</label></td>
									<td>{{$sPatientID}}</td>
								</tr>
								<tr>
									<td>
										<label style="white-space:nowrap">Fullname</label>
									</td>
									<td nowrap="nowrap">
										{{$sPatientEncNr}}
										{{$sPatientName}}
										{{$sSelectEnc}}
										{{$sClearEnc}}
									</td>
								</tr>
							</table>
						</td>
						<td width="10"></td>
						<td width="*">
							<div style="white-space: nowrap">
								<label>Address</label>
							</div>
							<div style="white-space: nowrap">
								{{$sAddress}}
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>

	<div id="rqsearch" style="width:720px" align="center">
		<div style="margin:1px;">
			<div class="dashlet" style="margin-top:20px;">
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader" style="font:bold 11px Tahoma">
					<tr>
						<td width="30%" valign="top"><h1 style="white-space:nowrap">Request from Social Service</h1></td>
						<td align="right" width="*">
							<!-- <input type="button" class="segButton" value="Add request" onclick="newRequest()" /> -->
						</td>
					</tr>
				</table>
			</div>
			<div>
{{$lstRequest}}
			</div>
		</div>
	</div>




{{$sHiddenInputs}}
{{$jsCalendarSetup}}

{{$sFormEnd}}
{{$sTailScripts}}