{{* form.tpl  Form template for orders module (pharmacy) *}}
{{$sFormStart}}
	<div align="center" style="margin-bottom:5px">
		<table border="0">
			<tr>
				<td width="1"><strong style="white-space:nowrap">Pharmacy area</strong></td>
				<td width="5"></td>
				<td width="*">{{$sSelectArea}}</td>
			</tr>
		</table>
	</div>
	<div align="center" style="width:90%">
		<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center">
			<tbody>
				<tr>
					<td class="segPanelHeader" width="*">
						Order Details
					</td>
					<td class="segPanelHeader" width="170">
						Reference No.
					</td>
					<td class="segPanelHeader" width="220">
						Order Date
					</td>
				</tr>
				<tr>
					<td rowspan="3" class="segPanel" align="center" valign="top">
						<table width="95%" border="0" cellpadding="1" cellspacing="0" style="font:normal 12px Arial" >
							<tr>
								<td align="left" nowrap="nowrap" style="padding-left:20px">
									<strong>Transaction type</strong>
									{{$sIsCash}}
									{{$sIsCharge}}
									{{$sChargeType}}
								</td>
								<td align="center" nowrap="nowrap"  style="display:none">
									{{$sIsTPL}}
								</td>
							</tr>
						</table>
						<table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px; font:normal 12px Arial" >
							<tr>
								<td align="right" valign="top"><strong>Name</strong></td>
								<td width="1" valign="middle">
									{{$sOrderEncNr}}
									{{$sOrderEncID}}
									{{$sOrderDiscountID}}
									{{$sOrderDiscount}}
									{{$sOrderName}}
								</td>
								<td width="1" valign="middle">
									{{$sSelectEnc}}
									{{$sClearEnc}}
								</td>
								<td valign="middle">
								</td>
							</tr>
							<tr>
								<td valign="top"><strong>Address</strong></td>
								<td colspan="3">{{$sOrderAddress}}</td>
							</tr>
						</table>
					</td>
					<td class="segPanel" align="center" nowrap="nowrap">
						{{$sRefNo}}
						{{$sResetRefNo}}
					</td>
					<td class="segPanel" align="center" valign="middle" nowrap="nowrap">
						{{$sOrderDate}}{{$sCalendarIcon}}
					</td>
				</tr>
				<tr>
					<td class="segPanelHeader">Discounts</td>
					<td class="segPanelHeader">Order options</td>
				</tr>
				<tr>
					<td class="segPanel" align="center">
						<table>
							<tr>
								<td valign="middle">
									<div style="font:bold 12px Arial"><strong>Classification: </strong><span id="sw-class" style="font:bold 12px Arial;color:#006633">{{$sSWClass}}</span></div>
									<div style="margin-top:5px; vertical-align:middle">{{$sDiscountShow}}</div>
								</td>
								<td>{{$sDiscountInfo}}</td>
							</tr>
						</table>
						{{$sBtnDiscounts}}
					</td>
					<td class="segPanel" align="center" style="padding-bottom:5px">
						<table border="0" cellpadding"0" cellspacing="0" style="font:normal 11.5px Arial;">
							<tr>
								<td align="right">
									<strong>Priority</strong>
								</td>
								<td>
									{{$sNormalPriority}}
									{{$sUrgentPriority}}
								</td>
							</tr>
							<tr>
								<td align="right" valign="top">
									<strong>Notes</strong>
								</td>
								<td>
									{{$sComments}}
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		</table>

		<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top:2px">
			<thead>
				<tr id="order-list-header">
					<th width="10%" nowrap="nowrap" align="center">Item No.</th>
					<th width="*" nowrap="nowrap" align="left">Item Description</th>
					<th width="10%" align="center" nowrap="nowrap">Price</th>
					<th width="6%" align="center" nowrap="nowrap">Qty</th>
					<th width="10%" align="center" nowrap="nowrap">Total</th>
					<th width="10%" align="center" nowrap="nowrap">Status</th>
					<th width="10%" align="center" nowrap="nowrap">Served?<input type="checkbox" id="serve_all" name="serve_all" onclick="setServeStatus();"></th>
					<th width="10%" align="center" nowrap="nowrap">Used?</th>
					<th width="20%" align="center" nowrap="nowrap">Write note</th>
				</tr>
			</thead>
			<tbody>
{{$sOrderItems}}
			</tbody>
		</table>
		
		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
			<tr>
				<tr>
					<td width="*" align="right" style="background-color:#ffffff; padding:4px"><strong>Net Total</strong></th>
					<td width="17%" id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold">{{$sTotalPrice}}</th>
				</tr>

			</tr>
		</table>
	</div>


{{$sHiddenInputs}}
{{$jsCalendarSetup}}
<br/>
<img src="" vspace="2" width="1" height="1"><br/>
{{$sDiscountControls}}
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>

<div style="width:80%">
{{$sUpdateControlsHorizRule}}
{{$sUpdateOrder}}
{{$sCancelUpdate}}
{{$sContinueButton}}
{{$sBreakButton}}
</div>

<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}} 	
