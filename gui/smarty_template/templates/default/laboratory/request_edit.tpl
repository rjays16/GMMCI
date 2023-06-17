{{* form.tpl  Form template for products module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}

{{$sFormStart}}
	<div style="padding:10px;width:95%;border:0px solid black">
	{{* NOTE:::  The following table  block must be inside the $sFormStart and $sFormEnd tags !!! *}}
	<font class="warnprompt">{{$sTitle}}</font><br>
	<!-- <font class="prompt">{{$sDeleteOK}}{{$sSaveFeedBack}}</font> -->
	<font class="warnprompt">{{$sMascotImg}} {{$sDeleteFailed}} {{$LDOrderNrExists}} <br> {{$sNoSave}}</font>
	<table border="0" cellspacing="1" cellpadding="3" style="" width="100%">
		<tbody class="submenu">
			<tr>
				<td align="right" width="140"><b>Reference no.</b></td>
				<td width="80%">{{$sReferenceNoInput}}</td>
			</tr>
			<tr>
				<td align="right" width="140"><b>Request Date</b></td>
				<td>{{$sPurchaseDateInput}}&nbsp;{{$sCalendarIcon}}</td>
			</tr>
			<tr>
				<td align="right" width="140"><b>{{$sPatientName}}</b></td>
				<td>
					{{$sPayerID}}
					{{$sPayerNameInput}}&nbsp;{{$sPayerSelectButton}}
				</td>
			</tr>
			<tr>
				<td align="right" width="140" ><b>Payment type</b></td>
				<td>
					{{$sIsCashCheckBox}}
				</td>
			</tr>
			<!--
			<tr>
				<td align="right" width="140" ><b>Discount(Percentage)</b></td>
				<td>
					{{$sDiscount}}
				</td>
			</tr>
			-->
			<tr>
				<td align="right" width="140"><b>{{$sLabGroup}}</b></td>
				<td style="font-weight:normal">
					{{$sGroupID}}
					{{$sGrpNameInput}}&nbsp;{{$sParamGroupSelect}}
				</td>
			</tr>
			<tr>
				<td align=right width=140>{{$LDReset}}</td>
				<td align=right>{{$sUpdateButton}}</td>
			</tr>
		</tbody>
	</table>
	
	{{$sHiddenInputs}}
	
{{$jsCalendarSetup}}

{{$sTransactionDetailsControls}}

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sFilter}}

<!--
<br>
	<div id="listcontainer" align="center">
		<span>
			{{$sFilter}}
			<br>Selected:<span id="selectedcount">0</span>
			
		</span>
						
		<table id="srcRowsTable" style="margin-botton:5px" width="85%" border="0" cellpadding="0" cellspacing="0">
			
  		</table>
	</div><br>
-->
<br>

<img src="" vspace="2" width="1" height="1"><br/>
{{$sDiscountControls2}}
{{$sDiscountControls}}

<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>
<img src="" vspace="1" width="1" height="1"><br/>
<div style="float:left;">

<table  border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="2">&nbsp;</td>
		<td>&nbsp;</td>
		<td width="1%">{{$sViewPDF}}</td>
	</tr>
</table>

</div>
<div style="float:left;">
<table border="0" cellpadding="0" cellspacing="0">
	
	<tr>
		<td width="1%">{{$sContinueButton}}</td>
		<td width="2">&nbsp;</td>
		<td>{{$sBreakButton}}</td>
	</tr>
	
</table>
</div>


</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}