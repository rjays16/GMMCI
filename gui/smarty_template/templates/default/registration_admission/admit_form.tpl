{{* Template for admission input and data display *}}
{{* Files using this: *}}
{{* - /modules/registration_admission/aufnahme_start.php *}}
{{* - /modules/registration_admission/aufnahme_daten_zeigen.php *}}

	{{if $bSetAsForm}}
	<form method="post" action="{{$thisfile}}" name="aufnahmeform" id="aufnahmeform" onSubmit="return false;">
	{{/if}}

		<table border="0" cellspacing=1 cellpadding=0 width="100%">

		{{if $error}}
				<tr>
					<td colspan=4 class="warnprompt">
						<center>
						{{$sMascotImg}}
						{{$LDError}}
						</center>
					</td>
				</tr>
		{{/if}}

		{{if $is_discharged}}
				<tr>
					<td bgcolor="red" colspan="3">
						&nbsp;
						{{$sWarnIcon}}
						<font color="#ffffff">
						<b>
						{{$sDischarged}}
						</b>
						</font>
					</td>
				</tr>
		{{/if}}

				<tr>
					<td  class="adm_item">
						<p>{{$LDCaseNr}}
</p>
					<p>Bar Code 				    </p></td>
					<td class="adm_input">
						{{$encounter_nr}}
						<br>
						{{$sEncBarcode}} {{$sHiddenBarcode}}
					</td>
					<td {{$sRowSpan}} align="center" class="photo_id">
						{{$img_source}}
					</td>
				</tr>

				<tr id="rowDateConsult" style="display:none">
					<td  class="adm_item">
						{{$LDConsultDate}}:
					</td>
					<td  class="adm_item">
						{{$sConsultDate}}
						{{if $sAdmissionBol}}
							{{$jsCalendarSetup2}}
							{{$sDateMiniCalendar2}}
						{{/if}}
					</td>
				</tr>
				<tr id="rowTimeConsult" style="display:none">
					<td  class="adm_item">
						{{$LDConsultTime}}:
					</td>
					<td  class="adm_item">
						{{$sConsultTime}}
					</td>
				</tr>

				<tr id="rowDateAdmit">
					<td  class="adm_item" id="rowDate">
						{{$LDAdmitDate}}:
					</td>
					<!--commented by VAN 01-21-09 -->
					<!--
					<td class="adm_input">
						{{if $sAdmission}}
							{{$sAdmitDate2}}
							{{$sDateMiniCalendar2}}
							{{$jsCalendarSetup2}}
						{{else}}
							{{$sAdmitDate}}
						{{/if}}
					</td>
					-->
					<td class="adm_input">
						{{$sAdmitDate2}}
						{{$sDateMiniCalendar2}}
						{{$jsCalendarSetup2}}
					</td>
				</tr>

				<tr id="rowTimeAdmit">
					<td class="adm_item" id="rowTime">
					{{$LDAdmitTime}}:
					</td>
					<td class="adm_input">
						<!--edited by VAN 01-21-09 -->
						{{$sAdmitTime}}{{$sAdmitTime2}}
					</td>
				</tr>
				<!--commented by VAN 01-21-09 -->
				<!--
			{{if !$sAdmission}}

				<tr id="adm_date" style="display:none">
					<td  class="adm_item">
						{{$LDAdmitDate2}}:
					</td>
					<td class="adm_input">
						{{$sAdmitDate2}}
						{{$sDateMiniCalendar2}}
						{{$jsCalendarSetup2}}
					</td>
				</tr>

				<tr id="adm_time" style="display:none">
					<td class="adm_item">
					{{$LDAdmitTime2}}:
					</td>
					<td class="adm_input">
						{{$sAdmitTime2}}
					</td>
				</tr>

			{{/if}}
				-->
				<tr>
					<td class="adm_item">
						{{$LDTitle}}:
					</td>
					<td class="adm_input">
						{{$title}}
					</td>
				</tr>

				<tr>
					<td class="adm_item">
						{{$LDLastName}}:
					</td>
					<td bgcolor="#ffffee" class="vi_data"><b>
						{{$name_last}}</b>
					</td>
				</tr>

				<tr>
					<td class="adm_item">
						{{$LDFirstName}}:
					</td>
					<td bgcolor="#ffffee" class="vi_data">
						{{$name_first}} &nbsp; {{$sCrossImg}}
					</td>
				</tr>

			{{if $name_2}}
				<tr>
					<td class="adm_item">
						{{$LDName2}}:
					</td>
					<td bgcolor="#ffffee">
						{{$name_2}}
					</td>
				</tr>
			{{/if}}

			{{if $name_3}}
				<tr>
					<td class="adm_item">
						{{$LDName3}}:
					</td>
					<td bgcolor="#ffffee">
						{{$name_3}}
					</td>
				</tr>
			{{/if}}

			{{if $name_middle}}
				<tr>
					<td class="adm_item">
						{{$LDNameMid}}:
					</td>
					<td bgcolor="#ffffee">
						{{$name_middle}}
					</td>
				</tr>
			{{/if}}

				<tr>
					<td class="adm_item">
						{{$LDBday}}:
					</td>
					<td bgcolor="#ffffee" class="vi_data" width="39%">
						{{$sBdayDate}} &nbsp; {{$sCrossImg}} &nbsp;<font color="black">{{$sDeathDate}}</font>
					</td>
					<td bgcolor="#ffffee">
						&nbsp;&nbsp;{{$LDSex}}: {{$sSexType}}
					</td>
				</tr>

			{{if $LDBloodGroup}}
				<tr>
					<td class="adm_item">
						{{$LDBloodGroup}}:
					</td>
					<td class="adm_input" colspan=2>
						{{$blood_group}}
					</td>
				</tr>
			{{/if}}

				<tr>
						<td class="adm_item">
								{{$LDVitalSigns}}
						</td>
						<td class="adm_input" colspan=2>
								{{$vital_signs}}
						</td>
				</tr>

				<!-- Added Genesis 05/20/14!-->
				<tr>
					<td class="adm_item">
						{{$LDContactNo}} :
					</td>
					<td colspan=2 class="adm_input">
						{{$sContactNo}}
					</td>
				</tr>
				<!--end added Genesis 05/20/14 !-->

				<tr>
					<td class="adm_item">
						{{$LDAddress}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$segAddress}}
