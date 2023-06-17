{{* ward_occupancy_list_row.tpl 2004-06-15 Elpidio Latorilla *}}
{{* One row for each occupant or room/bed *}}
{{* This template is used by /modules/nursing/nursing_station.php to populate the ward_occupancy_list.tpl template *}}

 {{if $bToggleRowClass}}
	<tr class="{{$class_label}}">
 {{else}}
	<tr class="{{$class_label}}">
 {{/if}}
		<td>{{$sMiniColorBars}}</td>
		<td style="font-size:x-small">{{$sRoom}}</td>
		<td style="font-size:x-small ">&nbsp;{{$sBed}} {{$sBedIcon}}</td>
		<td >{{$sTitle}} {{$sFamilyName}}{{$cComma}} {{$sName}}</td>
		<td style="font-size:x-small ">{{$sBirthDate}}</td>
		<td style="font-size:x-small ">&nbsp;{{$sPatNr}}</td>
	<!--	<td style="font-size:x-small ">&nbsp;{{$sInsuranceType}}</td>-->
		<td style="font-size:x-small ">&nbsp;{{$sCaseNo}}</td>
		<td>&nbsp;{{$sAdmitDataIcon}} {{$sChartFolderIcon}} {{$sNotesIcon}} {{$sTransferIcon}} {{$sDischargeIcon}}</td>
		</tr>
				 
				 {{if $isBaby}}
					{{$BabyRows}}
				 {{else}}
				 {{/if}}

		<!-- dati code, jan. 24, 2010
				{{if $isBaby}}
				{{if $bToggleRowClass}}
				<tr class="wardlistrow1">
			 {{else}}
				<tr class="wardlistrow2">
			 {{/if}}
					<td></td>
					<td style="font-size:x-small">{{$sRoom}}</td>
					<td style="font-size:x-small ">&nbsp;{{$sBed}} {{$sBabyBedIcon}}</td>
					<td>{{$sBabyIcon}} {{$sBabyFamilyName}}{{$cComma}} {{$sBabyName}}</td>
					<td style="font-size:x-small ">{{$sBabyBirthDate}}</td>
					<td style="font-size:x-small ">&nbsp;{{$sBabyPatNr}}</td>
					<td></td>
					<td>&nbsp;{{$sBabyNotesIcon}} {{$sBabyTransferIcon}} {{*$sBabyDischargeIcon*}}</td>
					</tr>
			 {{else}}
			 {{/if}}
		-->
		
		<tr>
		<td colspan="8" class="thinrow_vspacer">{{$sOnePixel}}</td>
		</tr>
