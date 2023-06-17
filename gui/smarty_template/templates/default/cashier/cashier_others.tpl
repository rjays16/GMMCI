{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}
{{if $bShowQuickKeys}}
<div style="width:80%">
	<table border="0" cellspacing="1" cellpadding="2" style="width:100%">
		<tr>
			<td class="jedPanelHeader">Quick keys</td>
		</tr>
		<tr>
			<td style="background-color:#fffeed; border:1px solid #ebeac4">
				<table cellpadding="1" cellspacing="1" border="0">
					<tr>
						<td width="1"><img src="{{$sRootPath}}images/shortcut-f2.png" /></td>
						<td style="font:bold 11px Arial; white-space:nowrap">Add hospital service</td>
						<td width="10">&nbsp;</td>
						<td	 width="1"><img src="{{$sRootPath}}images/shortcut-f3.png" /></td>
						<td style="font:bold 11px Arial; white-space:nowrap">Amount tendered</td>
						<td width="99%"></td>
					</tr>
				</table>	
			</td>
		</tr>
	</table>
</div>
<br />
{{/if}}
<div align="center" style="margin-bottom:10px; width:80%">
	<table border="0">
		<tr>
			<td width="1"><strong style="white-space:nowrap">Account type</strong></td>
			<td width="5"></td>
			<td width="*">{{$sSelectAccountType}}</td>
		</tr>
	</table>
</div>
<div style="width:80%">
	{{include file="cashier/gui_cashier_info.tpl"}}
	<div style="width:100%; text-align:right; margin-top:5px">
		{{$sContinueButton}}
		{{$sBreakButton}}
	</div>
</div>
<br />
<div style="width:80%">
	{{include file="cashier/gui_totals.tpl"}}
	<br />
	<div align="left" style="margin:2px; color:#888888">
		{{$sHospitalServiceAdd}}
		{{$sHospitalServiceRemove}}
		{{$sHospitalServiceClear}}
	</div>
	<table id="list_hs0000000000" class="jedList" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:10px">
		<thead>
			<tr id="row_hs0000000000">
				<th width="3%" style="padding:0 2px;">
					<!-- <input type="checkbox" onchange="flagCheckBoxesByName('hs0000000000[]',this.checked)"> -->
					&nbsp;
				</th>
				<th align="left" width="10%" nowrap>Item No</th>
				<th align="left" width="*" nowrap>Item Description</th>
				<th align="right" width="9%" nowrap="nowrap" style="font-size:90%">Price/item</th>
				<th align="right" width="5%">Quantity</th>
				<th align="right" width="9%">Price</th>
			</tr>
		</thead>
		<tbody>
{{$sOtherHospitalServices}}
		</tbody>
		<tfoot>
			<tr>
				<th colspan="3" nowrap="nowrap" align="left"><span class="segLink" style="font-size:10px" onclick="toggleTBody('list_hs0000000000')">Hide/Show details</span></th>
				<th align="left">&nbsp;</th>
				<th align="right">Subtotal</th>
				<th align="right">
					<input type="hidden" id="subtotal_hs0000000000" name="subtotal_hs0000000000" value="0"/>
					<span id="show_subtotal_hs0000000000">0.00</span>
				</th>
			</tr>
		</tfoot>
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
