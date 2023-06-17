<!-- RETAIL DISCOUNT INFORMATION BLOCK -->
<div id="referenceSearchTab" style="border:0px solid black;padding:2px;background-color:#FFFFFF;width:100%;position:relative;display:block" align="center">
<table border="0" cellpadding="0" cellspacing="0" style="width:100%">
	<tr>
		<td width="35%" valign="top">		
			<div style="width:100%;height:224px;overflow:hidden;border:1px solid black;">
			<div style="width:100%;height:240px;overflow:scroll;border:1px solid black">
			
			<table width="100%" border="0" cellpadding="0" cellspacing="1" id="srcRowsTable" style="font-size:12px">
				<thead>
					<tr class="reg_list_titlebar" style="font-weight:bold " id="srcRowsHeader">
						<th width="*"><strong>Discount</strong>&nbsp;</th>
						<th width="50%" nowrap align="left">							
							<select id="selDiscount" style="width:100%;font-size:12px" onchange=""></select>
							<input id="seldiscountid" type="hidden" value="">
							<input id="seldiscountdesc" type="hidden" value="">
							<input id="seldiscount" type="hidden" value="">
							
						</th>
						<th width="40">
							<input id="btnAddDiscount" type="button" value="Add" onclick="prepareAddRDiscount()" style="width:100%">
						</th>
					</tr>
				</thead>
			</table>

<!--				<tbody>
					<tr><td colspan="3"> -->
						<table id="rdiscountTable" width="100%" border="0" cellpadding="0" cellspacing="1">
							<tbody>
								<tr>
									<td>No discount added...</td>
								</tr>
							</tbody>
						</table>
<!--				</td></tr> -->
			<table width="100%" border="0" cellpadding="0" cellspacing="1" id="srcRowsTable" style="font-size:12px">
					<tr class="reg_list_titlebar">
						<td width="*"><strong>Total discount</strong></td>
						<td width="80" align="right"><span id="txtTotalDiscount" style="font-weight:bold">0%</span></td>
						<td width="40" align="left">&nbsp;</td>
					</tr>
			</table>
			</div></div>
		</td>
		<td width="5">&nbsp;&nbsp;</td>
		<td bgcolor="#444444">&nbsp;</td>
		<td width="5">&nbsp;&nbsp;</td>
		<td valign="top">
			<div style="width:100%;height:224px;overflow:hidden;border:1px solid black;">
			<div style="width:100%;height:240px;overflow:scroll;border:1px solid black">
			<table width="100%" border="0" cellpadding="1" cellspacing="1" id="summaryTable" style="font:12px 'Courier New'">
				<thead>
					<tr class="reg_list_titlebar" style="font-weight:bold">
						<td width="10%" nowrap>&nbsp;Qty</td>
						<td width="45%" nowrap>Item description</td>
						<td width="15%">&nbsp;Total</td>
						<td width="15%">&nbsp;Discount</td>
						<td width="15%">&nbsp;Net</td>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			</div></div>
		</td>
	</tr>
</table>


</div>

<!--<input type="button" onClick="gui_addRDiscountRow('id', 'desc', 'discount')" value="Add">-->


<!-- END: DISCOUNT INFORMATION BLOCK -->