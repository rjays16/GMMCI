{{* duty_plan_entry_frame.tpl  Common frame template for duty plan form 2004-06-26 Elpidio Latorilla *}}

<form name="schedule" id="schedule" {{$sFormAction}} method="post">
<div>
	<table id="schedule-table" border="0" cellspacing="2" cellpadding="2" width="50%" align="left">
		<tr>
			<td class="segPanelHeader" align="left" colspan="2"> {{$sPanelHeaderSchedule}} </td>
		</tr>
		<tr>
			<td class="segPanel" width="20%"> <strong>Batch No.</strong> </td>
			<td class="segPanel"> {{$sBatchNr}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Name</strong> </td>
			<td class="segPanel"> {{$sPatientName}} &nbsp; {{$sSelectBatchNr}} &nbsp; {{$sClearBatchNr}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Date Scheduled</strong> </td>
			<td class="segPanel"> {{$sDateScheduled}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Time Scheduled</strong> </td>
			<td class="segPanel"> {{$sTimeScheduled}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Instructions</strong> </td>
			<td class="segPanel"> {{$sInstructions}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Remarks</strong> </td>
			<td class="segPanel"> {{$sRemarks}} </td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				{{$sScheduleButton}} &nbsp;&nbsp; {{$sResetSchedule}}
			</td>
		</tr>
	</table>
</div>
{{if $sScheduledForTheDay}}
<div align="center" style="width:90%;">
	<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center" class="segList">
		<thead>
			<tr>
				<td class="segPanelHeader" align="left" colspan="9"> {{$sPanelHeaderScheduledForTheDay}}</td>
			</tr>
			<tr class="segPanel" style=" font-weight:bold;">
				<td align="center" width="2%">No.</td>
				<td align="center" width="8%">Batch No.</td>
				<td align="center" width="12%">Time</td>
				<td align="center" width="15%">Service Code</td>
				<td align="center" width="8%">RID</td>
				<td align="center" width="*">Patient's Name</td>
				<td align="center" width="12%">Scheduled By</td>
				<td align="center" width="10%" colspan="2">Options</td>
			</tr>
		</thead>
		<tbody>
			{{$sScheduledForTheDay}}
		</tbody>
	</table>
</div>
{{/if}}


<font size=4>
{{$LDMonth}} {{$sMonthSelect}} &nbsp; {{$LDYear}} {{$sYearSelect}}
</font>

<table border="0">
	<tbody>
		<tr>
			<td colspan="3" valign="top">
        
				<table border=0 cellpadding=0 cellspacing=1 width="100%" class="frame">
					<tbody>
{{if not $segDutyPlanRadiologyMode}}
						<tr class="submenu2_titlebar" style="font-size:16px">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td colspan="2">{{$LDStandbyPerson}}</td>
							<td colspan="2">{{$LDOnCall}}</td>
						</tr>
{{/if}}
						{{$sDutyRows}}
					</tbody>
				</table>
			</td>
			<td valign="top">
				{{$sSave}}
				<p>
				{{$sClose}}
			</td>
		</tr>
		<tr>
			<td colspan="3">{{$sSave}}&nbsp;&nbsp;&nbsp;{{$sClose}}</td>
			<td>&nbsp;</td>
		</tr>  
	</tbody>
</table>


{{$sHiddenInputs}}

</form>