{{* Template for medocs (medical diagnosis/therapy record) *}}
{{* Note: the input tags are left here in raw form to give the GUI designer freedom to change  the input dimensions *}}
{{* Note: be very careful not to rename nor change the type of the input  *}}

{{if $bSetAsForm}}
{{$sDocShotcuts}}
{{$sDocsJavaScript}}
<form method="post" id="entryform" name="entryform" onsubmit="return false">
{{/if}}				
<!-- chkForm(this) -->
<table border=0 cellpadding=2 width=100%>
	{{if $sDiagnosisNotes}}	   
	<tr bgcolor='#f6f6f6'>
	    <!-- <td>{{$LDExtraInfo}}<br />({{$LDInsurance}})</br></td>  -->   
	    <td width="25%"> Admitting Diagnosis</td>
		<td width="75%">
			{{*if $bSetAsForm*}}
				<!--<textarea name='aux_notes' id='aux_notes' type='hidden' cols=80 rows=3 wrap='physical' readonly="readonly"></textarea> -->
		   		{{$txtAreaDiagnosis}}	
		   {{*else*}}
				{{*$sExtraInfo*}}
			{{*/if*}}		</td>
	</tr>
	{{/if}}
	{{ if $sSetConsult}}
	<tr bgcolor='#f6f6f6'>
		<td><font color="red">*</font>Consulting Doctor & Department</td>
		<td>
			{{$consultingDoc}} {{$consultingDept}}
		</td>
	</tr>
	{{/if}}
	<!-- start -->
	{{if $sAdmittedOpd_a}}
		<tr bgcolor="#f6f6f6">
   		<td> &nbsp;&nbsp; Admission Date/Time</td>
			<td>
				<input type="text" size="10" maxlength="10" id="txtAdmissionDate" name="txtAdmissionDate" value="{{$sAdmissionDate}}" readonly /> 
				<input type="text" size="10" maxlength="10" id="txtAdmissionTime" name="txtAdmissionTime" value="{{$sAdmissionTime}}" readonly /> 
			</td>
   		</tr>
		{{if $sSetDischarged}}
		   <tr bgcolor='#f6f6f6'>
			 <td><FONT  color='red'>*</font>  {{*$LDDate*}}Discharge Date/Time</td>
			 <td>
				{{if $bSetAsForm}}
					 <br> 
					 <input type='text' name='date_text_d' size=10 maxlength=10 {{$sDateValidateJs_d}} />
					 {{$sDateMiniCalendar_d}} 
					<input type='text' id='time_text_d' name='time_text_d' size="4" maxlength="5" {{$sFormatTime}} />
					<select id='selAMPM' name="selAMPM">
						<option value="A.M.">A.M.</option>
						<option value="P.M.">P.M.</option>
					</select>
				{{else}}
					{{$sDate}}
				{{/if}}			 </td>
		   </tr>
	   {{/if}}
	   {{if $sSetDeptDischarged}}
		   <tr bgcolor="#f6f6f6">
				<td><font color="red">*</font> Attending Physician &amp; Department </td>
				<td>
					{{$sDoctorInputF}} {{$sDeptInputF}}				</td>
		   </tr>
	   {{/if}}
	   <tr bgcolor='#f6f6f6'>
		 <td><FONT  color='red'>*</font>  Encoded {{$LDBy}} </td>
		 <td>
			{{if $bSetAsForm}}
				<input type='text' name='personell_name' size=50 maxlength=60 value='{{$TP_user_name}}' readonly />
			{{else}}
				{{$sAuthor}}
			{{/if}}		 </td>
	   </tr>
	{{/if}}   
	<!-- end -->   
   {{if $bSetUpdate}}}
		<tr bgcolor='f6f6f6'>
			<td><font color=red>*</font> Department</td>
			<td>
				{{$sDeptInput1}}			</td>
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
			        	<td height="100" valign="top"><font color="red">*</font>{{$LDDiagnosis}}</td>
			        </tr>
			    </table>			</td>
				<td>{{if $sSetDeptDiagnosis}} {{$sDoctorInputD}} {{$sDeptInputD}} <br /> {{/if}}
				 {{$codeControl1}}</td>
		</tr>
		
		<!--  start-->
		
		
		<!--<tr bgcolor='f6f6f6'>
			<td valign="top">
				<table width="200" border="0" bordercolor="#F6F6F6">
					<tr>
						<td height="100" valign="top"> Operations Notes</td>
					</tr>
				</table>			</td>
				<!--<td>{{if $sSetDeptTherapy}} {{*$sDoctorInputP*}} {{*$sDeptInputP*}}  <br /> {{/if}}
				    {{*$codeControl2*}}</td>-->
					<!--<td>
					  <textarea name="aux_notes_p" id="aux_notes_p" cols="80" rows="3" wrap="physical" readonly="readonly"></textarea>
					</td>-->
		<!--</tr>-->
		
		<!--  end--><!--  end-->
		
		<!-- OPERATION AREA -->
		<tr bgcolor='f6f6f6'>
			<td valign="top">
				<table width="200" border="0" bordercolor="#F6F6F6">
					<tr>
						<td height="100" valign="top"><font color="red">*</font> Operations</td>
					</tr>
				</table>			</td>
				<td>{{if $sSetDeptTherapy}} {{$sDoctorInputP}} {{$sDeptInputP}} &nbsp; 
						<br/> <input type='text' name='date_text_p' id='date_text_p' size=10 maxlength=10 {{$sDateValidateJs_p}} />  {{$sDateMiniCalendar_p}}{{$sTimeP}}  <br /> {{/if}}
				    {{$codeControl2}}</td>
		</tr>
	{{if $sSetCon}}
		<tr bgcolor='#f6f6f6'>
			<td height="88" valign="top"><font color="red">*</font> Condition</td>
			<td>
				<table width="63%" height="84" border="0" cellpadding="1" id="srcResultTable" style="width:100%; font-size:12px">
					<td width="36%" height="80" valign="middle" id="leftTdResult">
						{{$rowConditionA}}					</td>
					<td width="64%" valign="middle" id="rightTdResult">
						{{$rowConditionB}}					</td>
				</table>			</td>
		</tr>
	{{/if}}
	
	{{if $sSetResult}}
		<tr bgcolor='#f6f6f6'>	
			<td height="88" valign="top"  ><font color="red">*</font>  Result</td>
			<td>
				<table width="63%" height="84" border="0" cellpadding="1" id="srcResultTable" style="width:100%; font-size:12px">
					<td width="36%" height="80" valign="middle" id="leftTdResult">
						{{$rowResultA}}					</td>
					<td width="64%" valign="middle" id="rightTdResult">
						{{$rowResultB}}					</td>
				</table>			</td>
		</tr>
	{{/if}}
	{{if $sSetResult}}		
		<tr bgcolor='#f6f6f6' id="rwDisposition">		
			<td height="88" valign="top"><font color="red">*</font>  Disposition</td>
			<td>
				<table width="63%" height="84" border="0" cellpadding="1" id="srcDispTable" style="width:100%; font-size:12px">		
					<td width="36%" valign="middle" height="80" id="leftTdDesposition">
						{{$rowDispA}}					</td>
					<td width="64%" valign="middle" id="rightTdDesposition">
						{{$rowDispB}}					</td>
				</table>			</td>
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
   
   {{if $sAdmittedOpd_b}}
		<tr bgcolor="#f6f6f6">
			<td> &nbsp;&nbsp; Admission Date/Time</td>
				<td>
					<input type="text" size="10" maxlength="10" id="txtAdmissionDate" name="txtAdmissionDate" value="{{$sAdmissionDate}}" readonly /> 
					<input type="text" size="10" maxlength="10" id="txtAdmissionTime" name="txtAdmissionTime" value="{{$sAdmissionTime}}" readonly /> 
				</td>
		</tr>
	{{/if}}	
	{{if $sAdmittedOpd_b}}
	   {{if $sSetDischarged}}
		   <tr bgcolor='#f6f6f6' id="rwDischarged">
			 <td><FONT  color='red'>*</font>  {{*$LDDate*}}Discharge Date/Time</td>
			 <td>
				{{if $bSetAsForm}}
					 <br>
					 <input type='text' name='date_text_d' size=10 maxlength=10 {{$sDateValidateJs_d}} /> 
					 {{$sDateMiniCalendar_d}} 
					<input type='text' id='time_text_d' name='time_text_d' size="4" maxlength="5" {{$sFormatTime}} />
					<select id='selAMPM' name="selAMPM">
						<option value="A.M.">A.M.</option>
						<option value="P.M.">P.M.</option>
					</select>
				{{else}}
					{{$sDate}}
				{{/if}}			 </td>
		   </tr>
	   {{/if}}
	{{/if}}
	{{if $sAdmittedOpd_b}}	 
	   {{if $sSetDeptDischarged}}
		   <tr bgcolor="#f6f6f6">
				<td><font color="red">*</font> Attending Physician &amp; Department </td>
				<td>
					{{$sDoctorInputF}} {{$sDeptInputF}}				</td>
		   </tr>
	   {{/if}}
	  
	   <tr bgcolor='#f6f6f6'>
		 <td><FONT  color='red'>*</font> Encoded {{$LDBy}} </td>
		 <td>
			{{if $bSetAsForm}}
				<input type='text' name='personell_name' size=50 maxlength=60 value='{{$TP_user_name}}' readonly />
			{{else}}
				{{$sAuthor}}
			{{/if}}		 </td>
	   </tr>
   {{/if}}  
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