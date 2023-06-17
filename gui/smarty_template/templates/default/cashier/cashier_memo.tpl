{{* cashier_memo.tpl  Form template for Cashier module *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

<style type="text/css">
.tabFrame {
	padding:5px;
}
</style>

{{$sFormStart}}
<div style="width:75%">
	<div class="segPanel" style="padding:1px; width:100%">
		<div id="tab0" class="tabFrame" style="display:block" >
			<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
				<tbody>
					<tr>
						<td width="35%" valign="top">
							<table border="0" cellspacing="1" cellpadding="1" width="100%" style="font-family:Arial, Helvetica, sans-serif">
								<tr valign="top">
									<td nowrap="nowrap" align="right"><strong>Memo Nr</strong></td>
									<td nowrap="nowrap">
										{{$sMemoNr}}
										{{$sResetNr}}
									</td>
								</tr>
                                                                <tr valign="top">
                                                                    <td nowrap="nowrap" align="right">
                                                                    </td>
                                                                    <td nowrap="nowrap">
                                                                         {{$sORWalkin}}
                                                                        <label class="segInput" for="search-walkin">Search walk-in</label>
                                                                    </td>
                                                                </tr>
								<tr valign="top">
									<td width="1%" nowrap="nowrap" align="right"><strong>Name</strong></td>
									<td align="left" valign="middle" nowrap="nowrap">
										{{$sMemoEncNr}}
										{{$sMemoEncID}}
										{{$sMemoDiscountID}}
										{{$sMemoDiscount}}
										{{$sMemoName}}
									</td>
									<td>{{$sSelectEnc}}</td>
									<td>{{$sClearEnc}}</td>
								</tr>
								<tr valign="top">
									<td width="1%" nowrap="nowrap" align="right" rowspan="2"><strong>Address</strong></td>
									<td align="left" valign="middle" colspan="3">
										{{$sMemoAddress}}
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
									<td width="1%" nowrap="nowrap" align="right"><strong>Date</strong></td>
									<td width="*" align="left" valign="middle">
										{{$sIssueDate}}{{$sCalendarIcon}}
									</td>
								</tr>
								<tr valign="top">
									<td nowrap="nowrap" align="right"><strong>Notes</strong></td>
									<td>
										{{$sRemarks}}
									</td>
								</tr>
								<tr valign="middle">
									<td align="right"><strong>Assign to</strong></td>
									<td colspan="4">
										{{$sPersonnel}}
									</td>
								</tr>
								<tr valign="middle">
									<td nowrap="nowrap" align="right"><strong>Total refund</strong></td>
									<td>
										{{$sTotalRefund}}
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
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="50%" align="left">{{$sMemoAdd}}{{$sMemoClearAll}}</td>
				<td align="right">{{$sContinueButton}}{{$sBreakButton}}</td>
			</tr>
		</table>
		<table id="memo-list" class="jedList" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-top:5px">
			<thead>
				<tr id="">
					<th align="center" width="8%" nowrap="nowrap">OR No.</th>
					<th align="center" width="8%" nowrap="nowrap">Source</th>
					<th align="center" width="8%" nowrap="nowrap">Req No</th>
					<th align="center" width="8%" nowrap="nowrap">Code</th>
					<th align="center" width="*" nowrap="nowrap">Item description</th>
					<th align="center" width="9%" nowrap="nowrap">Quantity</th>
					<th align="center" width="9%" nowrap="nowrap">Previous</th>
					<th align="center" width="9%" nowrap="nowrap" style="font:bold 11px Tahoma">Price/item</th>
					<th align="center" width="9%" nowrap="nowrap">Refund</th>
					<th align="center" width="9%" nowrap="nowrap" >Total</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
{{$sMemoList}}
			</tbody>
		</table>
	</div>
</div>

{{$sHiddenInputs}}
{{$jsCalendarSetup}}
<br/>
<img src="" vspace="2" width="1" height="1"><br/>
{{$sDiscountControls}}
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>

<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}} 	
