{{* Template for reports (notes) *}}
	<br>
	{{if $isOpdInpatient}}
		<table border=0 cellpadding=4 cellspacing=1  width= 100%>
			<tr>
				<td align="left" width="10%"><b>{{$segOpdBtn}}</b></td>
				<td align="left" ><b>{{$segInpatientBtn}}</b></td>
			</tr>
		</table>
	{{/if}}
	<b>{{$segHeadingPrincipal}}</b>
	<table border=0 cellpadding=4 cellspacing=1 width=100%>
		<tr class="adm_item">
			<td align="center" width="50%"><b>{{$LDDiagnosis}}</b></td>
			<td align="center"><b>{{$LDTherapy}}</b></td>
		</tr>
		{{if $segSetHeadingPrincipal}}
			{{$sDocsListRowsPrincipal}}
		{{else}}
			<tr {{$sRowClass}}>
				<td colspan="2" align="center"><font color="red">No Principal Diagnosis/Procedure</font></td>
			</tr>		
		{{/if}}	
	</table>
	<br>
	<b>{{$segHeadingOthers}}</b>
	<table border=0 cellpadding=4 cellspacing=1 width=100%>
		<tr class="adm_item">
			<td align="center" width="50%"><b>{{$LDDiagnosis}}</b></td>
			<td align="center"><b>{{$LDTherapy}}</b></td>
		</tr>
	
		{{if $segSetHeadingOthers}}
			{{$sDocsListRowsOthers}}
		{{else}}		
			<tr {{$sRowClass}}>
				<td colspan="2" align="center"><font color="red">No Other Diagnosis/Procedure</font></td>
			</tr>		
		{{/if}}	
	</table>
{{$sDetailsIcon}}


