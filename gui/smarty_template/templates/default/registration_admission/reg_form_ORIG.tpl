		{{*  Javascript block local to this form template *}}
		{{$sRegFormJavaScript}}

		{{* The duplicate data error block *}}
		{{if $error || $errorDupPerson}}
			{{include file="registration_admission/reg_error_duplicate.tpl"}}
		{{/if}}

		{{* extra block for additional front text *}}
		{{$pretext}}
		
		{{if $bSetAsForm}}
		<form method="post" action="{{$thisfile}}" name="aufnahmeform" ENCTYPE="multipart/form-data" onSubmit="return chkform(this)">
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
						{{$sBdayInput}}&nbsp;{{$sCrossImg}} {{$sDeathDate}}
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
					<td colspan=3 >&nbsp;
							
					</td>
				</tr>
				<tr>
					<td colspan=3 >
							{{$sERDepartments}}
					</td>
				</tr>
			{{/if}}

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
				<tr>
					<td colspan=3>
						{{$LDInsuranceBurn}}
					</td>
				</tr>
				<tr>
				<td class="reg_item">{{$LDInsuranceClass}}&nbsp;
					
				</td>
				<td colspan=2 class="reg_input">
					{{$sErrorInsClass}} 
					{{foreach from=$sInsClasses item=InsClass}}
						{{$InsClass}}
					{{/foreach}}
				</td>
				</tr>
				{{$sInsuranceNr}}

				<tr>
				<td class="reg_item">
					{{$LDInsuranceCo}}
				</td>
				<td colspan=2 class="reg_input">
					{{$sInsCoNameInput}} {{$sInsCoMiniCalendar}}
				</td>
				</tr>
				<tr>
                <td colspan=3>&nbsp;
					
				</td>
				</tr>
			{{/if}}

			{{if $bNoInsurance}}
				<tr>
				<td>&nbsp;
					
				</td>
				<td colspan=2 class="reg_input">
					{{$LDSeveralInsurances}}
				</td>
				</tr>
			{{/if}}

				{{* The following tags contain rows patterned after the  "registration_admission/reg_row.tpl" template *}}

				{{$sPhone1}}
				{{$sPhone2}}
				{{$sCellPhone1}}
				{{$sCellPhone2}}
				{{$sFax}}
				{{$sEmail}}
				{{$sCitizenship}}
				{{$sSSSNr}}
				{{$sNatIdNr}}
				{{$sReligion}}
				{{$sOccupation}}

				<tr>
				<td class="reg_item">
					{{$LDEthnicOrig}}
				</td>
				<td colspan=2 class="reg_input">
					{{$sEthnicOrigInput}} {{$sEthnicOrigMiniCalendar}}
				</td>
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
				<tr>
					<td colspan="3">&nbsp;
						
					</td>
				</tr>
			
			{{if $bShowOtherHospNr}}
				<tr>
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
