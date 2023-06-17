{{* form.tpl  Form template for products module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}

{{$sFormStart}}
	<div style="padding:10px;width:95%;border:0px solid black">
	{{* NOTE:::  The following table  block must be inside the $sFormStart and $sFormEnd tags !!! *}}

	<!-- <font class="prompt">{{$sDeleteOK}}{{$sSaveFeedBack}}</font> -->
	<font class="warnprompt">{{$sMascotImg}} {{$sDeleteFailed}} {{$LDOrderNrExists}} <br> {{$sNoSave}}</font>
	<table border="0" cellspacing="1" cellpadding="3" style="" width="100%">
		<tbody class="submenu">
			<tr>
				<td align="right" width="140"><b>Select report</b></td>
				<td width="80%">{{$sReportSelect}}</td>
			</tr>
			<tr id="section" style="display:none">
				<td align="right" width="140"><b>Social Service Section</b></td>
				<td width="80%">{{$sReportSelectGroup}}</td>
			</tr>
			<tr id="social_worker" style="display:none">
				<td align="right" width="140"><b>Social Worker</b></td>
				<td width="80%">{{$sReportEncoder}}</td>
			</tr>
			<tr>
				<td align="right" width="140"><b>From</b></td>
				<td>{{$sFromDateHidden}}{{$sFromDateInput}}{{$sFromDateIcon}}</td>
			</tr>
			<tr>
				<td align="right" width="140"><b>To</b></td>
				<td>{{$sToDateHidden}}{{$sToDateInput}}{{$sToDateIcon}}</td>
			</tr>
			<!--<tr>
				<td align="right" width="140"><b>Classification</b></td>
				<td width="80%">{{$sReportSelectClassification}}</td>
			</tr>
			<tr>
				<td align=right width=140>{{$LDReset}}</td>
				<td align=right>{{$sUpdateButton}}</td>
			</tr>-->
		</tbody>
	</table>

	{{$sHiddenInputs}}

{{$jsCalendarSetup}}
{{$sTransactionDetailsControls}}
<br/>
<div style="float:left;">
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1%">{{$sContinueButton}}</td>
	</tr>
</table>
</div>


</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}