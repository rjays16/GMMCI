{{$sFormStart}}
<div style="width:100%">
	<table border="0" cellspacing="1" cellpadding="2" width="60%" align="center" style="">
		<tbody>
			<tr>
				<td class="segPanelHeader">
					Search pharmacy product
				</td>
			</tr>
				<td class="segPanel">

					<table width="100%" cellspacing="0" cellspacing="0">
						<tr>
							<td align="right" valign="middle" width="20%"><strong>Code/Name</strong></td>
							<td align="left" valign="middle" width="30%" style="">
								{{$sCodeName}}
							</td>
							<td align="left" valign="middle" width="*" style="">
								<strong>Search products by code or name</strong>
							</td>
						</tr>
						<tr>
							<td align="right" valign="middle"><strong>Generic name</strong></td>
							<td align="left" valign="middle" style="">
								{{$sGenericName}}
							</td>
							<td align="left" valign="middle" style="">
								<strong>Search products by generic name</strong>
							</td>
						</tr>
						<tr>
							<td align="right" valign="middle"><strong>Type</strong></td>
							<td align="left" valign="middle" style="">
								{{$sProdClass}}
							</td>
							<td align="left" valign="middle" style="">
								<strong>Search for medicines or supplies</strong>
							</td>
						</tr>
						<tr>
							<td ></td>
							<td colspan="2" style="height:2px">
								<button class="segButton" onclick="search(); return false"><img src="{{$sRootPath}}gui/img/common/default/magnifier.png">Search</button>
							</td>
						</tr>
					</tbody>
				</table>
				</td>
			<tr>
			</tr>
		</tbody>
	</table>

	<br />

	<div align="left" style="width:85%">
		<div style="padding:2px 0px">
			{{$sCreateProduct}}{{$sProductCategories}}{{$sCreateClassification}}
		</div>
		{{$sProductList}}
<!--
		<table id="" class="segList" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:10px">
			<thead>
				{{$sListNav}}
				<tr id="">
					<th align="center" width="1%">Type</th>
					<th align="left" width="5%" nowrap>Item code</th>
					<th align="left" width="*" nowrap>Item name/Generic name</th>
					<th align="center" width="1%"></th>
				</tr>
			</thead>
			<tbody>
{{$sSearchResults}}
			</tbody>
		<tfoot>
			<tr>
				<th colspan="3" align="left"><span class="segLink" style="font-size:10px" onclick="toggleTBody('list_hs0000000000')">Hide/Show details</span></th>
				<th align="left">&nbsp;</th>
				<th align="right">SUBTOTAL</th>
				<th id="subtotal_hs0000000000" colspan="2" align="right">0.00</th>
			</tr>
		</tfoot>
		</table>
-->
		<div style="margin-top:2px">
			<span style="font:bold 11px Arial">Legend:</span>
			<span style="margin-left:5px; color:#000066">
				Medicine
				<img src="{{$sRootPath}}gui/img/common/default/pharma_meds.png" align="absmiddle" />
			</span>
			<span style="margin-left:5px; color:#006600">
				Supplies
				<img src="{{$sRootPath}}gui/img/common/default/pharma_supplies.png" align="absmiddle" />
			</span>
		</div>
	</div>


{{$sHiddenInputs}}
{{$jsCalendarSetup}}
<img src="" vspace="2" width="1" height="1"><br/>
{{$sDiscountControls}}
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>

<div style="width:80%">
{{$sUpdateControlsHorizRule}}
{{$sUpdateOrder}}
{{$sCancelUpdate}}
</div>


</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}
