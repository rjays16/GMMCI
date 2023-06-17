<style type="text/css">
<!--
body {
	background-color: #EBF0FE;
}
-->
</style>


{{$LDSearchFound}}

{{if $bShowResult}}

<div align="center">
	<table border=0 cellpadding=2 cellspacing=1>

		{{* This is the title row *}}
		<tr class="reg_list_titlebar">
			<td width="10%">{{$LDRegistryNr}}</td>
			<td width="1%">{{$LDSex}}</td>
			<td width="*">{{$LDLastName}}</td>
			<td width="15%">{{$LDFirstName}}</td>
			<td width="10%">{{$LDMiddleName}}</td>
			<td width="5%">{{$LDBday}}</td>
			<td width="10%">{{$segBrgy}}</td>
			<td width="10%">{{$segMuni}}</td>
			<td width="5%">{{$LDZipCode}}</td>
			<td width="16%">{{$LDOptions}}</td>
		</tr>

		{{* The content of sResultListRows is generated using the reg_search_list_row.tpl template *}}
		{{$sResultListRows}}

		<tr>
			<td colspan=8>{{$sPreviousPage}}</td>
			<td align=right>{{$sNextPage}}</td>
		</tr>
	</table>
{{/if}}
	{{$sPostText}}
</div>