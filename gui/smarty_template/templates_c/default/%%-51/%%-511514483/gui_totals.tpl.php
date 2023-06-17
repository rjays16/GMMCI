<?php /* Smarty version 2.6.0, created on 2018-04-30 12:25:03
         compiled from cashier/gui_totals.tpl */ ?>

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
</style>

<script type="text/javascript">

function clickAmountTendered() {
	$('show-amt-tendered').style.display = 'none';
	$('amount_tendered').style.display = '';
	$('amount_tendered').focus();
}

function clickDiscountTendered() {
	$('show-discount-total').style.display = 'none';
	$('discount_tendered').style.display = '';
	$('discount_tendered').focus();
}

function saveDiscountTendered() {
	$('show-discount-total').style.display = '';
	$('discount_tendered').style.display = 'none';
	$('discount_tendered').blur();
	//amtTenderedOnBlurFocusHandle($('amount_tendered'));
	return false;
}

function saveAmountTendered() {
	$('show-amt-tendered').style.display = '';
	$('amount_tendered').style.display = 'none';
	$('amount_tendered').blur();
	//amtTenderedOnBlurFocusHandle($('amount_tendered'));
	return false;
}
</script>
<table width="100%" style="font-size: 12px;" border="0" cellspacing="2" cellpadding="1">
	<tbody>
		<tr>
			<td width="20%" align="left" class="segPanelHeader" ><strong>Sub-Total</strong></td>
		</tr>
		<tr>
			<td style="background-color:#e0e0e0;margin:1px 10px;text-align:right"><span id="show-sub-total"	class="displayTotals" style="color:#000000;" <?php if ($this->_tpl_vars['sGUIvSubTotal']): ?>value="<?php echo $this->_tpl_vars['sGUIvSubTotal']; ?>
"<?php else: ?>value="0"<?php endif; ?>><?php echo $this->_tpl_vars['sGUISubTotal']; ?>
</span></td>
		</tr>
		<tr>
			<td width="20%" align="left" class="segPanelHeader" ><strong>Discount</strong></td>
		</tr>
		<tr>
			<td style="background-color:#d0d0d0;margin:1px 10px;text-align:right; border:1px solid #808080">
				<span id="show-discount-total" class="displayTotalsLink" style="color:#006600;" <?php if ($this->_tpl_vars['sGUIvDiscountTotal']): ?>value="<?php echo $this->_tpl_vars['sGUIvDiscountTotal']; ?>
"<?php else: ?>value="0"<?php endif; ?> onclick="clickDiscountTendered()"><?php echo $this->_tpl_vars['sGUIDiscountTotal']; ?>
</span>
			<input class="displayTotals" id="discount_tendered" name="discount_tendered" type="text" value="<?php echo $this->_tpl_vars['discountTendered']; ?>
"
					onfocus="discountOnBlurFocusHandle(this);this.select();"
					onblur="saveDiscountTendered();discountOnBlurFocusHandle(this);"
					onkeyup="if (event.keyCode==13) this.blur(); return false;"
					style="margin:0;padding:0;width:100%;display:none;"/>
			</td>
		</tr>
		<tr id="vatable_container" style="display:none">
			<td width="20%" align="left" class="segPanelHeader"><strong>Vatable Amount</strong></td>
		</tr>
		<tr id="vatable_container1" style="display:none">
			<td style="background-color:#c0c0c0;margin:1px 10px;text-align:right"><span id="show-vatable-total" class="displayTotals" style="color:#000066" <?php if ($this->_tpl_vars['sGUIvVATableTotal']): ?>value="<?php echo $this->_tpl_vars['sGUIvVATableTotal']; ?>
"<?php else: ?>value="0"<?php endif; ?>><?php echo $this->_tpl_vars['sGUIVATableTotal']; ?>
</span></td>
		</tr>
		<tr id="vat_container" style="display:none">
			<td width="20%" align="left" class="segPanelHeader"><strong>VAT</strong></td>
		</tr>
		<tr id="vat_container1" style="display:none">
			<td style="background-color:#c0c0c0;margin:1px 10px;text-align:right"><span id="show-vat-total" class="displayTotals" style="color:#000066" <?php if ($this->_tpl_vars['sGUIvVATTotal']): ?>value="<?php echo $this->_tpl_vars['sGUIvVATTotal']; ?>
"<?php else: ?>value="0"<?php endif; ?>><?php echo $this->_tpl_vars['sGUIVATTotal']; ?>
</span><input type="hidden" id="vat_amount" name="vat_amount" value="0" /></td>
		</tr>
		<tr>
			<td width="20%" align="left" class="segPanelHeader"><strong>Net Total</strong></td>
		</tr>
		<tr>
			<td style="background-color:#c0c0c0;margin:1px 10px;text-align:right"><span id="show-net-total" class="displayTotals" style="color:#000066" <?php if ($this->_tpl_vars['sGUIvNetTotal']): ?>value="<?php echo $this->_tpl_vars['sGUIvNetTotal']; ?>
"<?php else: ?>value="0"<?php endif; ?>><?php echo $this->_tpl_vars['sGUINetTotal']; ?>
</span></td>
		</tr>

		<tr>
			<td width="20%" align="left" class="segPanelHeader"><strong>Amt Tendered</strong></td>
		</tr>
		<tr>
			<td style="background-color:#b0b0b0;margin:1px 10px;text-align:right; border:1px solid #808080">
				<span id="show-amt-tendered" class="displayTotalsLink" style="color:#0000ff;display:block" <?php if ($this->_tpl_vars['sGUIvAmtTendered']): ?>value="<?php echo $this->_tpl_vars['sGUIvAmtTendered']; ?>
"<?php else: ?>value="0"<?php endif; ?> onclick="clickAmountTendered()"><?php echo $this->_tpl_vars['sGUIAmtTendered']; ?>
</span>
				<input class="displayTotals" id="amount_tendered" name="amount_tendered" type="text" value="<?php echo $this->_tpl_vars['sAmtTendered']; ?>
"
					onfocus="amtTenderedOnBlurFocusHandle(this);this.select();"
					onblur="saveAmountTendered();amtTenderedOnBlurFocusHandle(this);$('process-btn').onclick();"
					onkeyup="if (event.keyCode==13) this.blur(); return false;"
					style="margin:0;padding:0;width:100%;display:none;"/>
				<input type="hidden" id="partial_amount" name="partial_amount">
			</td>
		</tr>

		<tr>
			<td width="19%" align="left" class="segPanelHeader"><strong>Change</strong></td>
		</tr>
		<tr>
			<td style="background-color:#ffffff;margin:1px 10px;text-align:right;border:1px solid #cccccc"><span id="show-change" class="displayTotals" style="color:#000066;" <?php if ($this->_tpl_vars['sGUIvChange']): ?>value="<?php echo $this->_tpl_vars['sGUIvChange']; ?>
"<?php else: ?>value="0"<?php endif; ?>><?php echo $this->_tpl_vars['sGUIChange']; ?>
</span></td>
		</tr>

	</tbody>
</table>