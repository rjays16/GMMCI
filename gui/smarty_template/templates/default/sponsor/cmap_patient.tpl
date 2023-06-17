{{* grant.tpl  Form template for Grants module *}}
<div style="width:750px; margin-top:10px" align="center">
	<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%;margin:4px">
		<tr>
			<td class="segPanelHeader">CMAP entry details</td>
		</tr>
		<tr>
			<td class="segPanel" align="left" valign="top">
				<table width="98%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Arial; padding:4px" >
					<tr>
						<td width="1" align="right" valign="middle"><strong>HRN</strong></td>
						<td width="1" valign="middle">
							{{$sPatientID}}
						</td>
						<td></td>
						<td></td>
						<td valign="middle"`>
							<strong>Patient type:</strong><br/>
							{{$sPatientEncType}}
							<span id="encounter_type_show" style="font-weight:bold; color:#000080">{{$sOrderEncTypeShow}}</span>
						</td>
						<td valign="middle">
							<div style="">
								<strong>Classification:</strong><br/>
								<span id="sw-class" style="font:bold 12px Arial; color:#006633">{{$sSWClass}}</span>
							</div>
						</td>
					</tr>
					<tr>
						<td width="1" align="right" valign="top"><strong>Patient name</strong></td>
						<td width="1" valign="middle">
							{{$sPatientEncNr}}
							{{$sPatientName}}
						</td>
						<td width="1" valign="middle" style="white-space:nowrap">
							{{$sSelectEnc}}{{$sClearEnc}}
						</td>
						<td align="right" colspan="2" valign="top" nowrap="nowrap">
							<strong>Current balance</strong>
							{{$sRunningBalance}}
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<button id="reg-walkin" class="segButton" disabled="disabled" onclick="openRegisterWalkin();return false;" tooltip="Register new walk-in"><img src="../../gui/img/common/default/page_edit.png" />Register</button>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>

<div id="rqsearch" style="width:780px" align="center">
	<div style="margin:1px;">
		<div class="dashlet" style="margin-top:20px;">
			<table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader" style="font:bold 11px Tahoma">
				<tr>
					<td width="30%" valign="top"><h1 style="white-space:nowrap">Active referrals</h1></td>
					<td align="right" width="*">
							<button id="add-referral" class="segButton" disabled="disabled" onclick="openTransfer(); return false;" tooltip="Create a new CMAP Referral entry "><img src="../../gui/img/common/default/user_go.png" />Referral</button>
						<!--<button id="ext-request" class="segButton" disabled="disabled" onclick="newExternalRequest(); return false;" tooltip="Grant an external request for CMAP"><img src="../../gui/img/common/default/door_out.png" />External request</button>-->
						<button id="add-request" class="segButton" disabled="disabled" onclick="newRequest(); return false;" tooltip="Grant a CMAP request"><img src="../../gui/img/common/default/cart_add.png" />Add request</button>
						<button id="print-request" class="segButton" disabled="disabled" onclick="printRequest(); return false;" tooltip="Print the CMAP request form"><img src="../../gui/img/common/default/page_white_acrobat.png" />Print request</button>
					</td>
				</tr>
			</table>
		</div>
		<div>
{{$lstFunds}}
		</div>
	</div>
</div>

{{$sHiddenInputs}}
{{$jsCalendarSetup}}


{{$sTailScripts}}