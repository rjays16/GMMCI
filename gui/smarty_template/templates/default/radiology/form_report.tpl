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
								<!-- <tr>
									<td align="right" width="140"><b>Per Transaction No.</b></td>
									<td width="80%">{{$sViewGroup}}</td>
								</tr> -->
								<!-- <tr>
									<td align="right" width="140"><b>Select Report</b></td>
									<td width="80%">{{$sReportSelect}}</td>
								</tr> -->
								<tr>
									<td align="right" width="140"><b>Select Patient Type</b></td>
									<td width="80%">{{$sPatientSelect}}</td>
								</tr>
								<tr>
									<td align="right" width="140"><b>Radiology Section</b></td>
									<td width="80%">{{$sReportSelectGroup}}</td>
								</tr>
								<tr>
									<td align="right" width="140"><b>Rad. Resident Doctor</b></td>
									<td width="80%">{{$sReportRadDoctor}}</td>
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
								<!--
								<tr id="shiftrow">
									<td align="right" width="140"><b>Shift Schedule</b></td>
									<td colspan="80%">{{$sShift}}</td>
								</tr>
								-->
								<!-- <tr>
									<td align="right" width="140"><b>Classification</b></td>
									<td width="80%">{{$sReportSelectClassification}}</td>
								</tr> -->
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
							<tr>
								<td align="right" width="140"><b>Select Report</b></td>
								<td width="80%">{{$sReportSelectStat}}</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>From</b></td>
								<td  width="80%">{{$sFromDateHidden2}}{{$sFromDateInput2}}{{$sFromDateIcon2}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>To</b></td>
								<td  width="80%">{{$sToDateHidden2}}{{$sToDateInput2}}{{$sToDateIcon2}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr>
							<td align="right" width="140"><b>Export as</b></td>
								<!--<td width="80%"><input type="radio" name="exp_type" id="exp_pdf">PDF <input type="radio" name="exp_type" id="exp_excel">EXCEL</td>-->
								<td width="80%">{{$sExportAsPdf}}PDF{{$sExportAsExcel}}EXCEL</td>
							</tr>
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sStatButton}}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr id="mode_class" style="display:none">
				<td colspan="2">
					<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
						<tbody class="submenu">
							<tr>
								<td align="right" width="140"><b>Select Classification</b></td>
								<td width="80%">{{$sCases}}</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>From</b></td>
								<td  width="80%">{{$sFromDateHidden3}}{{$sFromDateInput3}}{{$sFromDateIcon3}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>To</b></td>
								<td  width="80%">{{$sToDateHidden3}}{{$sToDateInput3}}{{$sToDateIcon3}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sContinueButton2}}</td>
						</tr>
					</table>
				</td>
			</tr>
            <tr id="mode_logbook" style="display:none">
                <td colspan="2">
                        <table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
                            <tbody class="submenu">
                                <tr>
                                    <td align="right" width="140"><b>Select Status</b></td>
                                    <td width="80%">{{$sReportStatus}}</td>
                                </tr>
                                <tr>
                                    <td align="right" width="140"><b>Select Type</b></td>
                                    <td width="80%">{{$sReportSelect3}}</td>
                                </tr>
                                <tr>
                                    <td align="right" width="140"><b>Select Patient Type</b></td>
                                    <td width="80%">{{$sPatientSelect3}}</td>
                                </tr>
                                <tr>
                                    <td align="right" width="140"><b>Radiology Section</b></td>
                                    <td width="80%">{{$sReportSelectGroup3}}</td>
                                </tr>
                                <tr>
                                    <td align="right" width="140"><b>Rad. Tech on Duty</b></td>
                                    <td width="80%">{{$sReportRadTech2}}</td>
                                </tr>
                                <tr>
                                    <td align="right" width="140"><b>Rad. Resident Doctor</b></td>
                                    <td width="80%">{{$sReportRadDoctor2}}</td>
                                </tr>
                                <tr>
                                    <td align="right" width="140"><b>From</b></td>
                                    <td  width="80%">{{$sFromDateHidden5}}{{$sFromDateInput5}}{{$sFromDateIcon5}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
                                </tr>
                                <tr>
                                    <td align="right" width="140"><b>To</b></td>
                                    <td  width="80%">{{$sToDateHidden5}}{{$sToDateInput5}}{{$sToDateIcon5}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
                                </tr>
                                <tr id="orderby">
                                    <td align="right" width="140"><b>Alphabetical</b></td>
                                    <td colspan="80%">{{$sOrderBy2}}</td>
                                </tr>
                        </tbody>
                    </table>
                    <br>
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sLogbookButton}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
			<tr id="mode_results" style="display:none">
				<td colspan="2">
					<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
						<tbody class="submenu">
							<!--
							<tr>
								<td align="right" width="140"><b>Patient's Name</b></td>
								<td width="80%">{{$sOrderEncID}}{{$sOrderName}}&nbsp;{{$sSelectPatient}}{{$sClearEnc}}</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>Address</b></td>
								<td width="80%">{{$sOrderAddress}}</td>
							</tr>
							-->
							<tr>
								<td align="right" width="140"><b>Radiology Section</b></td>
								<td width="80%">{{$sReportSelectGroup2}}</td>
							</tr>
							<tr>
									<td align="right" width="140"><b>Select Patient Type</b></td>
									<td width="80%">{{$sPatientSelect2}}</td>
								</tr>
							<tr>
								<td align="right" width="140"><b>Rad. Tech on Duty</b></td>
								<td width="80%">{{$sReportRadTech}}</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>Date</b></td>
								<td  width="80%">{{$sFromDateInput4}}{{$sFromDateIcon4}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr id="shiftrow">
								<td align="right" width="140"><b>Shift Schedule</b></td>
								<td colspan="80%">{{$sShift}}</td>
							</tr>
							<tr>
							<!-- Added by Cherry 11-12-10 -->
							<tr id="filter_impression">
								<td align="right" width="140"><b>Filter by Impression</b></td>
								<td colspan="80%">{{$sFilterImp}}</td>
							</tr>
							<tr id="show_impression">
								<td align="right" width="140"><b>Impression</b></td>
								<td colspan="80%">{{$sImpression}}</td>
							</tr>
							<!-- End Cherry -->
							</tr>
							<tr id="orderby">
								<td align="right" width="140"><b>Alphabetical</b></td>
								<td colspan="80%">{{$sOrderBy}}</td>
							</tr>
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sResultsButton}}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr id="mode_monthlyreport" style="display:none">
				<td colspan="2">
					<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
						<tbody class="submenu">
							<!--
							<tr>
								<td align="right" width="140"><b>Patient's Name</b></td>
								<td width="80%">{{$sOrderEncID}}{{$sOrderName}}&nbsp;{{$sSelectPatient}}{{$sClearEnc}}</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>Address</b></td>
								<td width="80%">{{$sOrderAddress}}</td>
							</tr>
							-->
							<tr>
								<td align="right" width="140"><b>Select Department</b></td>
								<td width="80%">{{$sReportSelectedDepartment}}</td>
							</tr>
							<!-- <tr>
									<td align="right" width="140"><b>Select Rad. Tech</b></td>
									<td width="80%">{{$sReportRadTech3}}</td>
								</tr> -->
							<tr>
                                    <td align="right" width="140"><b>Rad. Resident Doctor</b></td>
                                    <td width="80%">{{$sReportRadDoctor3}}</td>
                                </tr>
							<tr>
                                    <td align="right" width="140"><b>From</b></td>
                                    <td  width="80%">{{$sFromDateHidden6}}{{$sFromDateInput6}}{{$sFromDateIcon6}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
                                </tr>
                                <tr>
                                    <td align="right" width="140"><b>To</b></td>
                                    <td  width="80%">{{$sToDateHidden6}}{{$sToDateInput6}}{{$sToDateIcon6}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
                                </tr>
						</tbody>
					</table>
					<br>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sMonthlyreportButton}}</td>
						</tr>
					</table>
				</td>
			</tr>
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