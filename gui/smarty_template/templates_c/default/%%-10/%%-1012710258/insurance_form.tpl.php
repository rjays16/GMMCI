<?php /* Smarty version 2.6.0, created on 2018-04-24 13:17:50
         compiled from registration_admission/insurance_form.tpl */ ?>
	<form method="post" action="<?php echo $this->_tpl_vars['thisfile']; ?>
" name="aufnahmeform" onSubmit="return chkform(this)">
		<table border="0" cellspacing="1" cellpadding="1" width="100%">

				<!-- The insurance class  -->
			 <?php if ($this->_tpl_vars['LDBillType']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDBillType']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sBillTypeInput']; ?>
&nbsp;&nbsp;<span name="iconIns" id="iconIns" style="display:none"><?php echo $this->_tpl_vars['sBtnAddItem']; ?>
</span>
					</td>
					<!--<td><?php echo $this->_tpl_vars['sBtnAddItem']; ?>
</td>-->
				</tr>
			 <?php endif; ?>
				<!-- edited 03-06-07------------->
				
			 <?php if ($this->_tpl_vars['LDInsuranceNr']): ?>
				<tr>
					<td class="adm_item" width="20%">
						Registered <?php echo $this->_tpl_vars['LDInsuranceNr']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<!--<?php echo $this->_tpl_vars['insurance_nr']; ?>
-->
						<!-- -->
						
						<table id="reg-order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
							<thead>
									<tr id="reg-order-list-header">
											<th width="4%" nowrap></th>
											<th width="*" nowrap align="left">&nbsp;&nbsp;Insurance Company</th>
											<th width="20%" nowrap align="right">&nbsp;&nbsp;Insurance No.</th>
											<th width="18%" nowrap align="right">&nbsp;&nbsp;Principal Holder</th>
											<th width="1"></th>
									</tr>
							</thead>
							<tbody>
								<?php echo $this->_tpl_vars['sOrderItemsreg']; ?>

							
						</table>
						
						<!-- -->
					</td>
					
				</tr>
				<tr>
					<td colspan="3" class="adm_item">&nbsp;</td>
				</tr>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDInsuranceNr']; ?>
 to be used:
					</td>
					<td colspan=2 class="adm_input">
						<!--<?php echo $this->_tpl_vars['insurance_nr']; ?>
-->
						<!-- -->
						
						<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
							<thead>
									<tr id="order-list-header">
											<th width="4%" nowrap></th>
											<th width="*" nowrap align="left">&nbsp;&nbsp;Insurance Company</th>
											<th width="20%" nowrap align="right">&nbsp;&nbsp;Insurance No.</th>
											<th width="18%" nowrap align="right">&nbsp;&nbsp;Principal Holder</th>
											<th width="1"></th>
									</tr>
							</thead>
							<tbody>
								<?php echo $this->_tpl_vars['sOrderItems']; ?>

							
						</table>
						
						<!-- -->
					</td>
					
				</tr>
			  <?php endif; ?>
				
				<?php echo $this->_tpl_vars['sHiddenInputs']; ?>


				<tr>
					<td colspan="3">&nbsp;
						
				  </td>
				</tr>
				<tr>
					<td>
						<?php echo $this->_tpl_vars['pbSave']; ?>

					</td>
					<td align="right">&nbsp;
						
					</td>
					<td align="right">
						<?php echo $this->_tpl_vars['pbCancel']; ?>
					</td>
				</tr>

		</table>
	
			<?php echo $this->_tpl_vars['sErrorHidInputs']; ?>

			<?php echo $this->_tpl_vars['sUpdateHidInputs']; ?>

	
	</form>