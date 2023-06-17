{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
{{$sFormStart}}
<div id="order_details" style="">
	<div align="center" style="width:90%">
		<table border="0" align="center" style="margin-bottom:2px" >
			<tr>
				<td width="1"><strong style="white-space:nowrap">Pharmacy area</strong></td>
				<td width="*">{{$sSelectArea}}{{$sHiddenArea}}</td>
			</tr>
		</table>
		<table border="0" cellspacing="2" cellpadding="1" align="center" width="70%">
			<tbody>
				<tr>
					<td class="segPanelHeader" width="60%">Return information</td>
					<td class="segPanelHeader">Return date</td>
				</tr>
				<tr>
					<td class="segPanel" nowrap="nowrap" rowspan="3" valign="top" style="padding:5px">
						<table border="0" cellpadding="1" cellspacing="0" style="font:bold 12px Arial">
							<tr>
								<td width="60" align="right"><strong>Control no.</strong></td>
								<td>
									{{$sReturnNr}}
									{{$sReturnNrReset}}
								</td>
							</tr>
							<tr>
								<td align="right" valign="top"><strong>Name</strong></td>
								<td width="1" valign="middle">
									{{$sReturnEncNr}}
									{{$sReturnEncID}}
									{{$sReturnDiscountID}}
									{{$sReturnDiscount}}
									{{$sReturnName}}
								</td>
								<td width="1" valign="middle">
									{{$sSelectEnc}}
								</td>
								<td valign="middle" style="display:none">
									{{$sClearEnc}}
								</td>
							</tr>
							<tr>
								<td valign="top" align="right"><strong>Address</strong></td>
								<td colspan="3">{{$sReturnAddress}}</td>
							</tr>
							<!--{{if $is_refund}}
							<tr>
								<td align="right"><strong>Refund<br />amount</strong></td>
								<td>
									{{$sRefundAmount}}
								</td>
							</tr>
							<tr>
								<td align="right"><strong>Adjusted <br />amount</strong></td>
								<td>
									<div style="margin-bottom:2px">
										{{$sCheckAdjust}}
									</div>
									{{$sAdjustAmount}}
								</td>
							</tr>
							{{/if}}-->
						</table>
					</td>
					<td class="segPanel" nowrap="nowrap" align="center">
						{{$sReturnDate}}{{$sCalendarIcon}}
					</td>
				</tr>
				<tr>
					<td class="segPanelHeader">Notes</td>
				</tr>
				<tr>
					<td class="segPanel" style="padding:5px" align="center">
						{{$sComments}}
					</td>
				</tr>
			</tbody>
		</table>
		<br />
		<table width="100%" border="0" cellpadding="2">
			<tr>
				<td width="50%" align="left">
					{{$sAddItem}}{{$sEmptyList}}
				</td>
				<td width="*" align="right">
					{{$sContinueButton}}{{$sBreakButton}}
				</td>
			</tr>
		</table>
		<table id="return-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%;">
			<thead>
				<tr id="return-list-header">
					<th width="10%" nowrap="nowrap" align="center">Ref No.</th>
					<th width="10%" nowrap="nowrap" align="center">Item No.</th>
					<th width="*" nowrap="nowrap" align="center">Item Description</th>
					<th width="10%" align="center" nowrap="nowrap">Qty</th>
					<th width="10%" align="center" nowrap="nowrap">Prev returns</th>
					<th width="10%" align="center" nowrap="nowrap">Price</th>
					<th width="10%" align="center" nowrap="nowrap">Returned</th>
					<th width="10%" align="center" nowrap="nowrap">Refundables</th>
					<th width="4%" nowrap="nowrap"></th>
				</tr>
			</thead>
			<tbody>
{{$sReturnItems}}
			</tbody>
		</table>
	</div>
</div>

{{$sHiddenInputs}}
{{$jsCalendarSetup}}
<br/>
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}