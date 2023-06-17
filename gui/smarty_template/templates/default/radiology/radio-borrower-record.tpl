{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}

	<table border="0" cellspacing="2" cellpadding="2" width="90%" align="center">
		<tbody>
			<tr>
				<td class="segPanelHeader" align="left" colspan="2" style="font-size:16px;"> 
					{{$sPanelHeader}} 
				</td>
			</tr>
<!--
			<tr>
				<td class="segPanel" width="10%"> <strong>PID</strong> </td>
				<td class="segPanel"> {{$sPID}} </td>
			</tr>
-->
			<tr>
				<td class="segPanel" width="10%"> <strong>Personnel ID</strong> </td>
				<td class="segPanel"> {{$sPersonnelID}} </td>
			</tr>
			<tr>
				<td class="segPanel" width="10%"> <strong>Department</strong> </td>
				<td class="segPanel"> {{$sDeptName}} </td>
			</tr>
			<tr>
				<td class="segPanel"> <strong>Birthdate</strong> </td>
				<td class="segPanel"> {{$sBirthdate}}&nbsp;&nbsp;&nbsp;{{$sAge}} </td>
			</tr>
			<tr>
				<td class="segPanel"> <strong>Gender</strong> </td>
				<td class="segPanel"> {{$sGender}} </td>
			</tr>
			<tr>
				<td class="segPanel"> <strong>Address</strong> </td>
				<td class="segPanel"> {{$sAddress}} </td>
			</tr>
		</tbody>
	</table>
<br>
<div align="left" style="width:90%;">
{{$sPrintIcon}}
</div>
<br>
{{if $sBorrowRecordHistory}}
<div align="center" style="width:90%;">
	<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center">
		<thead>
			<tr>
				<td class="segPanelHeader" align="left" colspan="8"> {{$sPanelHeadersBorrowRecordHistory}}</td>
			</tr>
			<tr class="segPanel" style=" font-weight:bold;">
				<td align="center" width="2%">No.</td>
				<td align="center" width="12%">RID</td>
				<td align="center" width="12%">Batch No.</td>
				<td align="center" width="12%">Film No.</td>
				<td align="center" width="12%">Date Borrowed</td>
				<td align="center" width="*">Patient's Name</td>
				<td align="center" width="15%">Short Desc</td>
				<td align="center" width="12%">Gross Price</td>
			</tr>
		</thead>
		<tbody>
			{{$sBorrowRecordHistory}}
		</tbody>
	</table>
<hr>
	<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center">
		<tr class="segPanel" style="font:'Courier New', Courier, mono;font-size:14px;font-weight:bold;">
			<td align="right" width="*">TOTAL</td>
			<td align="right" width="12%">{{$sTotalGrossPrice}}</td>
		</tr>
		<tr>
			<td colspan="2"></td>
		</tr>
		<tr class="segPanel" style="font:'Courier New', Courier, mono;font-size:14px;font-weight:bold;">
			<td align="right" width="*">PENALTY (30% of {{$sTotalGrossPrice}})</td>
			<td align="right" width="12%"><strong>{{$sPenalty}}</strong></td>
		</tr>
	</table>
</div>
{{/if}}
{{$sHiddenInputs}}
