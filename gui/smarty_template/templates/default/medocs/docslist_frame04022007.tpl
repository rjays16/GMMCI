{{* Template for reports (notes) *}}
<table border=0 cellpadding=0 cellspacing=1 width=100%>
	<tr class="adm_item">
		<td>{{$sDeptDiagnosis}}</td>
		<td>{{$sFinalDiagnosis}}</td>
	</tr>
</table>
<table border=0 cellpadding=4 cellspacing=1 width=100%>
	<tr class="adm_item">
		<td>{{$LDDate}}</td>
		<td>{{$LDDiagnosis}}</td>
		<td>{{$LDTherapy}}</td>
		<td>{{ $LDDetails}}</td>
		<td >&nbsp;</td>
		<td>{{$LDBy}}</td>
		<td>{{$segDept_nr}}</td>
	</tr>

	{{$sDocsListRows}}

</table>
