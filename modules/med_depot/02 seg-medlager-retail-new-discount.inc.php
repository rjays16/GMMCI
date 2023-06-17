<!-- RETAIL DISCOUNT INFORMATION BLOCK -->
<div id="referenceSearchTab" style="border:0px solid black;padding:2px;background-color:#FFFFFF;width:100%;position:relative;display:block" align="center">
<table border="0" cellpadding="0" cellspacing="0" style="width:100%">
	<tr>
		<td width="40%" valign="top">		
			<div style="width:100%;height:224px;overflow:hidden;border:1px solid black;">
			<div style="width:100%;height:240px;overflow:scroll;border:1px solid black">
			
			<table width="100%" border="0" cellpadding="1" cellspacing="1" id="srcRowsTable">
				<thead>
					<tr class="reg_list_titlebar" style="font-weight:bold " id="srcRowsHeader">
						<th width="100%" colspan="3" nowrap align="left">
							<strong>Discount</strong>&nbsp;
							<select id="selDiscount" style="width:60%">
							<!--
								<option selected>Senior citizen (5%)</option>
								<option>Company discount (10%)</option>
								<option>Veteran</option>
							-->
							</select>
							<input id="btnAddDiscount" type="button" value="Add" onclick="xajax_addRetailDiscount('<? echo $refno ?>',$('selDiscount').options[$('selDiscount').selectedIndex].value)">
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="reg_list_titlebar">
						<td colspan="3" bgcolor="#FFFFFF">
							<table border="0" width="100%" cellspacing="1" cellpadding="2">
								<tr class="reg_list_titlebar" >
									<td width="70%"><strong>Total discount</strong></td>
									<td width="*" align="right"><span id="txtTotalDiscount" style="font-weight:bold">0%</span></td>
								</tr>
							</table>
						</td>						
					</tr>
				</tbody>
			</table>
			</div></div>
		</td>
		<td width="5">&nbsp;&nbsp;</td>
		<td bgcolor="#444444">&nbsp;</td>
		<td width="5">&nbsp;&nbsp;</td>
		<td valign="top">
			<div style="width:100%;height:224px;overflow:hidden;border:1px solid black;">
			<div style="width:100%;height:240px;overflow:scroll;border:1px solid black">
			<table width="100%" border="0" cellpadding="0" cellspacing="1" id="summaryTable">
				<thead>
					<tr class="reg_list_titlebar" style="font-weight:bold">
						<td width="10%" nowrap>&nbsp;Qty</td>
						<td width="55%" nowrap>Item description/Generic Name</td>
						<td width="10%">&nbsp;Total</td>
						<td width="10%">&nbsp;Discount</td>
						<td width="15%">&nbsp;Net</td>
					</tr>
				</thead>
			</table>
			</div></div>
		</td>
	</tr>
</table>


</div>



<!-- END: DISCOUNT INFORMATION BLOCK -->