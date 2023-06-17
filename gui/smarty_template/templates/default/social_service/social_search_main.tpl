{{* reg_search_main.tpl  Mainframe for patient/person registration search page *}}

{{$sPretext}}

{{* Never remove the $sJSFormCheck tag from this template *}}
{{$sJSFormCheck}}

<p>
<center>
<table class="admit_searchmask_border" border=0 cellpadding=10>
	<tr>
		<td>
			<table class="admit_searchmask" cellpadding="5" cellspacing="5">
			<tbody>
				<tr>
					<td>
						<form {{$sFormParams}}>
							&nbsp;
							<br>
							{{$searchprompt}}
							<br>
							{{* Never rename this input. Redimensioning it is allowed. *}}
							<input type="text" name="searchkey" id="searchkey" size=40 maxlength=80 onKeyUp="DisabledSearch();" onBlur="DisabledSearch();">
							<p>
							<!-- {{$sCheckBoxFirstName}} {{$LDIncludeFirstName}} --> 
							<!-- "First Name" in this text as search key removed by pet (aug.5,2008) in accordance with VAS' search code changes -->
							
							{{* Do not move the sHiddenInputs outside the <form> block *}}
							{{$sHiddenInputs}}
						</form>
					</td>
				</tr>
			</tbody>
			</table>
		</td>
	</tr>
</table>
</center>
<p>
{{$sCancelButton}}
<p>

{{$LDSearchFound}}

{{if $bShowResult}}
	<p>
	<table border=0 cellpadding=2 cellspacing=1>
		<tr>
			<td colspan=10>{{$sPreviousPage}}</td>
			<td align=right colspan="2">{{$sNextPage}}</td>
		</tr>
		{{* This is the title row *}}
		<tr class="reg_list_titlebar">
			<!--
			<td>{{$LDCaseNr}}</td>
			<td>{{$segEncDate}}</td>
			<td>{{$LDSex}}</td>
			<td>{{$LDLastName}}</td>
			<td>{{$LDFirstName}}</td>
			<td>{{$LDBday}}</td>
			<td>{{$segBrgy}}</td>
			<td>{{$segMuni}}</td>
			<td>{{$LDZipCode}}</td>
			<td>&nbsp;{{$LDOptions}}</td>  
			-->
			<!--added by VAN 05-08-08 -->
			<td>{{$LDPID}}</td>
			
			<td width="12%">{{$LDCaseNr}}</td>
			<!--<td>{{$segEncDate}}</td>-->
			<td width="7%">{{$LDMSSno}}</td>
			<td width="1%">{{$LDSex}}</td>
			<td width="8%" align="center">{{$LDAge}}</td>
			<td>{{$LDLastName}}</td>
			<td>{{$LDFirstName}}</td>
			<td>{{$LDMiddleName}}</td>
			
			<td width="10%">{{$LDAdmissionDate}}</td>
			<td width="10%">{{$LDDischargeDate}}</td>
			<td>{{$LDDepartment}}</td>
			
			<!-- commented by VAN 06-26-08 -->
			<!--
			<td>{{$LDBday}}</td>
			<td>{{$segBrgy}}</td>
			<td>{{$segMuni}}</td>
			-->
			<td>&nbsp;{{$LDOptions}}</td>         
			
		</tr>

		{{* The content of sResultListRows is generated using the reg_search_list_row.tpl template *}}
		{{$sResultListRows}}

		<tr>
			<td colspan=10>{{$sPreviousPage}}</td>
			<td align=right colspan="2">{{$sNextPage}}</td>
		</tr>
	</table>
{{/if}}
<hr>
{{$sPostText}}

