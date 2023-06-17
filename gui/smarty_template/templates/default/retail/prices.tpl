{{* prices.tpl  Form template for manage transactions module (pharmacy & meddepot) Segworks Technologies, Inc *}}

<table border=0 cellspacing=2 cellpadding=3 width="80%">
	<tr bgcolor=#ffffdd>
		<td colspan=2>
			<FONT color="#800000"></font>
			<br><p>
		</td>
	</tr>
	<tr bgcolor=#ffffdd>
		<td align=right>Enter Product ID/Name</td>
		<td>
			<input type="text" id="keyword" size=40 maxlength=40 value="">
		</td>
	</tr>
	<tr>
		<td>
			<input type="reset" value="Reset" onClick="$('keyword').value='';$('keyword').focus();">
 		</td>
		<td align=right>
			<input type="button" value="Search" onclick="if ($('keyword').value) xajax_populateProductPrices($('keyword').value);">
		</td>
	</tr>
</table>

<table border="1" width="80%" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" style="padding:1px">
			<div style="width:100%;height:304px;overflow:hidden;border:1px solid black;">
			<div style="width:100%;height:320px;overflow:scroll;">
			<table  id="ppriceTable" class="segList" width="100%" border="0" cellpadding="0" cellspacing="1">		
				<thead>
					<tr class="wardlistrow1" id="ppriceRowHeader">
						<th width="15%" nowrap>&nbsp;Product ID</th>
						<th width="30%" nowrap>Product Name</th>
						<th width="15%">&nbsp;Purchase price</th>
						<th width="15%">&nbsp;Retail price (Cash)</th>
						<th width="15%">&nbsp;Retail price (Charge) </th>
						<th width="10%" align="center">Update</th>
					</tr>					
				</thead>
				<tbody>
				</tbody>
			</table>
			</div></div>
		</td>
	</tr>
	
</table>

{{$jsCalendarSetup}}
<br/>
<span id="tdShowWarnings" style=" font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal"></span>
<br/><img src="" vspace="1" width="1" height="1"><br/>
<div style="float:left;">
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1%">{{$sContinueButton}}</td>
		<td width="2">&nbsp;</td>
		<td>{{$sBreakButton}}</td>
	</tr>
</table>
</div>

<!--
<input type="button" value="Add" onClick="retail_addProductPrice('1000', 'Family', '0','0','0')"> 
<input type="button" value="Clear" onClick="retail_rmvProductPrice(1)"> 
<input type="button" value="Color" onClick="ppricecolorrow(1)">  -->
</div>
{{$sFormStart}}
{{$sHiddenFields}}
<input type="hidden" name="refno" id="sRefNo" value="">
<input type="hidden" name="pencnum" id="sPayerID" value="">
<input type="hidden" name="pname" id="sPayerName" value="">
<input type="hidden" name="purchasedt" id="sPurchaseDate" value="">
<input type="hidden" name="is_cash" id="sIsCash" value="">
<input type="hidden" name="saveok" value="1">
{{$sFormEnd}}