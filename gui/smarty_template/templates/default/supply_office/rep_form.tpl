{{* form.tpl  Form template for reports module (inventory) 2004-07-04 Elpidio Latorilla *}}

{{$sFormStart}}
		<div style="width:500px">
		{{* NOTE:::  The following table  block must be inside the $sFormStart and $sFormEnd tags !!! *}}

		<!-- <font class="prompt">{{$sDeleteOK}}{{$sSaveFeedBack}}</font> -->
		<table width="100%" border="0" style="font-size:12px; margin-top:5px" cellspacing="1" cellpadding="2">
				<tbody>
						<tr>
								<td align="left" class="segPanelHeader" colspan="2" width="300"><strong>Report options</strong></td>
						</tr>
						<tr>
								<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel"><b>Select Report</b></td>
								<td width="70%" nowrap="nowrap" class="segPanel">{{$sReportSelect}}</td>
						</tr>
						<tr id="expdate_row" style="display:none">
							<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel" valign="middle"><strong>Select item:</strong></td>
							<td width="70%" nowrap="nowrap" class="segPanel" valign="baseline">{{$sSCItemHidden}}{{$sSCItemInput}}<span style="vertical-align:top;">{{$sSCItemIcon}}</span></td>
			            </tr>
						<tr id="area_row" style="display:none">
								<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel"><b>Select Area</b></td>
								<td width="70%" nowrap="nowrap" class="segPanel">{{$sReportSelectArea}}</td>
						</tr>
						<tr id="percent_row" style="display:none">
								<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel"><b>Percentage</b></td>
								<td width="70%" nowrap="nowrap" class="segPanel">{{$sFromPercent}}%-{{$sToPercent}}%</td>
						</tr>
						<tr id="itemtype_row" style="display:none">
								<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel"><b>Item Type</b></td>
								<td width="70%" nowrap="nowrap" class="segPanel">{{$sReportSelectItemType}}</td>
						</tr>
						<tr id="adjusttype_row" style="display:none">
								<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel"><b>Adjustment Reason</b></td>
								<td width="70%" nowrap="nowrap" class="segPanel">{{$sAdjustSelectItemType}}</td>
						</tr>
						<!-- -->
						<tr id="serialno_row" style="display:none">
								<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel"><b>Serial Number</b></td>
								<td width="70%" nowrap="nowrap" class="segPanel">{{$sSerialNumber}}</td>
						</tr>
						<tr id="fromdate_row" style="display:none">
								<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel"><b>From</b></td>
								<td width="70%" nowrap="nowrap" class="segPanel">{{$sFromDateHidden}}{{$sFromDateInput}}{{$sFromDateIcon}}</td>
						</tr>
						<tr id="todate_row" style="display:none">
								<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel"><b>To</b></td>
								<td width="70%" nowrap="nowrap" class="segPanel">{{$sToDateHidden}}{{$sToDateInput}}{{$sToDateIcon}}</td>
						</tr>
						<tr id="date_row" style="display:none">
								<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel"><b>Date</b></td>
								<td width="70%" nowrap="nowrap" class="segPanel">{{$sDateHidden}}{{$sDateInput}}{{$sDateIcon}}</td>
								<div style="float: right; margin-left: 1em; margin-bottom: 1em;"
									id="calendar-container"></div>
						</tr>
						<tr id="expdate_row" style="display:none">
								<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel"><b>Expiry Date</b></td>
								<td width="70%" nowrap="nowrap" class="segPanel">{{$sExpiryDateHidden}}{{$sExpDateInput}}{{$sExpDateIcon}}</td>
						</tr>
						<!-- added by bryan 10-21-09 -->
						<tr id="expchk_row" style="display:none">
								<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel"><b>Include No Expiries</b></td>
								<td width="70%" nowrap="nowrap" class="segPanel">{{$sExpiriesCheckBox}}</td>
						</tr>
						<tr id="item_row" style="display:none">
								<td align="right" width="30%" nowrap="nowrap" align="right" class="segPanel"><b>Items Below</b></td>
								<td width="70%" nowrap="nowrap" class="segPanel">{{$sItemsBelow}}</td>
						</tr>
						<!-- Added by Cherry 01-21-11 -->
						<tr id="month_row" style="display:none">
							<td align="right" width="30%" nowrap="nowrap" class="segPanel"><b>Select Month</b></td>
							<td width="70%" nowrap="nowrap" class="segPanel">{{$sMonth}}</td>
						</tr>
						<tr id="year_row" style="display:none">
							<td align="right" width="30%" nowrap="nowrap" class="segPanel"><b>Select Year</b></td>
							<td width="70%" nowrap="nowrap" class="segPanel">{{$sYear}}</td>
						</tr>
						<!-- -->
						<tr>
								<td colspan="2" align="center" class="segPanel">
										{{$sGenerateButton}}
								</td>
						</tr>
				</tbody>
		</table>

		{{$sHiddenInputs}}

{{$jsCalendarSetup}}
{{$sTransactionDetailsControls}}
<br/>
<div style="float:left;">
<table border="0" cellpadding="0" cellspacing="0">
		<tr>
				<td width="1%">{{$sContinueButton}}</td>
		</tr>
</table>
</div>


</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}
