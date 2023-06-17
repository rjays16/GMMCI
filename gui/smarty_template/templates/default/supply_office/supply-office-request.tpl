{{* form.tpl  *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />
{{$sFormStart}}
<table border="0" width="90%" align="center">
		<tr>
			<td><strong style="white-space:nowrap">&nbsp;Department:</strong>
				<!--{{$selecthere}}-->
				{{$sDeptName}}
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" align="center" width="100%" cellspacing="1" cellpadding="2">	
					<tr>
						<thead>
							<td class="jedPanelHeader" width="*">&nbsp;Name of Personnel</td>
							<td class="jedPanelHeader" width="25%">&nbsp;Reference #</td>
							<td class="jedPanelHeader" width="25%">&nbsp;Date of Request</td>
						</thead>
						<tbody align="center" class="jedPanel">
							<td class="segPanel">
								<table align="left">
									<tr>
										<td><strong>Name:</strong></td>
										<td>
											{{$sOrderEncID}}
											{{$sOrderName}}
										</td>
									</tr>
								</table>
							</td>
							<td class="segPanel">
								{{$sRefNo}}
								{{$sResetRefNo}}
							</td>
							<td class="segPanel" align="center" valign="middle">
								{{$sDate}}{{$sCalendarIcon}}
							</td>
						</tbody>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<table width="100%">
					<tr>
						<td width="50%" align="left">
							{{$sBtnAddItem}}
							{{$sBtnEmptyList}}
							{{$sBtnPDF}}
						</td>
					<!--<td align="right">
							{{$sContinueButton}}
							{{$sBreakButton}}
						</td>-->
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table id="order-list" class="jedList" border="0" cellpadding="0" cellspacing="0" width="100%">
						<thead>
							<tr id="order-list-header">
								<th width="1%" nowrap="nowrap">&nbsp;</th>
								<th width="10%" nowrap="nowrap" align="left">Code</th>
								<th width="*" nowrap="nowrap" align="left">Description</th>
								<th width="4%" nowrap="nowrap" align="center">Packaging</th>
								<th width="10%" align="center" nowrap="nowrap">Unit Cost</th>
								<th width="10%" align="right" nowrap="nowrap">Cost</th>
								<th width="10%" align="right" nowrap="nowrap">Total</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="7">Order list is currently empty...</td>
							</tr>
						</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td>
					<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
					<tbody>
						<tr>
					<td width="*" align="right" style="background-color:#ffffff; padding:4px" height=""><strong>Sub-Total</strong></th>
					<td id="show-sub-total" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Verdana; font-size:15px; font-weight:bold"></th>

				</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Discount</strong></th>
					<td id="show-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Verdana; font-size:15px; font-weight:bold"></th>
				</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Net Total</strong></th>
					<td id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Verdana; font-size:15px; font-weight:bold"></th>

						</tr>
					</tbody>
				</table>
			</td>
		</tr>
{{$jsCalendarSetup}}		
{{$sFormEnd}}
		<tr>
			<table width="100%">
				<tr>
					<td align="right">
						{{$sContinueButton}}
					</td>	
					<td></td>
					<td align="left">
						{{$sBreakButton}}
					</td>	
				</tr>
			</table>
		</tr>
	</table>