<script type="text/javascript" language="javascript">
<!--
	function openWindow(url) {
		window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}
-->
</script>
<div align="center">
<table width="60%" border="0" cellpadding="0" cellspacing="0">
<thead>
			<tr>
				<th width="*" align="center">{{$sMsgTitle}}</th>
			</tr>
</thead>
</table>
</div>
<br/>
<div align="center">
	<table width="60%" border="0" cellpadding="0" cellspacing="0" class="jedDialog">
		<tbody>
			<tr>
				<td align="center">
					<style type="text/css" media="all">
						.detailstb tr td {
						}

						.detailstb tr td span {
							font:bold 11px Tahoma;
							color:#00006d;
						}
					</style>
					<table class="detailstb" align="center" width="95%" border="1" cellpadding="2" cellspacing="0" style="border:1px solid #cad3e8;border-collapse:collapse; font:bold 12px Arial">
						<tr>
							<td><b>Requested Area</b></td>
							<td><span>{{$sDesArea}}</span></td>
						</tr>
						<tr>
							<td><b>Requested By</b></td>
							<td><span>{{$sReqName}}</span></td>
						</tr>
						<tr>
							<td width="20%"><b>Reference No.</b></td>
							<td><span>{{$sRefNo}}</span></td>
						</tr>
						<tr>
							<td><b>Request Date</b></td>
							<td><span>{{$sReqDate}}</span></td>
						</tr>
						<!--<tr>
							<td><b>Type</b></td>
							<td><span>{{$sCashCharge}}</span></td>
						</tr>-->
						<!--<tr>
							<td><b>Address</b></td>
							<td><span>{{$sOrderAddress}}</span></td>
						</tr>
						<tr>
							<td><b>Priority</b></td>
							<td><span>{{$sPriority}}</span></td>
						</tr>
						<tr>
							<td><b>Notes</b></td>
							<td><span>{{$sRemarks}}</span></td>
						</tr>-->
						<tr>
							<td><b>Requested Items</b></td>
							<td align="left">
								<table border="0" width="100%" cellpadding="1" cellspacing="1" style="margin:0px;border:1px solid #006699">
									<tr>
											<td width="45%" class="jedPanelHeader">Items</td>
											<!--<td width="12%" class="jedPanelHeader" align="center">Price</td>-->
											<td width="12%" class="jedPanelHeader" align="center">Quantity</td>
											<td width="15%" class="jedPanelHeader" align="center">Unit</td>
									</tr>
									<tr>
										<td>
										{{$sItems}}
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="center"></td>
			</tr>
			<tr>
				<td align="center">
					<div align="center" style="width:95%;padding:0;margin:0">
						{{$sPrintButton}}
						{{$sBreakButton}}
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<br>
<br>
<br style="list-style:disc">
