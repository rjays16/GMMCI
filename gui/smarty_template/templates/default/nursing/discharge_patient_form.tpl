{{* discharge_patient_form.tpl : Discharge form 2004-06-12 Elpidio Latorilla *}}
{{* Note: never rename the input when redimensioning or repositioning it *}}

<ul>

<div class="prompt">{{$sPrompt}}</div>

<form action="{{$thisfile}}" name="discform" method="post" onSubmit="return pruf(this)">

	<table border=0 cellspacing="1">
		<tr>
			<td colspan=2 class="adm_input">
				{{$sBarcodeLabel}} {{$img_source}}
			</td>
		</tr>
		<tr>
			<td class="adm_item">{{$LDLocation}}:</td>
			<td class="adm_input">{{$sLocation}}</td>
		</tr>
			<td class="adm_item"><span id="w_date"></span>{{$LDDate}}:</td>
			<td class="adm_input">
				{{if $released}}
					{{$x_date}}
				{{else}}
					{{$sDateInput}} {{$sDateMiniCalendar}} {{$jsCalendarSetup}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="adm_item"><span id="w_time"></span>{{$LDClockTime}}:</td>
			<td class="adm_input">
				{{if $released}}
					{{$x_time}}
				{{else}}
					{{$sTimeInput}}
				{{/if}}
			</td>
		</tr>
		<tr id="row_disctype" style="display:none">
			<td class="adm_item">{{$LDReleaseType}}:</td>
			<td class="adm_input">
				{{$sDischargeTypes}}
			</td>
		</tr>
				<tr id="row_deaths" style="display:none">
						<td class="adm_item">Death Options : </td>
						<td class="adm_input">{{$sDeathRows}}</td>
				</tr>
		<tr id="row_notes" style="display:none">
			<td class="adm_item">{{$LDNotes}}:</td>
			<td class="adm_input">
				{{if $released}}
					{{$info}}
				{{else}}
					<textarea name="info" cols=40 rows=3></textarea>
				{{/if}}
			</td>
		</tr>
		<tr>
			<!--<td class="adm_item">{{$LDNurse}}:</td> -->
			<td class="adm_item">Encoded By:</td>
			<td class="adm_input">
				{{if $released}}
					{{$encoder}}
				{{else}}
					<input type="text" name="encoder" readonly="1" size=50 maxlength=30 value="{{$encoder}}">
				{{/if}}
			</td>
		</tr>

	{{if $bShowValidator}}
		<tr id='row_undo' style="display:none">
			<td class="adm_item">{{$stoggleIcon}}</td>
			<td class="adm_input">{{$sToggleText}}</td>
		</tr>

		<tr id='row_mgh' style="display:none">
			<td class="adm_item">{{$stoggleIcon2}}</td>
			<td class="adm_input">{{$sToggleText2}}</td>
		</tr>

		<tr id='row_discharge' style="display:none">
			<td class="adm_item">{{$pbSubmit}}</td>
			<td class="adm_input">{{$sValidatorCheckBox}} {{$LDYesSure}}</td>
		</tr>

		<tr id='row_undo_discharge' style="display:none">
			<td class="adm_item">{{$sUndoDischarge}}</td>
			<td class="adm_input">{{$sUndoDischargeText}}</td>
		</tr>

	{{/if}}

	</table>

	{{$sHiddenInputs}}

</form>

{{$pbCancel}}

</ul>
