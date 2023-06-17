<style type="text/css">
.displayTotals {
	text-align:right;
	font-family:Arial;
	font-size:16px;
	font-weight:bold;
}

.displayTotalsLink {
	font-family:Arial;
	font-size:16px;
	font-weight:bold;
	cursor:pointer;
	color:#000066;
}

span.displayTotalsLink:hover {
	text-decoration:underline;
	color:#660000;
	background: #cccccc;
}
div#mainContent div, div#mainContent table {
	-moz-box-sizing: border-box;
}
</style>

{{$sFormStart}}
<div id="mainContent" style="width:98%">
	<div style="padding:4px;">
		<ul id="request-tabs" class="segTab" style="padding-left:10px;">
			<li id="tab_pharma" {{if $bTabRequest}}class="segActiveTab"{{/if}} onclick="tabClick(this)" segTab="requests">
				<h2 class="segTabText">Pharmacy</h2>
			</li>
			<li id="tab_lab" {{if $bTabBilling}}class="segActiveTab"{{/if}} onclick="tabClick(this)" segTab="billing">
				<h2 class="segTabText">Laboratory</h2>
			</li>
			<li id="tab_radio" {{if $bTabDeposit}}class="segActiveTab"{{/if}} onclick="tabClick(this)" segTab="deposit">
				<h2 class="segTabText">Radiology</h2>
			</li>
		</ul>
		<div class="">
			<div id="basic" style="padding:4px" class="segPanel">
				<table width="100%" border="1" cellpadding="1" cellspacing="2" style="font:normal 12px Arial; margin:2px" >
					<tr>
						<td rowspan="2" width="1">
							<button id="add-item" class="segButton"><img src="../../gui/img/common/default/add.png">Add item</button>
						</td>
						<td width="15%">
							<label>Date</label>
						</td>
						<td width="45%">
							<label>Item description</label>
						</td>
						<td width="10%">
							<label>Quantity</label>
						</td>
						<td width="15%">
							<label>Total Price</label>
						</td>
						<td width="15%">
							<label>Price per item</label>
						</td>
					</tr>
					<tr>
						<td></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<div style="padding:0 4px; text-align:left">
		<button class="segButton" onclick="search(); return false;"><img src="../../gui/img/common/default/magnifier.png" />Search</button>
		<button class="segButton" onclick="return false;" disabled="disabled"><img src="../../gui/img/common/default/exclamation.png" />Reset</button>
	</div>
	<div>
		<div id="rqsearch" style="margin-top:10px; overflow:hidden" align="center">
			<div class="dashlet">
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="dashletHeader" style="font:bold 11px Tahoma">
					<tr>
						<td width="30%" valign="top"><h1 style="white-space:nowrap">Request list</h1></td>
						<td width="*" align="right" valign="top" nowrap="nowrap"></td>
					</tr>
				</table>
			</div>
			<div>
{{$lstRequest}}
			</div>
		</div>
		<div id="hidden-inputs" style="display:none">
{{$sHiddenInputs}}
	</div>
{{$jsCalendarSetup}}
</div>
{{$sFormEnd}}
{{$sTailScripts}}