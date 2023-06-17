{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}

	<table border="0" cellspacing="2" cellpadding="2" width="90%" align="center">
		<tbody>
			<tr>
				<td class="segPanelHeader" align="left" colspan="2"> {{$sPanelHeader}}</td>
			</tr>
			<tr>
				<td class="segPanel" width="10%"> <strong>HRN</strong> </td>
				<td class="segPanel"> {{$sPID}} </td>
			</tr>
			<tr>
				<td class="segPanel"> <strong>RID</strong> </td>
				<td class="segPanel"> {{$sRID}} </td>
			</tr>
			<tr>
				<td class="segPanel"> <strong>Name</strong> </td>
				<td class="segPanel"> {{$sName}} </td>
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
	<table id="borrow-table" border="0" cellspacing="2" cellpadding="2" width="90%" align="center">
		<tr>
			<td class="segPanelHeader" align="left" colspan="2"> {{$sPanelHeaderBorrow}} </td>
		</tr>
		<tr>
			<td class="segPanel" width="20%"> <strong>Batch No.</strong> </td>
			<td class="segPanel"> {{$sBatchNr}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Borrower</strong> </td>
			<td class="segPanel"> {{$sBorrower}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Date Borrowed</strong> </td>
			<td class="segPanel"> {{$sDateBorrowed}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Time Borrowed</strong> </td>
			<td class="segPanel"> {{$sTimeBorrowed}} </td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Film Releaser</strong> </td>
			<td class="segPanel"> 
				{{$sFilmReleaser}} 
				{{$sNewFilmReleaser}}
			</td>
		</tr>
		<tr>
			<td class="segPanel"> <strong>Remarks</strong> </td>
			<td class="segPanel"> {{$sRemarks}} </td>
		</tr>
		<tr>
			<td colspan="2">
				{{$sBorrowButton}}
				{{$sUpdateBorrowButton}}						
			</td>
		</tr>
		<tr id="headerReturned">
			<td class="segPanelHeader" align="left" colspan="2"> {{$sPanelHeaderReturn}} </td>
		</tr>
		<tr id="penaltyRow">
			<td class="segPanel" width="20%"> <strong>Penalty</strong> </td>
			<td class="segPanel"> Php {{$sPenalty}} </td>
		</tr>
		<tr id="dateReturned">
			<td class="segPanel" width="20%"> <strong>Date Returned</strong> </td>
			<td class="segPanel"> {{$sDateReturned}} </td>
		</tr>
		<tr id="timeReturned">
			<td class="segPanel"> <strong>Time Returned</strong> </td>
			<td class="segPanel"> {{$sTimeReturned}} </td>
		</tr>
		<tr id="filmReceiver">
			<td class="segPanel"> <strong>Film Receiver</strong> </td>
			<td class="segPanel"> 
				{{$sFilmReceiver}} 
				{{$sNewFilmReceiver}}
			</td>
		</tr>
		<tr>
			<td colspan="2">
				{{$sReturnButton}}
				{{$sUpdateReturnButton}}
			</td>
		</tr>
	</table>
		
{{$sHiddenInputs}}
{{$jsCalendarSetup}}
	<hr id="doneButtonHr">
{{$sDoneButton}}
{{$sIntialRequestList}}
{{$sFormEnd}}

{{if $sRecordHistory}}
<div align="center" style="width:100%;">
	<hr>
	<table class="segList" border="0" cellspacing="2" cellpadding="2" width="100%" align="center">
		<thead>
			<tr>
				<td class="segPanelHeader" align="left" colspan="6"> {{$sPanelHeaderRecordHistory}}</td>
			</tr>
			<tr class="segPanel">
				<td align="center">Borrower's Name</td>
				<td align="center">Date Borrowed</td>
				<td align="center">Releaser's Name</td>
				<td align="center">Date Returned</td>
				<td align="center">Receiver's Name</td>
				<td align="center">Remarks</td>
			</tr>
		</thead>
		<tbody>
			{{$sRecordHistory}}
		</tbody>
	</table>
</div>
{{/if}}
<br><br><br>