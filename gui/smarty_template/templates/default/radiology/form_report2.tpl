{{* form.tpl  Form template for products module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}

{{$sFormStart}}
	<div style="padding:10px;width:95%;border:0px solid black">
	{{* NOTE:::  The following table  block must be inside the $sFormStart and $sFormEnd tags !!! *}}

	<!-- <font class="prompt">{{$sDeleteOK}}{{$sSaveFeedBack}}</font> -->
	<font class="warnprompt">{{$sMascotImg}} {{$sDeleteFailed}} {{$LDOrderNrExists}} <br> {{$sNoSave}}</font>
	<table border="0" cellspacing="1" cellpadding="3" style="" width="100%">
		<tbody class="submenu">
			<tr id="mode_stat">
				<td colspan="2">
					<table id="" border="0" cellspacing="1" cellpadding="3" style="" width="100%">
						<tbody class="submenu">
							<tr>
								<td colspan="2"><b>Statistics Report</b></td>
							</tr>
							<tr>
								<td align="right" width="140"><b>From</b></td>
								<td  width="80%">{{$sFromDateHidden}}{{$sFromDateInput}}{{$sFromDateIcon}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
							<tr>
								<td align="right" width="140"><b>To</b></td>
								<td  width="80%">{{$sToDateHidden}}{{$sToDateInput}}{{$sToDateIcon}}&nbsp;&nbsp;(YYYY-MM-DD)</td>
							</tr>
						</tbody>
					</table>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sStatButton}}</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	{{$sHiddenInputs}}
	
{{$jsCalendarSetup}}
{{$sTransactionDetailsControls}}
<br/>
<!--
<div style="float:left;">
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sContinueButton}}</td>
		<td width="80%">&nbsp;&nbsp;{{$sStatButton}}</td>
	</tr>
</table>
</div>
-->

</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}