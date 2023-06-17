{{* cashier_memo.tpl  Form template for Cashier module *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

<style type="text/css">
<!--
	.tabFrame {
		padding:5px;
	}

-->
</style>

{{$sFormStart}}
<div style="width:550px">
	<div class="segTabPanel" style="padding:1px; width:100%">
		<div id="tab0" class="tabFrame" style="display:block" >
			<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
				<tbody>
					<tr>
						<td width="35%" valign="top">
							<table border="0" cellspacing="1" cellpadding="1" width="100%" style="font-family:Arial, Helvetica, sans-serif">
								<tr valign="top">
									<td nowrap="nowrap" align="right">
										<strong>Ref No.</strong>
									</td>
									<td nowrap="nowrap" align="left">
										{{$sRefNo}}
									</td>
								</tr>
								<tr valign="top">
									<td width="1%" nowrap="nowrap" align="right"><strong>Name</strong></td>
									<td align="left" valign="middle" nowrap="nowrap">
										{{$sVoucherEncNr}}
										{{$sVoucherEncID}}
										{{$sVoucherDiscountID}}
										{{$sVoucherDiscount}}
										{{$sVoucherName}}
									</td>
									<td></td>
									<td></td>
								</tr>
								<tr valign="top">
									<td width="1%" nowrap="nowrap" align="right" rowspan="2"><strong>Address</strong></td>
									<td align="left" valign="middle" colspan="3">
										{{$sVoucherAddress}}
									</td>
								</tr>
								<tr valign="top">
									<td align="left" valign="middle" colspan="4">
										{{$sSWClass}}
									</td>
								</tr>
							</table>
						</td>
						<td width="*" valign="top">
							<table border="0" cellspacing="2" cellpadding="1" width="100%" style="font-family:Arial, Helvetica, sans-serif">
								<tr valign="top">
									<td width="1%" nowrap="nowrap" align="right"><strong>Entry Date</strong></td>
									<td width="*" align="left" valign="middle">
										{{$sEntryDate}}{{$sCalendarIcon}}
									</td>
								</tr>
								<tr valign="top">
									<td nowrap="nowrap" align="right"><strong>Remarks</strong></td>
									<td>
										{{$sRemarks}}
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div style="width:90%;" align="center">
	<div id="" style="padding:2px;margin-top:3px;">
		<table width="100%" border="0" cellspacing="0" cellpadding="2">
			<tr>
				<td width="45%" align="left">{{$sAddCoverage}}{{$sClearCoverage}}</td>
				<td width="*" align="right">{{$sContinueButton}}{{$sBreakButton}}</td>
			</tr>
			<tr>
				<td valign="top">
					<table id="sponsor-list" class="jedList" border="0" cellpadding="0" cellspacing="0" style="width:100%">
						<thead>
							<tr id="">
								<th align="center" width="8%" nowrap="nowrap">Ctrl No.</th>
								<th align="center" width="*" nowrap="nowrap">Sponsor</th>
								<th align="center" width="25%" nowrap="nowrap">Amount</th>
								<th width="1"></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="2" align="right">Total coverage</th>
								<th align="right" id="total-coverage">0.00</th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</td>
				<td valign="top">
					<table id="memo-list" class="jedList" border="0" cellpadding="0" cellspacing="0" style="width:100%">
						<thead>
							<tr id="">
								<th align="center" width="8%" nowrap="nowrap">Code</th>
								<th align="center" width="*" nowrap="nowrap">Item Description</th>
								<th align="center" width="9%" nowrap="nowrap">Quantity</th>
								<th align="center" width="9%" nowrap="nowrap">Price/item</th>
								<th align="center" width="9%" nowrap="nowrap" >Total</th>
							</tr>
						</thead>
						<tbody>
{{$sMemoList}}
						</tbody>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>

{{$sHiddenInputs}}
{{$jsCalendarSetup}}
<br/>
<img src="" vspace="2" width="1" height="1"><br/>
{{$sDiscountControls}}
{{$sSponsorTemplate}}
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>

<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}