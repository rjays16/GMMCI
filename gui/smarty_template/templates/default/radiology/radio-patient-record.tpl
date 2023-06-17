{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}

	<table border="0" cellspacing="2" cellpadding="2" width="90%" align="center">
		<tbody>
			<tr>
				<td class="segPanelHeader" align="left" colspan="2"> {{$sPanelHeader}} </td>
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
	<table border="0" cellspacing="2" cellpadding="2" width="90%" align="center">
		<tr>
			<td>
				{{$sSearchInput}}
			</td>
		</tr>
	</table>
<br>
	{{$sTabRadiology}}
<br>
{{$sAvailabilityNotes}}
{{$sHiddenInputs}}
{{$jsCalendarSetup}}
{{$sIntialRequestList}}
<br/>
<img src="" vspace="2" width="1" height="1"><br>
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}} 	