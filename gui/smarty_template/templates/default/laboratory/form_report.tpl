{{* form.tpl  Form template for products module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}

{{$sFormStart}}
	<div style="padding:10px;width:95%;border:0px solid black">
	{{* NOTE:::  The following table  block must be inside the $sFormStart and $sFormEnd tags !!! *}}

	<!-- <font class="prompt">{{$sDeleteOK}}{{$sSaveFeedBack}}</font> -->
	<font class="warnprompt">{{$sMascotImg}} {{$sDeleteFailed}} {{$LDOrderNrExists}} <br> {{$sNoSave}}</font>
	<table border="0" cellspacing="1" cellpadding="3" style="" width="100%">
		<tbody class="submenu">
			<tr>
				<td align="right" width="140"><b>Select Report Mode</b></td>
				<td width="80%">{{$sReportSelectType}}</td>
			</tr>
			<tr id="mode_status" style="display:none">
				<td colspan="2">
						<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
							<tbody class="submenu">
								<tr>
									<td align="right" width="140"><b>Per Transaction No.</b></td>
									<td width="80%">{{$sViewGroup}}</td>
								</tr>
								<tr>
									<td align="right" width="140"><b>Select Report</b></td>
									<td width="80%">{{$sReportSelect}}</td>
								</tr>
								<tr>
									<td align="right" width="140"><b>Select Patient Type</b></td>
									<td width="80%">{{$sPatientSelect}}</td>
								</tr>
								<tr>
									<td align="right" width="140"><b>Laboratory Section</b></td>
									<td width="80%">{{$sReportSelectGroup}}</td>
								</tr>
								<tr>
									<td align="right" width="140"><b>From</b></td>
									<td  width="80%">{{$sFromDateHidden}}{{$sFromDateInput}}{{$sFromDateIcon}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
								</tr>
								<tr>
									<td align="right" width="140"><b>To</b></td>
									<td  width="80%">{{$sToDateHidden}}{{$sToDateInput}}{{$sToDateIcon}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
								</tr>
								<!--<tr id="shiftrow" style="display:none">-->
								<tr id="shiftrow">
									<td align="right" width="140"><b>Shift Schedule</b></td>
									<td colspan="80%">{{$sShift}}</td>
								</tr>
								<tr>
									<td align="right" width="140"><b>Classification</b></td>
									<td width="80%">{{$sReportSelectClassification}}</td>
								</tr>
								<!--
								<tr>
									<td align="right" width="140"><b>Sorted By</b></td>
									<td width="80%">{{$sReportOrder}}</td>
								</tr>
								<tr>
									<td align=right width=140>{{$LDReset}}</td>
									<td align=right>{{$sUpdateButton}}</td>
								</tr>
								-->
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sContinueButton}}&nbsp;&nbsp;{{$sReportButton}}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr id="mode_stat" style="display:none">
				<td colspan="2">
					<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
						<tbody class="submenu">
							<!--
							<tr>
								<td align="right" width="140"><b>Select Report</b></td>
								<td width="80%">{{$sReportSelect2}}</td>
							</tr>

							<tr>
								<td align="right" width="140"><b>Laboratory Section</b></td>
								<td width="80%">{{$sReportSelectGroup2}}</td>
							</tr>
							 -->
							<!--Added by Cherry 04-21-09-->
							<tr id ="serv_grp">
								<td align="right" width="140"><b>Laboratory Section</b></td>
								<td width="80%">{{$sReportSelectGroup3}}</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>Laboratory Services</b></td>
								<td width="80%">{{$sReportSelectServices3}}</td>
							</tr>
							<tr id="pat_type">
									<td align="right" width="140"><b>Select Patient Type</b></td>
									<td width="80%">{{$sPatientSelect3}}</td>
								</tr>

							<tr>
								<td align="right" width="140"><b>From</b></td>
								<td  width="80%">{{$sFromDateHidden2}}{{$sFromDateInput2}}{{$sFromDateIcon2}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>To</b></td>
								<td  width="80%">{{$sToDateHidden2}}{{$sToDateInput2}}{{$sToDateIcon2}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sStatButton}}{{$sIncomeButton}}{{$sBloodBankButton}}{{$sLabCensusPdf}}{{$sLabCensusExl}}</td>
						</tr>
					</table>
				</td>
			</tr>
				<tr id="mode_forwarding" style="display:none">
				<td colspan="2">
					<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
						<tbody class="submenu">
							<tr id = "pat_type">
								<td align="right" width="140"><b>Patient Type</b></td>
								<td  width="80%">{{$sForwadingPType}}</td>
							</tr>
							<tr id = "ward_name" style="display:none">
								<td align="right" width="140"><b>Ward/Station</b></td>
								<td  width="80%">{{$sWard}}</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>From</b></td>
								<td  width="80%">{{$sFromDateHidden3}}{{$sFromDateInput3}}{{$sFromDateIcon3}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>To</b></td>
								<td  width="80%">{{$sToDateHidden3}}{{$sToDateInput3}}{{$sToDateIcon3}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr id="shiftrow">
								<td align="right" width="140"><b>Time</b></td>
								<td colspan="80%">{{$sShift2}}</td>
							</tr>
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sForwardButton}}</td>
						</tr>
					</table>
				</td>
			</tr>
			<!--Added by Cherry 04-29-09
			<tr id="mode_crossmatching" style="display:none">
				<td colspan="2">
					<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
						<tbody class="submenu">
							<tr>
								<td align="right" width="140"><b>From</b></td>
								<td  width="80%">{{$sFromDateHidden3}}{{$sFromDateInput3}}{{$sFromDateIcon3}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>To</b></td>
								<td  width="80%">{{$sToDateHidden3}}{{$sToDateInput3}}{{$sToDateIcon3}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr id="shiftrow">
								<td align="right" width="140"><b>Time</b></td>
								<td colspan="80%">{{$sShift2}}</td>
							</tr>
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sForwardButton}}</td>
						</tr>
					</table>
				</td>
			</tr>
			 -->
			<tr id="mode_results" style="display:none">
				<td colspan="2">
					<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
						<tbody class="submenu">
							<tr>
								<td align="right" width="140"><b>Laboratory Section</b></td>
								<td width="80%">{{$sReportSelectGroup2}}</td>
							</tr>
							<tr id = "pat_type">
									<td align="right" width="140"><b>Select Patient Type</b></td>
									<td width="80%">{{$sPatientSelect2}}</td>
								</tr>
							<tr>
								<td align="right" width="140"><b>Date</b></td>
								<td  width="80%">{{$sFromDateInput4}}{{$sFromDateIcon4}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr id="shiftrow">
									<td align="right" width="140"><b>Shift Schedule</b></td>
									<td colspan="80%">{{$sShift3}}</td>
								</tr>
							<tr>
									<td align="right" width="140"><b>Classification</b></td>
									<td width="80%">{{$sReportSelectClassification2}}</td>
								</tr>
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sResultsButton}} </td>

						</tr>
					</table>
				</td>
			</tr>
						<tr id="rpt_charges" style="display:none">
								<td colspan="2">
										<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
														<td width="100%" align="center">{{$sGenerate3Button}}</td>
												</tr>
										</table>
								</td>
						</tr>
			<!--added by daryl -->
							<tr id="rpt_NewBorn" style="display:none">
								<td colspan="2">
									<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
										<tbody class="submenu">
											<tr>
													<td align="right" width="140"><b>Select Quarter</b></td>
													<td width="80%">{{$sNewBornQuarter}}</td>
											</tr>
											<tr>
													<td align="right" width="140"><b>Select Year</b></td>
													<td width="80%">{{$sNewBornYear}}</td>
											</tr>
										</tbody>
									</table>
									<br>
									<table border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td width="100%" align="center">{{$sNewBornButton}}</td>
										</tr>
									</table>
								</td>
						</tr>
			<!--ended by daryl -->
		</tbody>
	</table>

	{{$sHiddenInputs}}

{{$jsCalendarSetup}}
{{$sTransactionDetailsControls}}
<br/>
<!--
<div style="float:left;">
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sContinueButton}}</td>
		<td width="80%">&nbsp;&nbsp;{{$sStatButton}}</td>
	</tr>
</table>
</div>
-->

</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}