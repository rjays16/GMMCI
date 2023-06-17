		{{*  Javascript block local to this form template *}}
		{{$sRegFormJavaScript}}

		{{* The duplicate data error block *}}
		{{if $error || $errorDupPerson}}
			<table border=0 cellspacing=0 cellpadding=0 {{$sFormWidth}}>
				{{include file="registration_admission/reg_error_duplicate.tpl"}}
			</table>
		{{/if}}

		{{* extra block for additional front text *}}
		{{$pretext}}

		{{if $bSetAsForm}}
		<!--<form method="post" action="{{$thisfile}}" id="aufnahmeform" name="aufnahmeform" ENCTYPE="multipart/form-data" onSubmit="return chkform(this)">-->
		<form method="post" action="{{$thisfile}}" id="aufnahmeform" name="aufnahmeform" ENCTYPE="multipart/form-data"  onSubmit="return false;">
		{{/if}}
<!-- edited by VAN 02-11-08-->
		<table border=0 cellspacing=0 cellpadding=0 {{$sFormWidth}} style="margin-top:10px">
				<tr>
					<td class="reg_item">
						{{$LDRegistryNr}}
					</td>
					<td class="reg_input">
						<b><font size="+2">{{$pid}}</font></b>

						{{$sBarcodeImg}}
					</td>

					<td {{$sPicTdRowSpan}} class="photo_id" align="center">
						<a href="#"  onClick="showpic(document.aufnahmeform.photo_filename)"><img {{$img_source}} name="headpic"></a>
						<br>
						{{$sFileBrowserInput}}
												<br>
												 {{$sFileBrowserUpload}}
					</td>

				</tr>

				<tr>
					<td  class="reg_item">
						{{$LDRegDate}}
					</td>
					<td class="reg_input">
						<!--<span style="color:#800000">{{$sRegDate}}</span>-->
						{{$sRegDate}}
						{{$sDateMiniCalendar}}
						{{$jsCalendarSetup}}
					</td>
				</tr>

				<tr>
					<td  class="reg_item">
						{{$LDRegTime}}
					</td>
					<td class="reg_input">
						<span style="color:#800000">{{$sRegTime}}</span>
					</td>
				</tr>

				{{* The following tags contain rows patterned after the  "registration_admission/reg_row.tpl" template *}}

				{{$segProfileType}}

				<tr>
					<td colspan=3 >&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="dashlet">
							<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="*">
										<h1>Personal Details</h1>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				{{$sPersonTitle}}
				{{$sNameLast}}
				{{$sNameFirst}}
				{{$sSuffix}}
				{{$sName2}}
				{{$sName3}}
				{{$sNameMiddle}}
				{{$sNameMaiden}}
				{{$sNameOthers}}

				<tr>
					<td class="reg_item">
						{{$LDBday}}
					</td>
					<td class="reg_input">
						{{$sBdayInput}}&nbsp;&nbsp;&nbsp;&nbsp;{{$segAge}}&nbsp;&nbsp;&nbsp;{{$sCrossImg}} {{$sDeathDate}}
					</td>
					<td>&nbsp;{{$sTempBday}}</td>
										<!-- added by LST -- 08.30.2009 -- for fingerprint enrollment -->
								<!--
										<td {{$sPicTdRowSpan}} class="photo_id" align="center">
												<a href="#"  onClick="showFPImage(document.aufnahmeform.fpimage_filename)"><img {{$fpimg_source}} name="fpimage"></a>
												<br>
												{{$sFPImageEnrollment}}
										</td>
							-->
				</tr>

                <!-- added by VAS 08-17-2012 -- for NICU patient, that will automatically copy the birth date and time as admission date -->
                {{if $sIsNewborn}}
                <tr>
                    <td class="reg_item">{{$LDBirthTime}}</td>
                    <td class="reg_input">{{$sBirthTime}}</td>
                </tr>
                {{/if}}
                <!-- -->

				<tr id="senior_row">
					<td class="reg_item">{{$LDSenior}}</td>
					<td class="reg_input">{{$sSenior}}</td>
				</tr>
				<tr id="veteran_row">
					<td class="reg_item">{{$LDVeterans}}</td>
					<td class="reg_input">{{$sVeterans}}</td>
				</tr>

				<tr>
					<td class="reg_item">
						{{$LDBirthplace}}
					</td>
					<td class="reg_input">
						{{$sBirthplace}}
					</td>
				</tr>
				<tr>
					<td class="reg_item">
						{{$LDSex}}
					</td>
					<td class="reg_input">
						{{$sSexM}} {{$LDMale}}&nbsp;&nbsp; {{$sSexF}} {{$LDFemale}}
					</td>
				</tr>

			{{if $LDBloodGroup}}
				<tr>
				<td class="reg_item">
					{{$LDBloodGroup}}
				</td>
				<td colspan=2 class="reg_input">
					{{$sBGAInput}}{{$LDA}}  &nbsp;&nbsp; {{$sBGBInput}}{{$LDB}} &nbsp;&nbsp; {{$sBGABInput}}{{$LDAB}}  &nbsp;&nbsp; {{$sBGOInput}}{{$LDO}}
				</td>
				</tr>
			{{/if}}

			{{if $LDCivilStatus}}
				<tr>
				<td class="reg_item">
					{{$LDCivilStatus}}
				</td>
				<td colspan=2 class="reg_input">
					<!--added by VAN 04-26-08-->
					{{$sCSChildInput}}{{$LDChild}}

					{{$sCSSingleInput}}{{$LDSingle}}
					{{$sCSMarriedInput}}{{$LDMarried}}
					{{$sCSDivorcedInput}}{{$LDDivorced}}
					{{$sCSWidowedInput}}{{$LDWidowed}}
					{{$sCSSeparatedInput}}{{$LDSeparated}}
				</td>
				</tr>
			{{/if}}

			{{$sReligion}}
			{{$sEthnicOrig}}
			{{$sCellPhone1}}
			{{$sPhone1}}
			<tr>
					<td colspan=3 >&nbsp;</td>
				</tr>
			<tr>
				<td colspan=2>
					<div class="dashlet">
						<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="*">
									<h1>{{$LDAddress}}</h1>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>

			{{$segAddressNew}}

			{{$sPhone2}}
			{{$sCellPhone2}}
			{{$sFax}}
			{{$sEmail}}

			{{if $sERDepartments}}
				<tr>
					<td colspan=3 >&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="dashlet">
							<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="*">
										<h1>{{$sFamilyBackground}}</h1>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>

				<!-- edited by VAN 05-19-08 -->
				{{if $segPersonInput}}

				{{if $sIsNewborn}}
				<tr>
					<td>&nbsp;</td>
					<td colspan="2">&nbsp;&nbsp;First Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Middle Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Last Name</td>
				</tr>

				<tr>
					<td class="reg_item">{{$sFather}}</td>
					<td class="reg_input" colspan="2">{{$sFather_fname}}{{$sFather_mname}}{{$sFather_lname}}</td>
				</tr>
				{{else}}
				<tr>
					<td class="reg_item">{{$sFather}}</td>
				</tr>
				{{/if}}
				{{if $sIsNewborn}}
				<tr>
					<td class="reg_item">{{$sMother}}</td>
					<td colspan="2">
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
                                <td>&nbsp;&nbsp;</td>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">First Name</span></br>{{$sMother_fname}}
								</td>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">Maiden Name</span><br />{{$sMother_mdname}}
								</td>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">Middle Name</span><br />{{$sMother_mname}}
								</td>
								<td width="*" nowrap="nowrap">
									<span class="reg_label">Last Name</span><br />{{$sMother_lname}}
								</td>
							</tr>

						</table>
					</td>
				</tr>
				{{else}}
				<tr>
					<td class="reg_item">{{$sMother}}</td>
				</tr>
				{{/if}}
				{{else}}

				<tr>
					<td class="reg_item">{{$sFather}}</td>
					<td class="reg_input" colspan="2">{{$sFather_name}}</td>
				</tr>
				<tr>
					<td class="reg_item">{{$sMother}}</td>
					<td class="reg_input" colspan="2">{{$sMother_name}}</td>
				</tr>
				{{/if}}

				{{$sSpouse}}
				{{$sGuardian}}
			{{/if}}
			{{if !$sERDepartments}}

				<tr>
					<td colspan=3 >&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="dashlet">
							<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="*">
										<h1>{{$sFamilyBackground}}</h1>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				<!--{{$sMother}}
				{{$sFather}}-->
				<!-- edited by VAN 05-19-08 -->
				{{if $segPersonInput}}
				{{if $sIsNewborn}}
				<tr class="personDetails">
					<td class="reg_item">{{$sFather}}</td>
					<td colspan="2">
						<table width="100%" cellpadding="0" cellspacing="2">
							<tr>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">First Name</span></br>{{$sFather_fname}}
								</td>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">Middle Name</span><br />{{$sFather_mname}}
								</td>
								<td width="*" nowrap="nowrap">
									<span class="reg_label">Last Name</span><br />{{$sFather_lname}}
								</td>
							</tr>
						</table>
					</td>
				</tr>
				{{else}}
				<tr class="personDetails">
					<td class="reg_item">{{$sFather}}</td>
				</tr>
				{{/if}}
				{{if $sIsNewborn}}
				<tr class="personDetails">

					<td class="reg_item">{{$sMother}}</td>
					<td colspan="2">
						<table width="100%" cellpadding="0" cellspacing="2">
							<tr>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">First Name</span></br>{{$sMother_fname}}
								</td>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">Maiden Name</span><br />{{$sMother_mdname}}
								</td>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">Middle Name</span><br />{{$sMother_mname}}
								</td>
								<td width="*" nowrap="nowrap">
									<span class="reg_label">Last Name</span><br />{{$sMother_lname}}
								</td>
							</tr>
						</table>
					</td>
				</tr>
				{{else}}
				<tr class="personDetails">
					<td class="reg_item">{{$sMother}}</td>
				</tr>
				{{/if}}

				{{else}}
				<tr class="personDetails">
					<td class="reg_item">{{$sFather}}</td>
					<td class="reg_input" colspan="2">{{$sFather_name}}</td>
				</tr>
				<tr class="personDetails">
					<td class="reg_item">{{$sMother}}</td>
					<td class="reg_input" colspan="2">{{$sMother_name}}</td>
				</tr>

				{{/if}}
				{{$sSpouse}}
				{{$sGuardian}}
			{{/if}}

			<tr>
					<td colspan=3 >&nbsp;</td>
				</tr>
			<tr>
				<td colspan="2">
					<div class="dashlet">
						<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="*">
									<h1>Other Personal Details:</h1>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
				{{* The following tags contain rows patterned after the  "registration_admission/reg_row.tpl" template *}}

				{{$sOccupation}}
				{{$sEmployer}}
				{{$sCitizenship}}
				{{$sSSSNr}}
				{{$sNatIdNr}}
			
				<tr>
					<td class="reg_item">
						{{$LDContactNo}}
					</td>
					<td class="reg_input">
						{{$sContactNo}}
					</td>
				</tr>
		

			<tr>
				<td colspan=3 >&nbsp;</td>
			</tr>
			<tr>
				<td colspan=3 >
						{{$sArrows}}
				</td>
			</tr>
			<tr>
				<td colspan=3 >&nbsp;</td>
			</tr>

			{{if $bShowInsurance}}
				<tr class="personDetails">
					<td colspan=3 >&nbsp;</td>
				</tr>
				<tr class="personDetails">
					<td colspan=2>
						<div class="dashlet">
							<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="*">
										<h1>Insurances:</h1>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				<tr class="personDetails">
					<td class="reg_item">{{$LDInsuranceClass}}&nbsp;</td>
					<td colspan=2 class="reg_input">
						{{$sErrorInsClass}}
						{{foreach from=$sInsClasses item=InsClass}}
							{{$InsClass}}
						{{/foreach}}
						&nbsp;&nbsp;<span name="iconIns" id="iconIns" style="display:none">{{$sBtnAddItem}}</span>
					</td>
				</tr>
				<!---added by VAN 09-04-07----------->
				<tr class="personDetails">
					<td class="reg_item">{{$LDInsuranceNr}}</td>
					<td colspan=2 class="reg_input">
						<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="70%">
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
					</td>
				</tr>
			{{/if}}

			{{if $bNoInsurance}}
				<tr class="personDetails">
					<td>&nbsp;</td>
					<td colspan=2 class="reg_input">
						{{$LDSeveralInsurances}}
					</td>
				</tr>
			{{/if}}

			<tr class="personDetails">
				<td colspan="3">&nbsp;</td>
			</tr>

			{{if $bShowOtherHospNr}}
				<tr class="personDetails">
					<td class="reg_item" valign=top class="reg_input">
						{{$LDOtherHospitalNr}}
					</td>
					<td colspan=2 class="reg_input">
						{{$sOtherNr}}
						{{$sOtherNrSelect}}
					</td>
				</tr>
				{{/if}}

			{{if $sERDepartments}}

				<tr class="personDetails">
					<td colspan=3>
							{{$sERDepartments}}
					</td>
				</tr>
				<tr class="personDetails">
					<td colspan=3>
							{{$sERCategory}}
					</td>
				</tr>
				<tr>
					<td colspan=3 >&nbsp;</td>
				</tr>

			{{/if}}
			<tr class="personDetails">
				<td class="reg_item">
					{{$LDRegBy}}
				</td>
				<td colspan=2 class="reg_input">
					{{$sRegByInput}}
				</td>
			</tr>
			<tr class="personDetails">
				<td class="reg_item">
					{{$LDDept}}
				</td>
				<td colspan=2 class="reg_input">
						{{$sDeptInput}}
				</td>
			</tr>
		</table>

		{{$sHiddenInputs}}
		{{$sUpdateHiddenInputs}}
		<p>
		{{$pbSubmit}} {{$pbERSubmit}} &nbsp;&nbsp; {{$pbReset}}  &nbsp;&nbsp; {{$pbForceSave}}

		{{if $bSetAsForm}}
		</form>
		{{/if}}

		{{$sNewDataForm}}