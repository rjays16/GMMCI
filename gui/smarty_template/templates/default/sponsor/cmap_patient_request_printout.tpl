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
		<div class="" style="width:60%">
			<div id="basic" class="segPanel">
				<table width="100%" border="0" cellpadding="1" cellspacing="2" style="font:normal 12px Arial; margin:2px">
					<tr align="left">
						<td width="1" valign="top" style="white-space:nowrap">
							<label>View requests from</label>
						</td>
						<td>
{{$sSources}}
						</td>
					</tr>
					<tr id="row_dept" style="display:none">
						 <td valign="top" align="right">
							<label>Department</label>
						</td>
						<td>{{$sDepartment}}</td>
					</tr>
					<tr>
						<td valign="top" align="right">
							<label>Date from</label>
						</td>
						<td>
{{$sRequestFilterDateFrom}}
						</td>
					</tr>
					<tr>
						<td valign="top" align="right">
							<label>Date to</label>
						</td>
						<td>
{{$sRequestFilterDateTo}}
						</td>
					</tr>
					<tr>
						<td align="left" colspan="2">
								<button class="segButton" onclick="search(); return false;"><img src="../../gui/img/common/default/magnifier.png" />Search</button>
								<button class="segButton" onclick="print(); return false;" disabled="disabled" id="print_button"><img src="../../gui/img/common/default/printer.png" />Print Form</button>
						</td>
					</tr>
				</table>
			</div>
		</div>
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