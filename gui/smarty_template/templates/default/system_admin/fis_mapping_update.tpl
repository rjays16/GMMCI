{{$form_start}}
<div style="width:780px" align="center">
	<table border="0" cellspacing="1" cellpadding="0" width="80%" align="center" style="">
		<tbody>
			<tr>
				<td colspan="4" class="segPanelHeader">Create or Update Items account in accounting</td>
			</tr>
			<tr>
				<td class="segPanel">
					<table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
						<tbody>
							<tr>
								<td width="25%" align="right"><strong>Name</strong>&nbsp;</td>
								<td align="left">{{$sItemName}}</td>
							</tr>
							<tr>
								<td width="25%" align="right"><strong>Cost Center Area</strong>&nbsp;</td>
								<td align="left">{{$sCostArea}}</td>
							</tr>
							<tr>
								<td width="25%" align="right"><strong>Account transaction</strong>&nbsp;</td>
								<td align="left">{{$sTransaction}}</td>
							</tr>

							{{$sDebit}}
							{{$sDebitAccount}}
							
							{{$sCredit}}
							{{$sCreditAccount}}
							
							{{$sIncome}}
							{{$sIncomeAccount}}
							
							{{$sCash}}
							{{$sCashAccount}}
							
							{{$sTax}}
							{{$sTaxAccount}}
							
							{{$sInventory}}
							{{$sInventoryAccount}}
							
							{{$sCOGS}}
							{{$sCOGSCredit}}
							<tr>
								<td align="right">{{$sbtnsave}}</td>
								<td align="left">{{$sbtnCancel}}</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>


{{$form_end}}