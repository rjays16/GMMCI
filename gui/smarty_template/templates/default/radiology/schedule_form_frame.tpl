{{* duty_plan_entry_frame.tpl  Common frame template for duty plan form 2004-06-26 Elpidio Latorilla *}}

<form name="schedule-form" id="schedule-form" {{$sFormAction}} method="post">
<!--
<div style="width:90%; overflow:hidden; border:1px solid red">
-->
	<table id="schedule-table" border="0" cellspacing="2" cellpadding="2" width="100%" align="left">
		<tr>
			<td class="segPanelHeader" align="left" colspan="2"> {{$sPanelHeaderSchedule}} </td>
		</tr>
		<tr>
			<td class="segPanel" width="25%"> <strong>Ref. No.</strong> </td>
			<td class="segPanel"> {{$sBatchNr}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Service Code</strong> </td>
			<td class="segPanel"> {{$sServiceCode}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Name</strong> </td>
			<td class="segPanel"> {{$sPatientName}} &nbsp; </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Date Scheduled</strong> </td>
			<td class="segPanel"> {{$sDateScheduled}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Time Scheduled</strong> </td>
			<td class="segPanel"> {{$sTimeScheduled}} </td>
		</tr>
		{{if sServiceDate}}
		<tr>
			<td class="segPanel"> <strong>Date of Service</strong> </td>
			<td class="segPanel"> {{$sServiceDate}} </td>
		</tr>
		{{/if}}
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
				{{$sScheduleButton}} &nbsp;&nbsp; {{$sPrintButton}} 
			</td>
		</tr>
	</table>
<!--
</div>
-->
{{$sHiddenInputs}}
{{$sPresets}}
</form>