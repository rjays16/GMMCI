{{* form.tpl  Form template for products module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}

{{$sFormStart}}
	<div style="padding:10px;width:95%;border:0px solid black">
	{{* NOTE:::  The following table  block must be inside the $sFormStart and $sFormEnd tags !!! *}}

	<!-- <font class="prompt">{{$sDeleteOK}}{{$sSaveFeedBack}}</font> -->
	<font class="warnprompt">{{$sMascotImg}} {{$sDeleteFailed}} {{$LDOrderNrExists}} <br> {{$sNoSave}}</font>
	<table border="0" cellspacing="1" cellpadding="3" style="" width="100%">
		<tbody class="submenu">
				{{if $sShowCategory}}
				<tr>
					<td align="right" width="140"><b>Select category</b></td>
					<td width="80%">{{$sReportCategory}}</td>
				</tr>
				{{/if}}
				<tr>
					<td align="right" width="140"><b>Select report</b></td>
					<td width="80%">{{$sReportSelect}}</td>
				</tr>

				<!-- added by cha 07-20-09 -->
				<tr id="codetype" style="display:none">
					<td align="right" width="140"><b>Select Code type</b></td>
					<td width="80%"><b>{{$sICD10code}}ICD10 Codes {{$sICPcode}}ICP Codes</b></td>
				</tr>

				<tr id="icd_type" style="display:none">
					<td align="right" width="140"><b>Select ICD10 Code</b></td>
					<td class="yui-skin-sam">
							<div id="icd_autocomplete">
									<input type="text" id="icd_code" name="icd_code"/>
									<input type="hidden" id="icd_code_nr" name="icd_code_nr"/>
									<div id="icd_container"></div>
							</div>
					</td>
				</tr>

				<tr id="icp_type" style="display:none">
					<td align="right" width="140"><b>Select ICP Code</b></td>
					<td class="yui-skin-sam">
							<div id="icp_autocomplete">
									<input type="text" id="icp_code" name="icp_code"/>
									<input type="hidden" id="icp_code_nr" name="icp_code_nr"/>
									<div id="icp_container"></div>
							</div>
					</td>
				</tr>

				<tr id="patient_type" style="display:none">
					<td align="right" width="140"><b>Patient Type</b></td>
					<td width="80%">{{$sPatientType}}</td>
				</tr>

				<!-- added by VAN 09-12-08 -->
				<tr id="dept_row" style="display:none">
					<td align="right" width="140"><b>Select department</b></td>
					<td width="80%">{{$sReportSelectDept}}</td>
				</tr>

				<tr id="dept_row_sub" style="display:none">
					<td align="right" width="140"><b>Select department</b></td>
					<td width="80%">{{$sReportSelectDeptSub}}</td>
				</tr>

			<tr id="icd_class">
				<td align="right" width="140"><b>Select Diagnosis Classification</b></td>
				<td width="80%">{{$sICDClassification}}</td>
			</tr>

			<tr id="notifiable_format" style="display:none">
				<td align="right" width="140"><b>Select Printout Format</b></td>
				<td width="80%">{{$sNotifiableFormat}}</td>
			</tr>

			<!-- added by Cherry 11-25-09 -->
			<tr id="age_row" style="display:none">
				<td align="right" width="140"><b>Select Age Distribution</b></td>
				<td width="80%">{{$sReportSelectAge}}</td>
			</tr>

			<!-- added by Cherry 05-09-09 -->
			<tr id="code" style="display:none">
				<td align="right" width="140"><b>Select Code</b></td>
				<td width="80%">{{$sReportSelectCode}}</td>
			</tr>
			<!-- -->

			<tr id="mode_row" style="display:none">
				<td align="right" width="140"><b>Select mode of report</b></td>
				<td width="80%">{{$sReportSelectKey}}</td>
			</tr>

			<tr id="died_row" style="display:none">
				<td align="right" width="140"><b>Select Status</b></td>
				<td width="80%">{{$sReportSelectKey2}}</td>
			</tr>

			<tr id="phic_row" style="display:none">
				<td align="right" width="140"><b>Select Classification</b></td>
				<td width="80%">{{$sReportSelectKey3}}</td>
			</tr>

			<!--added by Cherry 09-10-10-->
			<tr id="medocs_encoder" style="display:none">
				<td align="right" width="140"><b>Select Encoder</b></td>
				<td width="80%">{{$sReportEncoder}}</td>
			</tr>
			<!-- end Cherry -->
			<tr id="insurance_type" style="display:none">
					<td align="right" width="140"><b>Insurance Type</b></td>
					<td width="80%"><b>{{$sInsurances}}</td>
			</tr>
			<tr id="is_senior" style="display:none">
					<td align="right" width="140"><b>Patient Type</b></td>
					<td width="80%"><b>{{$sSenior}}</td>
			</tr>
			<!-- added by Cherry 04-15-09 -->
			<tr id="loc_row" style="display:none">
				<td align="right" width="140"><b>Select location</b></td>
				<td width="80%">{{$sReportSelectLoc}}</td>
			</tr>

			<!-- -->
			<tr>
				<td align="right" width="140"><b>From</b></td>
				<td>{{$sFromDateHidden}}{{$sFromDateInput}}{{$sFromDateIcon}}</td>
			</tr>

			<tr>
				<td align="right" width="140"><b>To</b></td>
				<td>{{$sToDateHidden}}{{$sToDateInput}}{{$sToDateIcon}}</td>
			</tr>

			<tr id="shiftrow" style="display:none">
				<td align="right" width="140"><b>Time</b></td>
				<td colspan="80%">{{$sShift}}</td>
			</tr>

			<tr id="orderby" style="display:none">
				<td align="right" width="140"><b>Alphabetical</b></td>
				<td colspan="80%">{{$sOrderBy}}</td>
			</tr>

			<!-- added by cha 07-20-09 -->
			<tr id="export_type" style="display:none">
				<td align="right" width="140"><b>Export as</b></td>
				<td width="80%">{{$sExportAsPdf}}PDF {{$sExportAsExcel}}EXCEL</td>
			</tr>
			<!-- end cha -->

			<tr>
				<td align=right width=140>{{$LDReset}}</td>
				<td align=right>{{$sUpdateButton}}</td>
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
