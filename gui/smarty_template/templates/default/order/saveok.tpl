<script type="text/javascript">
function openWindow(url) {
	window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
}
</script>

<br/>
<div align="center">
	<table width="600" class="panel">
		<thead>
			<tr>
				<th class="panelHeader" width="*">{{$sMsgTitle}}</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="center" class="panelContent">
					<div align="left" style="width:95%;padding:0;margin:0">
						{{$sPrintButton}}
						{{$sBreakButton}}
					</div>
				</td>
			</tr>
			<tr>
				<td align="center" class="panelContent">
					<style type="text/css" media="all">
						.detailstb tr td {
						}
						.detailstb tr td span {
							font:bold 11px Tahoma;
							color:#00006d;
						}
					</style>
					<table class="detailstb" align="center" width="98%" border="0" cellpadding="2" cellspacing="0" style="border-collapse:collapse; font:bold 12px Arial">
						<tr>
							<td><strong>Pharmacy area</strong></td>
							<td><span>{{$sSelectArea}}</span></td>
						</tr>
						<tr>
							<td width="20%"><strong>Reference no.</strong></td>
							<td><span>{{$sRefNo}}</span></td>
						</tr>
						<tr>
							<td><strong>Order date</strong></td>
							<td><span>{{$sOrderDate}}</span></td>
						</tr>
						<tr>
							<td><strong>Type</strong></td>
							<td><span>{{$sCashCharge}}</span></td>
						</tr>
						<tr>
							<td><strong>Name</strong></td>
							<td><span>{{$sOrderName}}</span></td>
						</tr>
						<tr>
							<td><strong>Address</strong></td>
							<td><span>{{$sOrderAddress}}</span></td>
						</tr>
						<tr>
							<td><strong>Priority</strong></td>
							<td><span>{{$sPriority}}</span></td>
						</tr>
						<tr>
							<td><strong>Notes</strong></td>
							<td><span>{{$sRemarks}}</span></td>
						</tr>
						<tr>
							<td><strong>Request details</strong></td>
							<td align="left">
								<table class="segList" border="0" width="100%" cellpadding="0" cellspacing="0" style="">
									<thead>
										<tr>
											<th width="15%" >Code</td>
											<th width="*" >Particular/s</td>
											<th width="15%" align="center">Price</td>
											<th width="10%" align="center">Quantity</td>
											<th width="15%" align="center">Total</td>
										</tr>
									</thead>
									<tbody>
										{{$sItems}}
									</tbody>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="center"></td>
			</tr>
		</tbody>
	</table>
</div>
<br/>
<br/>