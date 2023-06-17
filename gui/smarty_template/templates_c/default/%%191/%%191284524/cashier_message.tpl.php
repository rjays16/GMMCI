<?php /* Smarty version 2.6.0, created on 2018-04-30 12:32:26
         compiled from cashier/cashier_message.tpl */ ?>
<script type="text/javascript">
	function openWindow(url) {
		window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}
	
	function draftPrint(url) {
		if (confirm('Print this OR?')) {
		return overlib(OLiframeContent(url, 250, 60, 'or-draft', 0, 'no'),
			WIDTH,250, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
			CAPTION,'Printing receipt', 
				REF,'print-draft', REFC,'LR', REFP, 'UR', REFY,4, REFX,25,
				STATUS,'Draft printing OR...');
		}
	}
	function draftPrint2(url) {
		if (confirm('Print this Cash Invoice?')) {
		return overlib(OLiframeContent(url, 250, 60, 'or-draft', 0, 'no'),
			WIDTH,250, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
			CAPTION,'Printing receipt', 
				REF,'print-draft', REFC,'LR', REFP, 'UR', REFY,4, REFX,25,
				STATUS,'Draft printing OR...');
		}
	}



jQuery(function($) {
	$('#btn-print-draft').click(function(e){
		e.preventDefault();
		draftPrint('<?php echo $this->_tpl_vars['draftPrintURL']; ?>
');
	});
	
	$('#btn-launch-director').click(function(e){
		e.preventDefault();
		draftPrint('<?php echo $this->_tpl_vars['directorURL']; ?>
');
	});

	$('#btn-launch-cashinvoice').click(function(e){
		e.preventDefault();
		draftPrint2('<?php echo $this->_tpl_vars['draftPrintUrlCI']; ?>
');
	});
});

</script>

<div style="text-align:center;width:95%;margin-top:10px">


	<div style="display:none">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cashier/gui_totals.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
	
	<div align="right">
		<?php echo $this->_tpl_vars['CashInvoice']; ?>


		<button style="cursor:pointer" id="btn-print-draft" class="segButton"><img src="../../gui/img/common/default/printer.png" />Print OR</button>
         
       <?php echo $this->_tpl_vars['ORprint']; ?>

       
	</div>

	<table width="100%" border="0" cellspacing="5" cellpadding="0">
		<tr>
			<td width="40%" valign="top">
				<div class="dashlet" align="left" style="width:100%">		
					<table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="99%" nowrap="nowrap"><h1>Payment details</h1></td>
							<td>								
							</td>
						</tr>
					</table>
					<table border="0" cellpadding="2" cellspacing="2" width="100%" style="border:2px solid #cccccc">
						<tbody>
							<tr>
								<td class="segPanel" width="45%">
									<strong>O.R. No.</strong>
								</td>
								<td class="segPanel3" style="color:#008000;font:bold 14px Arial"><?php echo $this->_tpl_vars['sORNo']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Date</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sORDate']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Payor</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sORName']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>HRN</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sPID']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Address</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sORAddress']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>PRICE</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sPrice']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>VAT</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sVAT']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>TOTAL</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sAmountDue']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Tendered</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sAmountTendered']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Discount</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sDiscountTendered']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Change</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sAmountChange']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Remarks</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sRemarks']; ?>
</td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php if ($this->_tpl_vars['sUseCheck']): ?>
				<div class="dashlet" align="left" style="width:100%">		
					<table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="99%" nowrap="nowrap"><h1>Check information</h1></td>
							<td>								
							</td>
						</tr>
					</table>
					<table border="0" cellpadding="2" cellspacing="2" width="100%" style="border:2px solid #cccccc">
						<tbody>
							<tr>
								<td class="segPanel" width="45%">
									<strong>Check No.</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sCheckNo']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Check Date</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sCheckDate']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Bank Name</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sCheckBank']; ?>
</td>
							</tr>
							<!-- Added by Jarel 09/25/2013 -->
							<tr>
								<td class="segPanel">
									<strong>Company Name</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sCompanyName']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Check Name</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sCheckName']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Amount</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sCheckAmount']; ?>
</td>
							</tr>
						</tbody>
					</table>
				</div> <!-- Check information dashlet -->
				<?php endif; ?>				
				<?php if ($this->_tpl_vars['sUseCard']): ?>
				<div class="dashlet" align="left" style="width:100%">		
					<table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="99%" nowrap="nowrap"><h1>Card information</h1></td>
							<td>
							</td>
						</tr>
					</table>
					<table border="0" cellpadding="2" cellspacing="2" width="100%" style="border:2px solid #cccccc">
						<tbody>
							<tr>
								<td class="segPanel" width="45%">
									<strong>Card No.</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sCardNo']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Issuing Bank</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sCardBank']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Card Brand</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sCardBrand']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Cardholder</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sCardName']; ?>
</td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Amount</strong>
								</td>
								<td class="segPanel3"><?php echo $this->_tpl_vars['sORAddress']; ?>
</td>
							</tr>
						</tbody>
					</table>
				</div> <!-- Card information dashlet -->
				<?php endif; ?>
			</td>
			<td width="*" valign="top">
				<div class="dashlet" align="left" style="width:100%">		
					<table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="99%" nowrap="nowrap"><h1>Particulars</h1></td>
							<td></td>
						</tr>
					</table>
					<?php echo $this->_tpl_vars['sItemList']; ?>

				</div>
			</td>
		</tr>
	</table>
</div>
<br />