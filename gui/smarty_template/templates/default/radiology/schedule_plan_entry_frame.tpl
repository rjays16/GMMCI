{{* duty_plan_entry_frame.tpl  Common frame template for duty plan form 2004-06-26 Elpidio Latorilla *}}

<form name="schedule" id="schedule" {{$sFormAction}} method="post">
<!--
<div style="width:90%; overflow:hidden; border:1px solid red">
-->
	<table id="schedule-table" border="0" cellspacing="2" cellpadding="2" width="50%" align="left">
		<tr>
			<td class="segPanelHeader" align="left" colspan="2"> {{$sPanelHeaderSchedule}} </td>
		</tr>
		<tr>
			<td class="segPanel" width="40%"> <strong>Ref. No.</strong> </td>
			<td class="segPanel"> {{$sBatchNr}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Service Code</strong> </td>
			<td class="segPanel"> {{$sServiceCode}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Name</strong> </td>
			<td class="segPanel"> {{$sPatientName}} &nbsp; {{$sSelectBatchNr}} &nbsp; {{$sClearBatchNr}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Patient Type</strong> </td>
			<td class="segPanel"> {{$sPatientType}} </td>
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
			<td class="segPanel"> <strong>Please bring the following</strong> </td>
			<td class="segPanel"> {{$sInstructions}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Instructions</strong> </td>
			<td class="segPanel"> {{$sRemarks}} </td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				{{$sScheduleButton}} &nbsp;&nbsp; {{$sResetSchedule}}
			</td>
		</tr>
	</table>
<!--
</div>
-->
<br style="clear:left ">
<br style="clear:left ">
<br style="clear:left ">
<!--
<div align="center" style="width:90%; border:1px solid red">
-->
	<table border="0" id="scheduled-list" cellspacing="2" cellpadding="2" width="75%" align="left" class="segList">
		<thead>
			<tr>
				<td class="segPanelHeader" align="left" colspan="9"> {{$sPanelHeaderScheduledForTheDay}}</td>
			</tr>
			<tr class="segPanel" style=" font-weight:bold;">
				<td align="center" width="2%">No.</td>
				<td align="center" width="8%">Ref. No.</td>
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
		
	</table>
<!--
</div>
-->


{{$sHiddenInputs}}
{{$sPresets}}
</form>