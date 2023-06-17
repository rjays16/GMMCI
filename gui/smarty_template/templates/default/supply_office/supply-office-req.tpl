{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<script type="text/javascript" language="javascript">
<!--
	function openWindow(url) {
		window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}
-->
</script>
{{if $bShowQuickKeys}}
<style type="text/css">
<!--
	table.quickKey td.qkimg{
		font:bold 11px Tahoma;
		vertical-align:middle;
	}
	
	table.quickKey td.qktxt {
		width:70px;
		padding:2px 4px;
		font:bold 11px Tahoma;
		vertical-align:middle;
		color:#007000;
	}
-->
</style>

<div style="width:80%">
	<table border="0" cellspacing="1" cellpadding="2">
		<tr>
			<td class="jedPanelHeader">Quick keys</td>
		</tr>
		<tr>
			<td style="background-color:#fffeed; border:1px solid #ebeac4">
				<table class="quickKey" cellpadding="0" cellspacing="1" border="0">
					<tr>

						<td class="qkimg" nowrap="nowrap" ><img src="{{$sRootPath}}images/shortcut-f2.png" /></td>
						<td class="qktxt">Add items</td>
						
						<td	class="quickKey" nowrap="nowrap"><img src="{{$sRootPath}}images/shortcut-f3.png" /></td>
						<td class="qktxt">Clear list</td>
						
						<td	class="quickKey" nowrap="nowrap"><img src="{{$sRootPath}}images/shortcut-f9.png" /></td>
						<td class="qktxt">Person select</td>
						
						<td	class="quickKey" nowrap="nowrap"><img src="{{$sRootPath}}images/shortcut-f12.png" /></td>
						<td class="qktxt">Save/Submit</td>

					</tr>
				</table>	
			</td>
		</tr>
	</table>
</div>
{{/if}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />
{{$sFormStart}}

	<div style="width:740px" align="center">
		<table border="0" align="center" style="margin-bottom:2px" >
			<tr>
				<td width="1"><strong style="white-space:nowrap">Pharmacy area</strong></td>
				<td width="*">{{$sSelectArea}}</td>
			</tr>
		</table>
		<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%">
			<tbody>
				<tr>
					<td class="jedPanelHeader" width="*">
						Order Details
					</td>
					<td class="jedPanelHeader" width="170">
						Reference No.
					</td>
					<td class="jedPanelHeader" width="215">
						Order Date
					</td>
				</tr>
				<tr>
					<td rowspan="3" class="jedPanel" align="center" valign="top">
						<table width="95%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Arial" >
							<tr>
								<td></td>
								<td colspan="3">
									<table border="0" cellpadding="0" cellspacing="0" style="font:normal 12px Arial" >
										<tr>
											<td align="right" nowrap="nowrap"><strong>Transaction type</strong></td>
											<td align="center" nowrap="nowrap" width="70">
												{{$sIsCash}}
											</td>
											<td align="center" nowrap="nowrap"  width="70">
												{{$sIsCharge}}
											</td>
											<td align="center" nowrap="nowrap"  style="display:none">
												{{$sIsTPL}}
											</td>
										</tr>
									</table>
								</td>
							</tr>
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
							<tr>
								<td></td>
								<td valign="top" colspan="3">
									<strong>Patient type:</strong>
									{{$sOrderEncType}}
									<span id="encounter_type_show" style="font-weight:bold;color:#000080">{{$sOrderEncTypeShow}}</span>
								</td>
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
						<table style="font:bold 12px Arial">
							<tr>
{{if $ssView}}
{{else}}
								<td valign="middle">
									<div style=""><strong>Classification: </strong><span id="sw-class" style="font:bold 14px Arial;color:#006633">{{$sSWClass}}</span></div>
									<div style="margin-top:5px; vertical-align:middle; ">{{$sDiscountShow}}</div>
								</td>
{{/if}}
							</tr>
						</table>
						{{$sBtnDiscounts}}
					</td>
					<td class="jedPanel" align="center" style="padding-bottom:5px;">
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
<!--
						<div style="padding-left:0px">
							<strong>Priority</strong>

						</div>
						<div style="padding-left:15px; margin-bottom:4px; vertical-align:middle">
							<strong style="float:left; margin-top:10px">Notes</strong>
							{{$sComments}}
						</div>
-->
					</td>
				</tr>

			</tbody>
		</table>
		<!--
		<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%">
			<tbody>
				<tr>
					<td class="jedPanelHeader" colspan="8">Lingap/CMAP Coverages</td>
				</tr>
				<tr>
					<td class="jedPanel" align="center" width="40%">
						<table width="95%" border="0" cellpadding="1" cellspacing="0" style="font:normal 12px Arial" >
							<tr>
								<td nowrap="nowrap" width="1%"><strong style="margin-right:5px">Select sponsor</strong></td>
								<td align="left" nowrap="nowrap">
									{{$sSponsor}}									
								</td>
							</tr>
						</table>
					</td>
					<td class="jedPanel" align="center">
						<table width="95%" border="0" cellpadding="1" cellspacing="0" style="font:normal 12px Arial" >
							<tr>
								<td nowrap="nowrap"><strong>Amount covered</strong></td>
								<td align="left" nowrap="nowrap">
									{{$sSponsorAmount}}
								</td>
							</tr>
						</table>
					</td>
					<td class="jedPanel" align="center">
						<table width="95%" border="0" cellpadding="1" cellspacing="0" style="font:normal 12px Arial" >
							<tr>
								<td nowrap="nowrap"><strong>Final amount due</strong></td>
								<td align="left" nowrap="nowrap">
									{{$sSponsorFinalAmount}}
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
-->
		
	</div>

	<br />

	<div style="width:760px" align="center">
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
					<th width="4%" nowrap="nowrap" align="center">Consigned</th>
					<th width="10%" align="center" nowrap="nowrap">Quantity</th>
					<th width="10%" align="right" nowrap="nowrap">Price(Orig)</th>
					<th width="10%" align="right" nowrap="nowrap">Price(Adj)</th>
					<th width="10%" align="right" nowrap="nowrap">Acc. Total</th>
				</tr>
			</thead>
			<tbody>
{{$sOrderItems}}
			</tbody>
		</table>
		
		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
			<tbody>
				<tr>
					<td width="*" align="right" style="background-color:#ffffff; padding:4px" height=""><strong>Sub-Total</strong></th>
					<td id="show-sub-total" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold"></th>
				</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Discount</strong></th>
					<td id="show-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold"></th>
				</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Net Total</strong></th>
					<td id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold"></th>
				</tr>
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
