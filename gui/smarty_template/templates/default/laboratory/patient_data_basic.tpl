{{* patient_data_basic.tpl  Shows very basic patient data 2004-06-27 Elpidio Latorilla *}}

<table>
<tbody>
	<!--added by VAN 01-29-08 -->
	<tr>
		<td class="adm_item">{{$LDHospNr}}:</td>
		<td class="adm_input">{{$encounter_nr}}</td>
	</tr>
	<tr>
		<td class="adm_item">{{$LDCaseNr}}:</td>
		<td class="adm_input">{{$pid}}</td>
	</tr>
	<!--commented by VAN 01-29-08 -->
	<!--
	<tr>
		<td class="adm_item">{{$LDLastName}}, {{$LDName}}, {{$LDBday}}:</td>
		<td class="adm_input"><b>{{$sLastName}}, {{$sName}} {{$sBday}}</b></td>
	</tr>
	-->
	<!--added by VAN 01-29-08 -->
	<tr>
		<td class="adm_item">{{$LDLastName}}, {{$LDName}}:</td>
		<td class="adm_input"><b>{{$sLastName}}, {{$sName}}</b></td>
	</tr>
	<tr>
		<td class="adm_item">{{$LDBday}}:</td>
		<td class="adm_input">{{$sBday}}</td>
	</tr>
</tbody>
</table>