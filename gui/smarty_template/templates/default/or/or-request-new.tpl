{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}

	<table border="0" cellspacing="2" cellpadding="2" width="80%" align="center">
		<tbody>
			<tr>
				<td class="jedPanelHeader" width="*">
					Order Details
				</td>
				<td class="jedPanelHeader" width="170">
					Reference No.
				</td>
				<td class="jedPanelHeader" width="220">
					Order Date
				</td>
			</tr>
			<tr>
				<td rowspan="3" class="jedPanel" align="center" valign="top">
					<table width="95%" border="0" cellpadding="1" cellspacing="0" style="font:normal 12px Arial" >
						<tr>
							<td><strong>Transaction type</strong></td>
							<td align="left">
								{{$sIsCash}}
								{{$sIsCharge}}
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
							</td>
							<td valign="middle">
								{{$sClearEnc}}
							</td>
						</tr>
						<tr>
							<td valign="top"><strong>Address</strong></td>
							<td colspan="3">{{$sOrderAddress}}</td>
						</tr>
					</table>
				</td>
				<td class="jedPanel" align="center">
					{{$sRefNo}}
					{{$sResetRefNo}}
				</td>
				<td class="jedPanel" align="center" valign="middle">
					{{$sOrderDate}}
					<strong style="font-size:10px">mm/dd/yyyy</strong>
				</td>
			</tr>
			<tr>
				<td class="jedPanelHeader">Discounts</td>
				<td class="jedPanelHeader">Order options</td>
			</tr>
			<tr>
				<td class="jedPanel" align="center">
					<table>
						<tr>
							<td valign="middle">
								<div style=""><strong>Classification: </strong><span id="sw-class" style="font:bold 14px Arial;color:#006633">{{$sSWClass}}</span></div>
								<div style="margin-top:5px; vertical-align:middle">{{$sDiscountShow}}</div>
								<!-- <input id="show-discount" type="text" style="color:#006600; font-family:Arial; font-size:15px; font-weight:bold; text-align:right" readonly="1" size="5" value="0.00"/> -->
							</td>
							<td>{{$sDiscountInfo}}</td>
						</tr>
					</table>
					{{$sBtnDiscounts}}
				</td>
				<td class="jedPanel" align="center" style="padding-bottom:5px">
					<div style="padding-left:5px">
						<strong>Priority</strong>
						{{$sNormalPriority}}
						{{$sUrgentPriority}}
					</div>
					<div style="padding-left:5px; margin-bottom:4px; vertical-align:middle">
						<strong style="float:left; margin-top:10px">Comments </strong>
						{{$sComments}}
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<br />
	<table border="0" cellspacing="0" cellpadding="2" width="80%" align="center" style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
		<tbody>
			<tr>
				<td></td>
			</tr>
		</tbody>	
	</table>
	
<!--	<div align="left" style="width:80%">
		<table width="100%">
			<tr>
				<td width="50%" align="left">
					{{$sBtnAddItem}}
					{{$sBtnEmptyList}}
					{{$sBtnPDF}}
				</td>
				<td align="right">
					{{$sContinueButton}}
					{{$sBreakButton}}
				</td>
			</tr>
		</table>
		<table id="order-list" class="jedList" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr id="order-list-header">
					<th width="4%" nowrap="nowrap">&nbsp;</th>
					<th width="10%" nowrap="nowrap" align="left">Item No.</th>
					<th width="*" nowrap="nowrap" align="left">Item Description</th>
					<th width="4%" nowrap="nowrap" align="center">Consigned</th>
					<th width="10%" align="right" nowrap="nowrap">Qty</th>
					<th width="10%" align="right" nowrap="nowrap">Price(Orig)</th>
					<th width="10%" align="right" nowrap="nowrap">Price(Adj)</th>
					<th width="10%" align="right" nowrap="nowrap">Acc. Total</th>
				</tr>
			</thead>
			<tbody>
{{$sOrderItems}}
			
		</table>
		
		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
			<tr>
				<tr>
					<td width="*" align="right" style="background-color:#ffffff; padding:4px" height=""><strong>Sub-Total</strong>
					<td id="show-sub-total" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold">
				</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Discount</strong>
					<td id="show-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold">
				</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Net Total</strong>
					<td id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold">
				</tr>

			
		</table>
	</div>
-->

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

<!--
<hr />

<input type="button" value="Add" onclick="addCharityDiscount('AB','100')" />
<input type="button" value="Clear" onclick="clearCharityDiscounts()" />
<input type="button" value="Clear" onclick="xajax_get_charity_discounts('2007500029')" />

-->
</div>



<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}} 	
