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
		<div class="">
			<div id="basic" style="padding:4px" class="segPanel">
				<table width="95%" border="0" cellpadding="1" cellspacing="2" style="font:normal 12px Arial; margin:2px" >
					<tr>
						<td width="1" valign="top" style="white-space:nowrap">
							<label>View requests from</label>
						</td>
						<td>
{{$sSources}}
						</td>
						<td width="20"></td>
						<td width="1" valign="top" style="white-space:nowrap">
							<label>Service / item name</label>
						</td>
						<td>
							<input id="basic-name" class="segInput" type="text" />
						</td>
					</td>
					<tr>
						<td valign="top">
							<label>Date</label>
						</td>
						<td>
{{$sRequestFilterDate}}
						</td>
						<td></td>
						<td valign="top" style="white-space:nowrap">
							<label>Grant</label>
						</td>
						<td valign="top">
							<select id="basic-grant" class="segInput">
								<option value="">--View all--</option>
								<option value="NULL">Not granted</option>
								<option value="PAID">Payment</option>
								<option value="LINGAP">Lingap</option>
								<option value="CMAP">CMAP</option>
								<option value="CHARITY">Charity</option>
							</select>
						</td>
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