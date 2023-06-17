{{* Template for admission input and data display *}}
{{* Files using this: *}}
{{* - /modules/registration_admission/aufnahme_start.php *}}
{{* - /modules/registration_admission/aufnahme_daten_zeigen.php *}}

	{{if $bSetAsForm}}
	<form method="post" action="{{$thisfile}}" name="aufnahmeform" onSubmit="return chkform(this)">
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

				<tr>
					<td  class="adm_item">
						{{$LDAdmitDate}}:
					</td>
					<td class="adm_input">
						{{$sAdmitDate}}</td>
				</tr>

				<tr>
					<td class="adm_item">
					{{$LDAdmitTime}}:
					</td>
					<td class="adm_input">
						{{$sAdmitTime}}
					</td>
				</tr>

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
					<td bgcolor="#ffffee" class="vi_data">
						{{$sBdayDate}} &nbsp; {{$sCrossImg}} &nbsp; <font color="black">{{$sDeathDate}}</font>
					</td>
					<td bgcolor="#ffffee">
						{{$LDSex}}: {{$sSexType}}
					</td>
				</tr>

			{{if $LDBloodGroup}}
				<tr>
					<td class="adm_item">
						{{$LDBloodGroup}}:
					</td>
					<td class="adm_input" colspan=2>&nbsp;
						{{$blood_group}}
					</td>
				</tr>
			{{/if}}

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
				<!---added 03-07-07---->	
			{{if $LDWard}}
				<tr{{$segERDetailsHideable}}>
					<td class="adm_item">
						<font color="red">{{$LDWard}}</font>:
					</td>
					<td colspan=2 class="adm_input">
						{{$sWardInput}}
					</td>
				</tr>
			{{/if}}	
				
				<!----added 02-27-07 -->
				<tr{{$segERDetailsHideable}}>
					<td class="adm_item">
						{{$LDDoctor}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$doctor_name}}
					</td>
					<td colspan=2 >
						{{$doctor_name2}}
					</td>
				</tr>
				
				<!----added 02-27-07 -->

			<!--{{if $LDDepartment}}-->
				<tr{{$segERDetailsHideable}}>
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
				<tr class="ERDetails">
					<td class="adm_item">
						{{$segERDiagnosis}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$er_opd_diagnosis}}
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
						{{$sBillTypeInput}}
					</td>
				</tr>
			 {{/if}}
				<!-- edited 03-06-07------------->
				
			 {{if $LDInsuranceNr}}
				<tr>
					<td class="adm_item">
						{{$LDInsuranceNr}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$insurance_nr}}
					</td>
					
				</tr>
			  {{/if}}
			  
			  {{if $LDInsuranceCo}}
				<tr>
					<td class="adm_item">
						{{$LDInsuranceCo}}:
					</td>
					<td colspan=2 class="adm_input">
						{{$insurance_firm_name}}
					</td>
					<!--
					<td colspan=2 >
						{{$insurance_firm_name}}
					</td>
					-->
				</tr>
			  {{/if}}	
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
						{{$sResults}}
					</td>
					
				</tr>
				{{/if}}
				{{if $LDDisposition && $segShowIfFromER}}
				<tr class="ERDetails">
					<td class="adm_item">
						<font color="red">{{$LDDisposition}}</font>:
					</td>
					<td colspan=2 class="adm_input">
						{{$sDisposition}}
					</td>
				</tr>
				{{/if}}

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

	{{if $bSetAsForm}}
	</form>

	<p>{{/if}}

{{$sNewDataForm}}</p>
	<p>&nbsp;</p>
	<p>
