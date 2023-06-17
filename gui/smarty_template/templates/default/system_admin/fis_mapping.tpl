{{$form_start}}
<div style="width:800px">
	<table border="0" cellspacing="1" cellpadding="0" width="80%" align="center" style="">
		<tbody>
			<tr>
				<td colspan="4" class="segPanelHeader">Search Details</td>
			</tr>
			<tr>
				<td class="segPanel">
					<table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
						<tbody>
							<tr>
								<td width="20%" align="right"><strong>Account transaction</strong>&nbsp;</td>
								<td align="left">{{$accountTransaction}}</td>
							</tr>
							<tr>
								<td width="20%" align="right"><strong>Cost Center Area</strong>&nbsp;</td>
								<td align="left">{{$serviceArea}}</td>
							</tr>
							<tr>
								<td width="20%" align="right"><strong>Search Service/Item</strong></td>
								<td align="left">
									{{$patientOptions}}
									<span id="p_name" style="display;">{{$pSearchName}}</span>
									<span id="p_pid" style="display:none">{{$pSearchId}}</span>
									<span id="p_enc" style="display:none">{{$pSearchEnc}}</span>
									{{$search_btn}}{{$AddAccount_btn}}
								</td>
							</tr>
							<tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="dashlet" style="margin-top:20px">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 11px Tahoma;">
			<tbody>
				<tr>
					<td width="30%" valign="top"><h1 style="white-space:nowrap">List of Services</h1></td>
				</tr>
			</tbody>
		</table>
		<div id="request-list"></div>
	</div>
</div>


{{$form_end}}