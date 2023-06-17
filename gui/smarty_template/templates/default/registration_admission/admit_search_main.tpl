{{* reg_search_main.tpl  Mainframe for patient/person registration search page *}}

{{$sPretext}}

{{* Never remove the $sJSFormCheck tag from this template *}}
{{$sJSFormCheck}}

<p>

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
							<br><br>
							{{* Never rename this input. Redimensioning it is allowed. *}}
							<input type="text" name="searchkey" id="searchkey" size=40 maxlength=80 onKeyUp="DisabledSearch();" onBlur="DisabledSearch();">
							
							{{* Do not move the sHiddenInputs outside the <form> block *}}
							&nbsp;{{$sHiddenInputs}}&nbsp;{{$sAllButton}}
							<p>
							{{$sCheckBoxFirstName}} {{$LDIncludeFirstName}}
							</p>
							
							<!-- added by VAN 06-25-08-->
							{{if $sClinics}}
								{{$sCheckAll}}&nbsp;{{$LDCheckAll}}&nbsp;&nbsp;&nbsp;{{$sCheckYes}}&nbsp;{{$LDCheckYes}}&nbsp;&nbsp;&nbsp;{{$sCheckNo}}&nbsp;{{$LDCheckNo}}
								<br>
							{{/if}}	
							<!-- -->
							
						</form>
					</td>
				</tr>
			</tbody>
			</table>
		</td>
	</tr>
</table>
<p>
{{$sCancelButton}}
<p>

{{$LDSearchFound}}

{{if $bShowResult}}
	<p>
	<table border=0 cellpadding=2 cellspacing=1>
		<tr>
			<td colspan=10>{{$sPreviousPage}}</td>
			<td align=right>{{$sNextPage}}</td>
		</tr>
		
		{{* This is the title row *}}
		<tr class="reg_list_titlebar">
			<td width="10%">{{$LDCaseNr}}</td>
			<td>{{$segEncDate}}</td>			
			<td>{{$segCurrentDept}}</td>			
			<td>{{$LDSex}}</td>
			<td>{{$LDLastName}}</td>
			<td>{{$LDFirstName}}</td>
			<td>{{$LDMiddleName}}</td>
			<td>{{$LDBday}}</td>
			<td>{{$segBrgy}}</td>
			<td>{{$segMuni}}</td>
<!--
			<td>{{$LDZipCode}}</td>
-->
			<td>&nbsp;{{$LDOptions}}</td> 
			
			<!-- added by VAN 06-25-08 -->
			{{if $LDServeOption}}
				<td>{{$LDServeOption}}</td>        
			{{/if}}	
			<!-- -->
		</tr>

		{{* The content of sResultListRows is generated using the reg_search_list_row.tpl template *}}
		{{$sResultListRows}}

		<tr>
			<td colspan=10>{{$sPreviousPage}}</td>
			<td align=right>{{$sNextPage}}</td>
		</tr>
	</table>
	
{{/if}}
<hr>
{{$yhPrevNext}}
{{$sPostText}}

