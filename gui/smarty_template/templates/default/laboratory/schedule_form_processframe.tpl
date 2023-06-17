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
			<td class="segPanel" width="22%"> <strong>Ref. No.</strong> </td>
			<td class="segPanel"> {{$sBatchNr}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Service Name</strong> </td>
			<td class="segPanel"> {{$sServiceName}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Service Code</strong> </td>
			<td class="segPanel"> {{$sServiceCode}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Section Name</strong> </td>
			<td class="segPanel"> {{$sAreaName}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Name</strong> </td>
			<td class="segPanel"> {{$sPatientName}} &nbsp; </td>
		</tr>
		
		<tr>
			<td class="segPanel"> <strong>Reagents</strong> </td>
			<td class="segPanel"> 
				<table border="1">
					<thead>
						<tr>
							<td width="*"><b>Reagent</b></td>
							<td width="20%"><b>Amount Used</b></td>
							<td width="10%"><b>Unit</b></td>
							<td width="10%"><b>Per Pc?</b></td>
						</tr>
					</thead>
					{{$sReagentUsed}}
		
				</table>
			</td>
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