<script type="text/javascript" language="javascript">
<!--
	function openWindow(url) {
		window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}
-->
</script>

<br/>
<br/>
<br/>
<br/>
<br/>
<div align="center">
	<table width="60%" border="0" cellpadding="0" cellspacing="0" class="jedDialog">
		<thead>
			<tr>
				<th width="*">{{$sMsgTitle}}</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="center">
					<div align="left" style="width:95%;padding:0;margin:0">
						{{$sPrintButton}}
						{{$sBreakButton}}
					</div>
				</td>
			</tr>
			<tr>
				<td align="center">
					<style type="text/css">
						.detailstb td {}
						.detailstb td+td {color:#000066;}
					</style>
					<table class="detailstb" align="center" width="95%" border="1" cellpadding="2" cellspacing="0" style="border:1px solid #cad3e8;border-collapse:collapse; font:bold 12px Arial">
						<tr>
							<td><b>Pharmacy area</b></td>
							<td>{{$sSelectArea}}</td>
						</tr>
						<tr>
							<td width="20%"><b>Reference no.</b></td>
							<td>{{$sRefNo}}</td>
						</tr>
						<tr>
							<td><b>Order date</b></td>
							<td>{{$sOrderDate}}</td>
						</tr>
						<tr>
							<td><b>Type</b></td>
							<td>{{$sCashCharge}}</td>
						</tr>
						<tr>
							<td><b>Name</b></td>
							<td>{{$sOrderName}}</td>
						</tr>
						<tr>
							<td><b>Address</b></td>
							<td>{{$sOrderAddress}}</td>
						</tr>
						<tr>
							<td><b>Priority</b></td>
							<td>{{$sPriority}}</td>
						</tr>
						<tr>
							<td><b>Remarks</b></td>
							<td>{{$sRemarks}}</td>
						</tr>
						<tr>
							<td><b>Items</b></td>
							<td align="left">{{$sItems}}</td>
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
<br>
<br>
<br style="list-style:disc">
