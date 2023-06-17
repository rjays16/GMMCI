{{* ward_occupancy_list.tpl  2004-05-15 Elpidio Latorilla *}}
{{* Table frame for the occupancy list *}}

<table>
		<tr>
			<td class="adm_item">
				Date and Time (if Not Real time):
			</td>
			<td colspan=2 class="adm_input">
				{{$sLDDateFrom}}
				{{$sDateMiniCalendar}}
				{{$jsCalendarSetup}}
				&nbsp;&nbsp;
				{{$sLDTimeFrom}}
			</td>
		</tr>
</table>
&nbsp;&nbsp;
<table cellspacing="0" width="100%">
<tbody>
	<tr>
		<td class="adm_item">{{$LDRoom}}</td>
		<td class="adm_item">{{$LDBed}}</td>
		<td class="adm_item">{{$LDFamilyName}}, {{$LDName}}</td>
		<td class="adm_item">{{$LDBirthDate}}</td>
		<td class="adm_item">{{$LDBillType}}</td>
		<td class="adm_item">&nbsp;</td>
	</tr>

	{{$sOccListRows}}

 </tbody>
</table>
