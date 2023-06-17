{{* discount.tpl  Form template for managing discounts -- Segworks Technologies, Inc *}}
<br>
<div align="center">
<table border="0" width="96%" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" style="padding:1px">

			<div style="width:100%;">
			<table class="segList" width="100%" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr class="reg_list_titlebar" >
						<th width="10%" align="center"><strong>ID</strong></td>
						<th width="30%" align="center"><strong>Description</strong></td>
						<th width="10%" align="center"><strong>Discount<br>(in %)</strong></td>
						<!--added by VAN 06-18-08 -->
						<th width="20%" align="center"><strong>Area Used</strong></td>
						<th width="12%" align="center"><strong>Applied<br>Areas</strong></td>
						<!-- -->
						<th width="*"><strong>Options</strong></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><input id="inputID" type="text" style="width:100%; font:bold 12px Arial"></td>
						<td><input id="inputDesc" type="text" style="width:100%; font:bold 12px Arial"></td>
						<td><input id="inputDiscount" type="text" style="width:100%; font:bold 12px Arial"></td>
						<!--added by VAN 06-18-08 -->
						<td>
							<select id="area_used" name="area_used">
								<option value="A">All</option>
								<option value="B">Billing</option>
								<option value="L">Laboratory</option>
								<option value="O">Operating Room</option>
								<option value="P">Pharmacy</option>
								<option value="R">Radiology</option>
							</select>
						</td>
						<!-- -->
						<!-- modifications by LST 07-30-2008 -->
						<td align="center">
							<input type="hidden" name="billareas_id" id="billareas_id" value="">
							<div id="billareas_appplied" style="display:none">&nbsp;</div>
							<a id="billareas_label" name="billareas_label" style="display:none" href="javascript:void(0);" onmouseover="return overlib($('billareas_appplied').innerHTML, LEFT);" onmouseout="return nd();">View Areas</a>
						</td>
						<td>{{$sSetBillAreasApplied}}&nbsp;<input type="button" style="font:bold 12px Arial" value="Add" onClick="js_prepareAdd()"></td>
						<!-- -->						
					</tr>
				</tbody>
			</table>
			</div>
			<br>
			
			<div style="width:100%;height:264px;overflow:hidden;">
			<div style="width:100%;height:280px;overflow:scroll;">
			<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0" id="discountTable">
				<thead>
					<tr class="reg_list_titlebar" style="font-weight:bold;height:24px" id="discountRowHeader">
						<th width="10%" nowrap>Discount ID</th>
						<th width="30%" nowrap>Description</th>
						<th width="10%">Discount<br>(in %)</th>
						<!--added by VAN 06-18-08 -->
						<th width="20%">Area Used</th>
						<!-- -->
						<th width="15%" align="center"><strong>Bill Areas<br>Applied</strong></td>
						<th width="*">Options</th>
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

{{$jsCalendarSetup}}
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
{{$sFormEnd}}