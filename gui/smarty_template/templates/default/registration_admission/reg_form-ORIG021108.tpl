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
		<form method="post" action="{{$thisfile}}" id="aufnahmeform" name="aufnahmeform" ENCTYPE="multipart/form-data" onSubmit="return chkform(this)">
		{{/if}}

		<table border=0 cellspacing=0 cellpadding=0 {{$sFormWidth}}>
				<tr>
					<td class="reg_item">
						{{$LDRegistryNr}}
					</td>
					<td class="reg_input">
						{{$pid}}
						<br>
						{{$sBarcodeImg}}
					</td>
					<td {{$sPicTdRowSpan}} class="photo_id" align="center">
						<a href="#"  onClick="showpic(document.aufnahmeform.photo_filename)"><img {{$img_source}} name="headpic"></a>
						<br>
						{{$LDPhoto}}
						<br>
						{{$sFileBrowserInput}}
					</td>
				</tr>

				<tr>
					<td  class="reg_item">
						{{$LDRegDate}}
					</td>
					<td class="reg_input">
						<FONT color="#800000">
						{{$sRegDate}}
					</td>
				</tr>

				<tr>
					<td  class="reg_item">
						{{$LDRegTime}}
					</td>
					<td class="reg_input">
						<FONT color="#800000">
						{{$sRegTime}}
					</td>
				</tr>

				{{* The following tags contain rows patterned after the  "registration_admission/reg_row.tpl" template *}}

				{{$segProfileType}}
				{{$sPersonTitle}}
				{{$sNameLast}}
				{{$sNameFirst}}
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
						{{$sBdayInput}}&nbsp;{{$segAge}}&nbsp;{{$sCrossImg}} {{$sDeathDate}}
					</td>
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
					{{$sCSSingleInput}}{{$LDSingle}}  &nbsp;&nbsp;
					{{$sCSMarriedInput}}{{$LDMarried}} &nbsp;&nbsp;
					{{$sCSDivorcedInput}}{{$LDDivorced}}  &nbsp;&nbsp;
					{{$sCSWidowedInput}}{{$LDWidowed}} &nbsp;&nbsp;
					{{$sCSSeparatedInput}}{{$LDSeparated}}
				</td>
				</tr>
			{{/if}}

				<tr>
				<td colspan=3>
					{{$LDAddress}}
				</td>
				</tr>
				{{$segAddressNew}}
<!--
				<tr>
					<td class="reg_item">
						{{$LDStreet}}
					</td>
					<td class="reg_input">
						{{$sStreetInput}}
					</td>
					<td class="reg_input">
						{{$LDStreetNr}} &nbsp;&nbsp; {{$sStreetNrInput}}
					</td>
				</tr>

				<tr>
					<td class="reg_item">
						{{$LDTownCity}}
					</td>
					<td class="reg_input">
						{{$sTownCityInput}} {{$sTownCityMiniCalendar}}
					</td>
					<td class="reg_input">
						{{$LDZipCode}} &nbsp;&nbsp; {{$sZipCodeInput}}
					</td>
				</tr>
-->
			{{if $sERDepartments}}
				<tr>
					<td colspan=3 >&nbsp;</td>
				</tr>
				<tr>
					<td colspan=3 >
							{{$sERDepartments}}
					</td>
				</tr>
				<tr>
					<td colspan=3 >&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3">
						{{$sFamilyBackground}}
					</td>
				</tr>
				{{$sMother}}
				{{$sFather}}
				{{$sSpouse}}
				{{$sGuardian}}
			{{/if}}

				<tr>
					<td colspan=3 >&nbsp;</td>
				</tr>
				{{$sReligion}}
				{{$sPhone1}}

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
					<td colspan=3>
						{{$LDInsuranceBurn}}
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
					<td class="reg_item">{{$LDInsuranceNr}}:</td>
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
							</tbody>
						</table>
					</td>
				</tr>
				<!--commented by VAN 09-04-07--->		
				<!--
				{{$sInsuranceNr}}

				<tr class="personDetails">
					<td class="reg_item">
						{{$LDInsuranceCo}}
					</td>
					<td colspan=2 class="reg_input">
						{{$sInsCoNameInput}} {{$sInsCoMiniCalendar}}
					</td>
				</tr>
				<tr class="personDetails">
                <td colspan=3>&nbsp;</td>
				</tr>
				-->
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
					<td colspan="3">&nbsp;
						
					</td>
				</tr>
				{{* The following tags contain rows patterned after the  "registration_admission/reg_row.tpl" template *}}

				{{$sPhone2}}
				{{$sCellPhone1}}
				{{$sCellPhone2}}
				{{$sFax}}
				{{$sEmail}}
				{{$sCitizenship}}
				{{$sSSSNr}}
				{{$sNatIdNr}}
				
				{{$sOccupation}}

				<tr class="personDetails">
					<td class="reg_item">
						{{$LDEthnicOrig}}
					</td>
					<td colspan=2 class="reg_input">
						{{$sEthnicOrigInput}} {{$sEthnicOrigMiniCalendar}}
					</td>
				</tr>

			{{if !$sERDepartments}}				
				<tr class="personDetails">
					<td colspan="3">
						{{$sFamilyBackground}}
					</td>
				</tr>
				{{$sMother}}
				{{$sFather}}
				{{$sSpouse}}
				{{$sGuardian}}
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
			<tr>
				<td class="reg_item">
					{{$LDRegBy}}
				</td>
				<td colspan=2 class="reg_input">
					{{$sRegByInput}}
				</td>
			</tr>
			<tr>
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
		{{$pbSubmit}} &nbsp;&nbsp; {{$pbReset}} {{$pbERSubmit}} &nbsp;&nbsp; {{$pbForceSave}}

		{{if $bSetAsForm}}
		</form>
		{{/if}}

		{{$sNewDataForm}}