<!--
						{{$addr_str}}  {{$addr_str_nr}}
						<br>
						{{$addr_zip}} {{$addr_citytown_name}}
-->
					</td>
				</tr>

				<!--added by CHA, May 21, 2010-->
				<!--<tr id="mother_nr_row" style="display:none">
					 <td class="adm_item">
						{{$LDMotherNr}}
					</td>
					<td colspan=2 class="adm_input">
						{{$sMotherCaseNr}}
						{{$sMotherWardNr}}
						{{$sMotherRoomNr}}
						{{$sMotherDeptNr}}
						{{$sMotherSelect}}
					</td>
				</tr> -->
				<!--end CHA, May 21, 2010-->

				<tr>
					<td class="adm_item">
						<font color="red">{{$LDAdmitClass}}</font>:
					</td>
					<td colspan=2 class="adm_input">
						{{$sAdmitClassInput}}
						{{$sAdmitClassInput2}}
						{{$sAdmitClassInput3}}
					</td>
				</tr>
			{{if $segORNumber}}
				<tr>
					<td class="adm_item">
						<font color="red">{{$segORNumber}}</font>:
					</td>
					<td colspan=2 class="adm_input">
						{{$sORNumber}}
						{{$sORTEMP}}
						{{$sOrDialog}}

					</td>
				</tr>
			{{/if}}
			<!---added 03-07-07---->
			{{if $LDInformant}}
				<tr>
					<td class="adm_item">
						{{$LDInformant}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$informant_name}}
					</td>
				</tr>
			{{/if}}
			{{if $LDInfoAdd}}
				<tr>
					<td class="adm_item">
						{{$LDInfoAdd}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$info_address}}
					</td>
				</tr>
			{{/if}}
			{{if $LDInfoRelation}}
				<tr>
					<td class="adm_item">
						{{$LDInfoRelation}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$relation_informant}}
					</td>
				</tr>
			{{/if}}

			<!--added by VAN 06-13-08 -->
			{{if $segShowIfFromER && $LDTriageCategory}}
				<tr>
					<td class="adm_item">
						{{$LDTriageCategory}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$sCategory}}
					</td>

				</tr>
			{{/if}}
			<!-- -->

			<!-- -->
			<tr>
				<td class="adm_item">
					{{$LDConfidential}}:
				</td>
				<td colspan=2 class="adm_input">
					{{$sConfidential}}
				</td>

			</tr>

			<!-- -->

			<!--added by VAN 04-28-08 -->

			{{if $LDMedico && $segShowIfFromER}}
				<tr>
					<td class="adm_item">
						{{$LDMedico}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$Medico}}
					</td>

				</tr>
				<tr id="ERMedico">
					<td class="adm_item" width="30%">
						{{$LDMedicoCases}}
					</td>
					<td colspan=2 class="adm_input">
							<table width="63%" height="84" border="0" cellpadding="1" id="srcMedicoTable" style="width:100%; font-size:12px">
								<td width="36%" height="80" valign="middle" id="leftTdMedico">
									{{$rowMedicoA}}					</td>
								<td width="64%" valign="middle" id="rightTdMedico">
									{{$rowMedicoB}}
																		{{$sdescription}}
																</td>
								</table>

					</td>

				</tr>

				<!--added by VAN 06-12-08 -->
				<tr id="ERMedicoPOI">
					<td class="adm_item">
						{{$LDPOI}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$sPOI}}
					</td>
				</tr>
				<tr id="ERMedicoTOI">
					<td class="adm_item">
						{{$LDTOI}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$sTOI}}
					</td>
				</tr>
				<tr id="ERMedicoDOI">
					<td class="adm_item">
						{{$LDDOI}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$sDOI}}
						{{$sDateMiniCalendar}}
						{{$jsCalendarSetup}}
					</td>
				</tr>
				{{/if}}

				{{if $LDDOA && $segShowIfFromER}}
					<tr>
					<td class="adm_item">
						{{$LDDOA}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$sDOA}}
						&nbsp;&nbsp;
						{{$sDOAreason}}
					</td>

				</tr>
				{{/if}}
			<!---------------->

			<!--added by VAN 08-20-08-->
			{{if $LDWard}}
				<!--<tr {{$segERDetailsHideable}}>-->
				<tr id="mode_assignment" class="ERDetails" style="display">	<!---edited by CHA, 04-29-2010---->
					<td class="adm_item">
						Mode in Room Assignment:
					</td>
					<td colspan=2 class="adm_input">
						{{$sLDRoomMode}}
					</td>
				</tr>
			{{/if}}
			<!-- -->

				<!---added 03-07-07---->
			{{if $LDWard}}
				<!--<tr {{$segERDetailsHideable}}>-->
				<tr id="accomodation_assignment" class="ERDetails" style="display">	<!---edited by CHA, 04-29-2010---->
					<td class="adm_item">
						<font color="red">{{$LDWard}}</font>:
					</td>
					<td colspan=2 class="adm_input">
						{{$sWardInput}}
					</td>
				</tr>
			{{/if}}

			<!------added by VAN 01-31-08 ----------------->

			{{if $LDWard}}
				<!--<tr {{$segERDetailsHideable}}>-->
				<tr id="room_assignment" class="ERDetails" style="display"> <!---edited by CHA, 04-29-2010---->
					<td class="adm_item">
						<font color="red">{{$LDRoom}}</font>:
					</td>
					<td colspan=2 class="adm_input">
						{{$sLDRoom}}
					</td>
				</tr>

				<!--<tr style="display:none" id="area_row" {{$segERDetailsHideable}}>-->
				<tr id="area_assignment" style="display">	<!---edited by CHA, 04-29-2010---->
					<td class="adm_item">
						<font color="red">{{$LDArea}}</font>:
					</td>
					<td colspan=2 class="adm_input">
						{{$sLDArea}}
					</td>
				</tr>
			{{/if}}

			<!-- added by VAN 08-20-08 -->
			{{if $LDWard}}
				<tr id="datefrom_row" style="display:none" {{$segERDetailsHideable}}>
					<td class="adm_item">
						Date and Time (From):
					</td>
					<td colspan=2 class="adm_input">
						{{$sLDDateFrom}}
						{{$sDateMiniCalendar3}}
						{{$jsCalendarSetup3}}
						&nbsp;&nbsp;
						{{$sLDTimeFrom}}
					</td>
				</tr>
			{{/if}}
			<!-- -->

			{{if $LDWard}}
				<!--<tr {{$segERDetailsHideable}}>-->
				<tr id="bed_assignment" class="ERDetails" style="display">	<!---edited by CHA, 04-29-2010---->
					<td class="adm_item">
						<font color="red">{{$LDBed}}</font>:
					</td>
					<td colspan=2 class="adm_input">
						{{$sLDBed}}
					</td>
				</tr>
			{{/if}}

			<!--------------------------------------------->

				<!----added 02-27-07 -->
				<tr {{$segERDetailsHideable}}>
					<td class="adm_item">
						{{$LDDoctor}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$doctor_name}}
						<!-- Added by Jarel 03102015 -->
						<table id="order-list-att" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
							<thead>
									<tr id="order-list-header-att">
											<th width="4%" nowrap></th>
											<th width="*" nowrap align="left">&nbsp;&nbsp;Doctor's Name</th>
											<th width="1"></th>
									</tr>
							</thead>
							<tbody>
								{{$sAttDrItems}}

						</table>
						<!-- End 03102015 -->
					</td>
					<td colspan=2 >
						{{$doctor_name2}}
					</td>
				</tr>

				<!----added 02-27-07 -->

			<!--{{if $LDDepartment}}-->
				<tr {{$segERDetailsHideable}}>
					<td class="adm_item">
						<font color="red">{{$LDDepartment}}</font>:
					</td>
					<td colspan=2 class="adm_input">
						{{$sDeptInput}}
					</td>
				</tr> 
			<!--{{/if}}-->


			<!-- burn added : May 16, 2006 -->
			{{if $segERDiagnosis}}
				<!--<tr class="ERDetails"> -->
								<tr id="diagnosis_assignment" style="display">	<!---edited by CHA, 04-29-2010---->
					<td class="adm_item">
						{{$segERDiagnosis}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$er_opd_diagnosis}}
					</td>
				</tr>
			{{/if}}

			{{if $segComplaint}}
				<tr id="complaint_assignment" style="display">
					<td class="adm_item">
						{{$segChiefComplaint}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$chief_complaint}}
					</td>
				</tr>
			{{/if}}

			{{if $segEROPDDr}}
				<tr class="ERDetails">
					<td class="adm_item">
						{{$segEROPDDr}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$sERDrInput}}
						<!-- Added by Jarel 03102015 -->
						<table id="order-list-adm" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
							<thead>
									<tr id="order-list-header-adm">
											<th width="4%" nowrap></th>
											<th width="*" nowrap align="left">&nbsp;&nbsp;Doctor's Name</th>
											<th width="1"></th>
									</tr>
							</thead>
							<tbody>
								{{$sConDrItems}}

						</table>
						<!-- End 03102015 -->
					</td>
				</tr>
			{{/if}}
			{{if $segEROPDDepartment}}
				<tr>
					<td class="adm_item">
						{{$segEROPDDepartment}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$sERDeptInput}}
					</td>
				</tr>
			{{/if}}
			{{if $LDDiagnosis && $segShowIfFromER}}
				<tr>
					<td class="adm_item">
						{{$LDDiagnosis}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$referrer_diagnosis}}
					</td>
				</tr>
			{{/if}}
			{{if $LDTherapy}}
				<tr>
					<td class="adm_item">
						{{$LDTherapy}}:
					</td>
					<td colspan=2 class="adm_input">
						{{ $referrer_recom_therapy}}
					</td>
				</tr>
			{{/if}}
			{{if $LDRecBy && $segShowIfFromER}}
				<tr>
					<td class="adm_item">
						{{$LDRecBy}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$referrer_dr_name}}
					</td>
					<!--<td colspan=2>
						{{$referrer_dept_name}}
					</td> -->
					<td colspan=2>
						{{$referrer_dr}}
					</td>
					<td colspan=2>
						{{$name1}}
					</td>
					<td colspan=2>
						{{$name2}}
					</td>
					<td colspan=2>
						{{$lname}}
					</td>
					<!--<td colspan=2>
						{{$referrer_dept}}
					</td>-->
				</tr>
			{{/if}}

			{{if $LDRecDept && $segShowIfFromER}}
				<tr>
					<td class="adm_item">
						{{$LDRecDept}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$referrer_dept_name}}
					</td>
					<td colspan=2>
						{{$referrer_dept}}
					</td>
				</tr>
			 {{/if}}

			 {{if $LDRecIns && $segShowIfFromER}}
				<tr>
					<td class="adm_item">
						{{$LDRecIns}}:
					</td>
					<td colspan=2 class="adm_input">
						{{ $referrer_institution}}
					</td>
				</tr>
			 {{/if}}
			 {{if $LDSpecials && $segShowIfFromER}}
				<tr>
					<td class="adm_item">
						{{$LDSpecials}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$referrer_notes}}
					</td>
				</tr>
			 {{/if}}
				<!-- The insurance class  -->
			 {{if $LDBillType}}
				<tr>
					<td class="adm_item">
						{{$LDBillType}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$sBillTypeInput}}&nbsp;&nbsp;<span name="iconIns" id="iconIns" style="display:none">{{$sBtnAddItem}}</span>
					</td>
					<!--<td>{{$sBtnAddItem}}</td>-->
				</tr>
			 {{/if}}
				<!-- edited 03-06-07------------->

			 {{if $LDInsuranceNr}}
				<tr>
					<td class="adm_item">
						{{$LDInsuranceNr}}:
					</td>
					<td colspan=2 class="adm_input">
						<!--{{$insurance_nr}}-->
						<!-- -->

						<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
							<thead>
									<tr id="order-list-header">
											<th width="4%" nowrap></th>
											<th width="*" nowrap align="left">&nbsp;&nbsp;Insurance Company</th>
											<th width="20%" nowrap align="right">&nbsp;&nbsp;Insurance No.</th>
											<th width="18%" nowrap align="right">&nbsp;&nbsp;Principal Holder</th>
											<th width="1"></th>
									</tr>
							</thead>
							<tbody>
								{{$sOrderItems}}

						</table>

						<!-- -->
					</td>

				</tr>
				{{/if}}

				<!--
				{{if $LDInsuranceCo}}
				<tr>
					<td class="adm_item">
						{{$LDInsuranceCo}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$insurance_firm_name}}
					</td>
				</tr>
				{{/if}}
				-->
				<!-- edited 03-06-07------------->
			{{if $LDCareServiceClass}}
				<tr>
					<td class="adm_item">
						{{$LDCareServiceClass}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$sCareServiceInput}} {{$LDFrom}} {{$sCSFromInput}} {{$LDTo}} {{$sCSToInput}} {{$sCSHidden}}
					</td>
				</tr>
			{{/if}}

			{{if $LDRoomServiceClass}}
				<tr>
					<td class="adm_item">
						{{$LDRoomServiceClass}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$sCareRoomInput}} {{$LDFrom}} {{$sRSFromInput}} {{$LDTo}} {{$sRSToInput}} {{$sRSHidden}}
					</td>
				</tr>
			{{/if}}

			{{if $LDAttDrServiceClass}}
				<tr>
					<td class="adm_item">
						{{$LDAttDrServiceClass}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$sCareDrInput}} {{$LDFrom}} {{$sDSFromInput}} {{$LDTo}} {{$sDSToInput}} {{$sDSHidden}}
					</td>
				</tr>
			{{/if}}

				<!-----added 03-08-07------------->
				{{if $LDCondition && $segShowIfFromER}}
				<tr class="ERDetails">
					<td class="adm_item">
						<font color="red">{{$LDCondition}}</font>:
					</td>
					<td colspan=2 class="adm_input">
						{{$sCondition}}
					</td>
				</tr>
				{{/if}}
				{{if $LDResults && $segShowIfFromER}}
				<tr class="ERDetails">
					<td class="adm_item">
						<font color="red">{{$LDResults}}</font>:
					</td>
					<td colspan=2 class="adm_input">
						<!--edited by VAN 02-27-08 -->
						{{if $segAdmissionShow}}
							{{$sResults}}
						{{else}}
							<table width="63%" height="84" border="0" cellpadding="1" id="srcResultTable" style="width:100%; font-size:12px">
								<td width="36%" height="80" valign="middle" id="leftTdResult">
									{{$rowResultA}}					</td>
								<td width="64%" valign="middle" id="rightTdResult">
									{{$rowResultB}}					</td>
								</table>
						{{/if}}
					</td>

				</tr>
				{{/if}}
				{{if $LDDisposition && $segShowIfFromER}}
				<tr class="ERDetails">
					<td class="adm_item">
						<font color="red">{{$LDDisposition}}</font>:
					</td>
					<td colspan=2 class="adm_input">
						<!--edited by VAN 02-27-08 -->
						{{if $segAdmissionShow}}
							{{$sDisposition}}
						{{else}}
						<table width="63%" height="84" border="0" cellpadding="1" id="srcResultTable" style="width:100%; font-size:12px">
							<td width="36%" height="80" valign="middle" id="leftTdResult">
								{{$rowDispositionA}}					</td>
							<td width="64%" valign="middle" id="rightTdResult">
								{{$rowDispositionB}}					</td>
							</table>
						{{/if}}
					</td>
				</tr>
				{{/if}}

                <!-- added by VAN 10-12-2011 -->
                {{if $LDSmokers}} 
                <tr>
                    <td class="adm_item">
                        {{$LDSmokers}}:
                    </td>
                    <td colspan=2 class="adm_input">
                        {{$sSmokersInput}}
                    </td>
                </tr>
                {{/if}}
                {{if $LDDrinker}}
                <tr>
                    <td class="adm_item">
                        {{$LDDrinker}}:
                    </td>
                    <td colspan=2 class="adm_input">
                        {{$sDrinkerInput}}
                    </td>
                </tr>
                {{/if}}
                <!-- -->
				<tr>
					<td class="adm_item">
						{{$LDAdmitBy}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$encoder}}
					</td>
				</tr>
				<tr>
					<td class="adm_item">
						{{$LDDeptBelong}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$sDeptBelong}}
					</td>
				</tr>

				<!-------------------------------->

				{{$sHiddenInputs}}

				<tr>
					<td colspan="3">&nbsp;

					</td>
				</tr>
				<tr>
					<td>
						{{$pbSave}}
					</td>
					<td align="right">
						{{$pbRefresh}} {{$pbRegData}}
					</td>
					<td align="right">
						{{$pbCancel}}					</td>
				</tr>

		</table>

			{{$sErrorHidInputs}}
			{{$sUpdateHidInputs}}
			 {{$isWellBaby}} <!--added by CHA, April 29,2010 -->
	{{if $bSetAsForm}}
	</form>

	<p>{{/if}}

{{$sNewDataForm}}</p>
	<p>&nbsp;</p>
	<p>
