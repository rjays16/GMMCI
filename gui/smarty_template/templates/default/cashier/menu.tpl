{{* retail.tpl  Submenu rows template for Segworks retail module (pharmacy) *}}


<tr>
	<TD class="submenu_title" colspan=3>Processing</TD>
</tr>
{{$LDSegCashierRequests}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegCashierBilling}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegCashierList}}
<tr>
	<TD class="submenu_title" colspan=3>Deposits</TD>
</tr>
{{$LDSegCashierNewDeposit}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegCashierManageDeposit}}