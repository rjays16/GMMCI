<form name="schedule-form" id="schedule-form" {{$sFormAction}} method="post">
<!--
<div style="width:90%; overflow:hidden; border:1px solid red">
-->
	<table id="schedule-table" border="0" cellspacing="2" cellpadding="2" width="100%" align="left">
		<tr>
			<td class="segPanelHeader" align="left" colspan="2"> {{$sPanelHeaderSchedule}} </td>
		</tr>
		<tr>
			<td class="segPanel" width="10%"> <strong>Ref. No.</strong> </td>
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
			<td class="segPanel"> <strong>Name</strong> </td>
			<td class="segPanel"> {{$sPatientName}} &nbsp; </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Date Served</strong> </td>
			<td class="segPanel"> {{$sDateServed}} </td>
		</tr>
		<tr id="bloodrow">
		   <td class="segPanel"> <strong>Blood Products</strong> </td>
			<td class="segPanel"> 
				<table border="1" width="100%">
					<thead>
						<tr>
								<td width="1%">&nbsp;</td>
								<td width="20%" nowrap="nowrap"><b>Code</b></td>
								<td width="*"><b>Blood</b></td>
								<td width="10%"><b>Expiry Date</b></td>  
								<td width="10%"><b>Stocks</b></td>  
								<td width="10%"><b>Qty Used</b></td>
						</tr>
					</thead>
					<tbody>
						
						{{$sBloodItem}}
					</tbody>
				
				</table>
			</td>
		</tr>
		
		<tr>
			<td colspan="2" align="center">
				 {{$sDoneButton}}&nbsp; &nbsp; {{$sScheduleButton}} &nbsp;&nbsp; {{$sPrintButton}} 
			</td>
		</tr>
	</table>
	
<!--
</div>
-->
{{$sHiddenInputs}}
{{$sPresets}}
</form>