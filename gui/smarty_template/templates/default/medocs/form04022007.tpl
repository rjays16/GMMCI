{{* Template for medocs (medical diagnosis/therapy record) *}}
{{* Note: the input tags are left here in raw form to give the GUI designer freedom to change  the input dimensions *}}
{{* Note: be very careful not to rename nor change the type of the input  *}}

{{if $bSetAsForm}}
{{$sDocsJavaScript}}
<form method="post" id="entryform" name="entryform" onSubmit="return chkForm(this)">
{{/if}}

<table border=0 cellpadding=2 width=100%>
   <tr bgcolor='#f6f6f6'>
     <td>{{$LDExtraInfo}}<br>({{$LDInsurance}})</td>
     <td>

	 	{{if $bSetAsForm}}
			<textarea name='aux_notes' cols=60 rows=1 wrap='physical'></textarea>
		{{else}}
			{{$sExtraInfo}}
		{{/if}}

	 </td>
   </tr>
   <!--
   <tr bgcolor='#f6f6f6'>
     <td><FONT color=red>*</font>  {{$LDGotMedAdvice}}</td>
     <td>
	 	{{if $bSetAsForm}}
	 		{{$sYesRadio}} {{$LDYes}}
         	{{$sNoRadio}} {{$LDNo}}
		{{else}}
			{{$sYesNo}}
		{{/if}}

      </td>
   </tr> -->
   {{if $bSetUpdate}}}
	   <tr bgcolor='f6f6f6'>
	   	<td><font color=red>*</font> Department</td>
	   	<td>
	   		{{$sDeptInput1}}
	   	</td>
	   </tr>
		<tr bgcolor='f6f6f6'>
			<td><font color=red>*</font> Attending Doctor</td>
			<td>{{$sDoctorInput1}}</td>
		</tr>
	{{/if}}
	{{if $bSetAsForm}}
		<tr bgcolor="#f6f6f6">
			<td valign="top">	
				<table width="200" border="0" bordercolor="#F6F6F6">
			    	<tr>
			        	<td height="100" valign="top"><font color="red">*</font>  {{$LDDiagnosis}}</td>
			        </tr>
			    </table>
			</td>
			{{if $bSetEntry}}                           
				<td> <!--{{*$sDoctorInputD*}} {{*$sDeptInputD*}}&nbsp
				<input type='text' name='date_text_d' size=10 maxlength=10 {{$sDateValidateJs_d}}>
				{{*$sDateMiniCalendar_d*}}<br> -->
				{{$codeControl1}}
				</td>
			{{/if}}   	
			   	<!-- <textarea name='text_diagnosis' cols=60 rows=1 wrap='physical'></textarea>  --> 
		</tr>
		<tr bgcolor='f6f6f6'>
			<td valign="top">
				<table width="200" border="0" bordercolor="#F6F6F6">
					<tr>
						<td height="100" valign="top"><font color="red">*</font> Operations</td>
					</tr>
				</table>
			</td>
			{{if $bSetEntry}}
				<td> <!--{{*$sDoctorInputP*}} {{*$sDeptInputP*}}&nbsp
				<input type='text' name='date_text_p' size=10 maxlength=10 {{$sDateValidateJs_p}}>
				{{*$sDateMiniCalendar_p*}}<br> -->
				{{$codeControl2}}</td>
			{{/if}}
		<!-- <textarea name='text_diagnosis' cols=60 rows=1 wrap='physical'></textarea>  -->
		</tr>
	{{if $sSetResult}}
		{{if $sSetCon}}
			<tr bgcolor='#f6f6f6'>
				<td height="88" valign="top"><font color="red">*</font> Condition</td>
				<td>
					<table width="63%" height="84" border="0" cellpadding="1" id="srcResultTable" style="width:100%; font-size:12px">
						<td width="36%" height="80" valign="middle" id="leftTdResult">
							{{$rowConditionA}}
						</td>
						<td width="64%" valign="middle" id="rightTdResult">
							{{$rowConditionB}}
						</td>
					</table>
				<td>
			</tr>
		{{/if}}
		<tr bgcolor='#f6f6f6'>	
			<td height="88" valign="top"  ><font color="red">*</font>  Result</td>
			<td>
				<table width="63%" height="84" border="0" cellpadding="1" id="srcResultTable" style="width:100%; font-size:12px">
					<td width="36%" height="80" valign="middle" id="leftTdResult">
						{{$rowResultA}}
					</td>
					<td width="64%" valign="middle" id="rightTdResult">
						{{$rowResultB}}
					</td>
				</table>
			</td>
		</tr>
	{{/if}}
	{{if $sSetResult}}		
		<tr bgcolor='#f6f6f6'>		
			<td height="88" valign="top"><font color="red">*</font>  Disposition</td>
			<td>
				<table width="63%" height="82" border="0" cellpadding="1" id="srcDispTable" style="width:100%; font-size:12px">		
					<td width="36%" valign="middle" height="78" id="leftTdDesposition">
						{{$rowDispA}}
						
					</td>
					<td width="64%" valign="middle" id="rightTdDesposition">
						{{$rowDispB}}
						<br /><br /><br />
					</td>
				</table>
			</td>
		</tr>
	{{/if}}
	{{else}}
	<tr bgcolor='#f6f6f6'>
     <td><FONT  color='red'>*</font>{{$LDDiagnosis}}</td>
     	<td>{{$sDiagnosis}}</td>
   </tr>
   {{*/if*}}
   {{*if $bSetAsForm*}}
			<!--<textarea name='text_therapy' cols=60 rows=1 wrap='physical'></textarea> -->		
	{{*else*}}
	<tr bgcolor='#f6f6f6'>
    <td><FONT  color='red'>*</font>  {{$LDTherapy}}</td>
		<td>{{$sTherapy}}</td>
  </tr>
  	{{if $sSetResult}}
		<tr bgcolor="#f6f6f6">
			<td><font color='red'>*</font>  Result</td>
			<td>{{$sResult}}</td>
		</tr> 
		<tr bgcolor="#f6f6f6">
			<td><font color='red'>*</font>  Disposition</td>
			<td>{{$sDisposition}}</td>
		</tr>
	{{/if}}   
   {{/if}}
<!-- 
   <tr bgcolor='#f6f6f6'>
     <td><FONT  color='red'>*</font>  {{$LDDate}}</td>
     <td>
	 	{{*if $bSetAsForm*}}
			 <input type='text' name='date' size=10 maxlength=10 {{$sDateValidateJs}}>
			{{*$sDateMiniCalendar*}}
		{{*else*}}
			{{*$sDate*}}
		{{*/if*}}
	 </td>
   </tr>
   -->
	 <tr bgcolor='#f6f6f6'>
     <td><FONT  color='red'>*</font>  {{$LDBy}} </td>
     <td>
	 	{{if $bSetAsForm}}
	 		<input type='text' name='personell_name' size=50 maxlength=60 value='{{$TP_user_name}}' readonly>
		{{else}}
			{{$sAuthor}}
		{{/if}}
	 </td>
   </tr>
</table>
{{if $bSetAsForm}}
	{{$frmIcd_old}}
{{/if}}

{{if $bSetAsForm}}
	{{$sHiddenInputs}}
	{{$sTailScripts}}
	{{$sTailScripts2}}
	</form>
{{/if}}