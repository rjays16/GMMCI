{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}

	<table border="0" cellspacing="2" cellpadding="2" width="90%" align="center">
		<tbody>
			<tr>
				<td class="segPanelHeader" align="left" colspan="2"> {{$sPanelHeader}}</td>
			</tr>
			<tr>
				<td class="segPanel" width="10%"> <strong>PID</strong> </td>
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
<!--
	<table id="borrow-table" border="0" cellspacing="2" cellpadding="2" width="90%" align="center">
		<tr>
			<td>
				<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center">
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
						<td class="segPanel"> {{$sCurrentFilmReleaser}} </td>
					</tr>
					{{if $sPreviousFilmReleaser}}
					<tr id="previousFilmReleaser">
						<td class="segPanel"> <strong>Previous Film Releaser</strong> </td>
						<td class="segPanel"> {{$sPreviousFilmReleaser}} </td>
					</tr>
				</table>
			</td>
			<td>
				{{$sBorrowButton}}
				{{$sUpdateBorrowButton}}
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" cellspacing="2" cellpadding="2" width="100%"align="center">
					<tr id="headerReturned">
						<td class="segPanelHeader" align="left" colspan="2"> {{$sPanelHeaderReturn}} </td>
					</tr>
					<tr id="dateReturned">
						<td class="segPanel"  width="20%"> <strong>Date Returned</strong> </td>
						<td class="segPanel"> {{$sDateReturned}} </td>
					</tr>
					<tr id="timeReturned">
						<td class="segPanel"> <strong>Time Returned</strong> </td>
						<td class="segPanel"> {{$sTimeReturned}} </td>
					</tr>
					<tr id="currentFilmReceiver">
						<td class="segPanel"> <strong>Film Receiver</strong> </td>
						<td class="segPanel"> {{$sCurrentFilmReceiver}} </td>
					</tr>
					{{if $sPreviousFilmReceiver}}
					<tr id="previousFilmReceiver">
						<td class="segPanel"> <strong>Previous Film Receiver</strong> </td>
						<td class="segPanel"> {{$sPreviousFilmReceiver}} </td>
					</tr>
					{{/if}}
					<tr>
						<td class="segPanel"> <strong>Remarks</strong> </td>
						<td class="segPanel"> {{$sRemarks}} </td>
					</tr>
				</table>
			</td>
			<td>
				{{$sReturnButton}}
				{{$sUpdateReturnButton}}
			</td>
		</tr>		
	</table>
<br>
-->
	<table id="borrow-table" border="0" cellspacing="2" cellpadding="2" width="90%" align="center">
		<tbody>
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
			<tr id="headerReturned">
				<td class="segPanelHeader" align="left" colspan="2"> {{$sPanelHeaderReturn}} </td>
			</tr>
			<tr id="dateReturned">
				<td class="segPanel"> <strong>Date Returned</strong> </td>
				<td class="segPanel"> {{$sDateReturned}} </td>
			</tr>
			<tr id="timeReturned">
				<td class="segPanel"> <strong>Time Returned</strong> </td>
				<td class="segPanel"> {{$sTimeReturned}} </td>
			</tr>
			<tr>
				<td class="segPanel"> <strong>Remarks</strong> </td>
				<td class="segPanel"> {{$sRemarks}} </td>
			</tr>
			<tr>
				<td class="segPanel"> <strong>Film Releaser</strong> </td>
				<td class="segPanel"> {{$sCurrentFilmReleaser}} </td>
			</tr>
			{{if $sPreviousFilmReleaser}}
			<tr id="previousFilmReleaser">
				<td class="segPanel"> <strong>Previous Film Releaser</strong> </td>
				<td class="segPanel"> {{$sPreviousFilmReleaser}} </td>
			</tr>
			{{/if}}
			<tr id="currentFilmReceiver">
				<td class="segPanel"> <strong>Film Receiver</strong> </td>
				<td class="segPanel"> {{$sCurrentFilmReceiver}} </td>
			</tr>
			{{if $sPreviousFilmReceiver}}
			<tr id="previousFilmReceiver">
				<td class="segPanel"> <strong>Previous Film Receiver</strong> </td>
				<td class="segPanel"> {{$sPreviousFilmReceiver}} </td>
			</tr>
			{{/if}}
		</tbody>
	</table>
	
	
{{$sHiddenInputs}}
{{$jsCalendarSetup}}
<img src="" vspace="2" width="1" height="1">
	
<hr/>
{{$sBorrowButton}}
{{$sUpdateBorrowButton}}
{{$sReturnButton}}
{{$sUpdateReturnButton}}
{{$sDoneButton}}
{{$sIntialRequestList}}
{{$sFormEnd}}