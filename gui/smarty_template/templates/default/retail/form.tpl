{{* form.tpl  Form template for products module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}

{{$sFormStart}}

	<table border="0" cellspacing="1" cellpadding="1" style="" width="400" align="center">
		<tbody class="submenu">
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td align="right" width="150"><b>Reference no.&nbsp;</b></td>
				<td width="*">{{$sReferenceNoInput}}</td>
			</tr>
			<tr>
				<td align="right"><b>Purchase date&nbsp;</b></td>
				<td>{{$sPurchaseDateInput}}&nbsp;{{$sCalendarIcon}}</td>
			</tr>
			<tr>
				<td align="right"><b>Select payer&nbsp;</b></td>
				<td>
					{{$sPayerID}}
					{{$sPayerNameInput}}<br />
					{{$sPayerSelectButton}}
				</td>
			</tr>
			<tr>
				<td align="right"><b>Payment type&nbsp;</b></td>
				<td>
				{{$sIsCashCheckBox}}
				</td>
			</tr>
			<tr>
				<td align="right">{{$LDReset}}</td>
				<td align="right">{{$sUpdateButton}}</td>
			</tr>
		</tbody>
	</table>		

	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="1%">{{$sContinueButton}}</td>
			<td width="2">&nbsp;</td>
		<td>{{$sBreakButton}}</td>
		</tr>
	</table>

{{$sHiddenInputs}}
{{$jsCalendarSetup}}
{{$sTransactionDetailsControls}}
<br/>
<img src="" vspace="2" width="1" height="1"><br/>
{{$sDiscountControls}}
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>


</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}