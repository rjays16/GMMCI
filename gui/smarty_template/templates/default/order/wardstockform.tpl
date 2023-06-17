{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<script type="text/javascript" language="javascript">
<!--
	function openWindow(url) {
		window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}
-->
</script>

<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}

	<div align="center" style="width:100%">
		<table border="0">
			<tr>
				<td width="1"><strong style="white-space:nowrap">Pharmacy area</strong></td>
				<td width="5"></td>
				<td width="*">{{$sSelectArea}}</td>
			</tr>
		</table>
	</div>
	<div style="width:600px" align="center">
		<table border="0" cellspacing="2" cellpadding="2" width="400" align="center">
			<tbody>
				<tr>
					<td class="jedPanelHeader">Ward stock information</td>
				</tr>
				<tr>
					<td class="jedPanel">
						<table width="100%" border="0" cellpadding="2" cellspacing="2" style="margin:4px">
							<tr>
								<td width="100" nowrap="nowrap">Reference no</td>
								<td>
									{{$sRefNo}}
									{{$sResetRefNo}}
								</td>
							</tr>
							<tr>
								<td nowrap="nowrap">Stock date</td>
								<td>{{$sOrderDate}}{{$sCalendarIcon}}</td>
							</tr>
							<tr>
								<td nowrap="nowrap">Select ward</td>
								<td>{{$sSelectWard}}</td>
							</tr>
						</table>						
					</td>
				</tr>
			</tbody>
		</table>
		<!--
		<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center">
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
								<td nowrap="nowrap"><strong>Transaction type</strong></td>
								<td align="left" nowrap="nowrap">
									{{$sIsCash}}
									{{$sIsCharge}}									
								</td>
								<td align="left">{{$sIsTPL}}</td>
							</tr>
						</table>
						<table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:4px; font:normal 12px Arial" >
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
						{{$sOrderDate}}{{$sCalendarIcon}}
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
{{if $ssView}}
{{else}}
								<td valign="middle">
									<div style=""><strong>Classification: </strong><span id="sw-class" style="font:bold 14px Arial;color:#006633">{{$sSWClass}}</span></div>
									<div style="margin-top:5px; vertical-align:middle; ">{{$sDiscountShow}}</div>
								</td>
								<td>{{$sDiscountInfo}}</td>
{{/if}}
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
						<div style="padding-left:35px; margin-bottom:4px; vertical-align:middle">
							<strong style="float:left; margin-top:10px">Notes</strong>
							{{$sComments}}
						</div>
					</td>
				</tr>
			</tbody>
		</table> -->

		<br />

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
					<th width="1%" nowrap="nowrap">&nbsp;</th>
					<th width="10%" nowrap="nowrap" align="left">Item No.</th>
					<th width="*" nowrap="nowrap" align="left">Item Description</th>
					<th width="10%" align="center" nowrap="nowrap">Quantity</th>
				</tr>
			</thead>
			<tbody>
{{$sOrderItems}}
			</tbody>
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
</div>


</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}} 	
