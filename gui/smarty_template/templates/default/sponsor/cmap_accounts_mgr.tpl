{{$sFormStart}}
<div style="width:595px;">
	<table border="0" width="100%" class="Search">
		<tbody>
			<tr>
				<td class="segPanelHeader" colspan="3">Add new sponsor</td>
			</tr>
			<tr>
				<td class="segPanel">
					<table border="0" width="100%" class="Search" style="font: 12px Arial;">
						<tbody>
							<tr>
								<td style="white-space:nowrap;width:130px"><label><b>Account name:</b></label></td>
								<td align="left" valign="middle">{{$accountName}}</td>
							</tr>
							<tr>
								<td style="white-space:nowrap;width:130px"><label><b>Account Address:</b></label></td>
								<td align="left" valign="middle">{{$accountAddress}}</td>
								<td align="right" valign="bottom">{{$addBtn}}</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<br/>
	<div id="sponsor_account_list" style="width:595px;" align="center"></div>
</div>

<div id="edit_sponsor" align="left" style="display:none">
<table border="0" width="100%" class="Search">
		<tbody>
			<tr>
				<td class="segPanel">
					<table border="0" width="100%" class="Search" style="font: 12px Arial;">
						<tbody>
							<tr>
								<td style="white-space:nowrap;width:130px"><label><b>Name:</b></label></td>
								<td align="left" valign="middle">{{$update_sponsor_name}}</td>
							</tr>
							<tr>
								<td style="white-space:nowrap;width:130px"><label><b>Address:</b></label></td>
								<td align="left" valign="middle">{{$update_sponsor_address}}</td>
								<td align="right" valign="bottom">{{$update_sponsor}}</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
{{$sFormEnd}}