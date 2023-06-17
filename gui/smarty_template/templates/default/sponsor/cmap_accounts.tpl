{{$sFormStart}}
	<div style="width:680px; margin-top:10px" align="center">
		<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%;margin:4px">
			<tr>
				<td class="segPanelHeader">Account information</td>
			</tr>
			<tr>
				<td class="segPanel" align="left" valign="top">
					<table width="98%" border="0" cellpadding="0" cellspacing="2" style="font:normal 12px Arial; padding:4px" >
						<tr>
							<td width="1" align="right" valign="middle" style="white-space:nowrap">
								<label>Select account:</label>
							</td>
							<td width="1" valign="middle">
								{{$sSelectAccount}}
							</td>
							<td width="50"></td>
							<td width="1" align="left" width="1" valign="middle" style="white-space:nowrap">
								<label>Actual balance</label>
							</td>
							<td width="1">
								{{$sActualBalance}}
							</td>
							<td width="*" valign="bottom" align="left">
								<!--{{$sAdjustBalance}} -->
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<button class="segButton" onclick="openAccountMgr();return false"><img src="../../gui/img/common/default/group_add.png"/>Accounts...</button>
							</td>
							<td width="50"></td>
							<td width="1" align="left" width="1" valign="middle" style="white-space:nowrap">
								<label>Balance after referrals</label>
							</td>
							<td width="1">
								{{$sReferredBalance}}
							</td>
							<td width="*" valign="bottom" align="left">
								<!--{{$sAdjustBalance}} -->
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>

	<div id="rqsearch" style="width:680px;" align="center">
		<div style="margin:1px;">
			<div class="dashlet" style="margin-top:20px;">
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader" style="font:bold 11px Tahoma">
					<tr>
						<td width="30%" valign="top"><h1 style="white-space:nowrap"></h1></td>
						<td align="right" width="*">
							<button class="segButton" onclick="editAllotment(); return false;"><img src="../../gui/img/common/default/key_add.png" />New allotment</button>
						</td>
					</tr>
				</table>
			</div>
			<div>
{{$lstAllotments}}
			</div>
		</div>
	</div>



{{$sHiddenInputs}}
{{$jsCalendarSetup}}

{{$sFormEnd}}
{{$sTailScripts}}