<?php /* Smarty version 2.6.0, created on 2018-01-25 13:53:29
         compiled from dialysis/misc_request_tray.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_radios', 'dialysis/misc_request_tray.tpl', 44, false),)), $this); ?>
<style>
	* {
		font: normal 12px Arial;
	}
	table.transaction_details_table {
		border-collapse: collapse;

	}
	table.transaction_details_table tr td {
		/**border: 1px #000000 solid;    **/
	}
	table.transaction_details_table table tr td {
		border: none;
	}
	#transaction_date_display {
		background: #FFFFFF;
		padding: 3px;
		border: 1px #99BBE8 solid;
		width: 170px;
	}
	.date_time_picker {
		cursor: pointer;
	}
</style>
<div>
<?php echo $this->_tpl_vars['form_start']; ?>

<div style="width:670px; margin-top:10px" align="center">
	<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%">
		<tbody>
			<tr>
				<td class="segPanelHeader" width="*">Request Details</td>
			</tr>
			<tr>
				<td class="segPanel" align="left" valign="top">
					<table  width="100%" class="transaction_details_table" cellpadding="3" cellspacing="0" style="font:normal 12px Arial; padding:4px" >
						<tr>
							<td width="40%">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td width="30%" align="right" nowrap="nowrap"><strong>Type:</strong></td>
										<!--<td><?php echo $this->_tpl_vars['transaction_type'];  echo $this->_tpl_vars['sChargeTyp']; ?>
</td>-->
										<td>
											<div style="font:bold 18px Arial; color:#006000">
												<?php echo smarty_function_html_radios(array('name' => 'transaction_type','options' => $this->_tpl_vars['transaction_types'],'selected' => $this->_tpl_vars['transaction_type'],'id' => 'transaction_type'), $this);?>

                                                <?php echo $this->_tpl_vars['sIsCash']; ?>

                                                <?php echo $this->_tpl_vars['sIsCharge']; ?>

											</div>
										</td>
									</tr>
									<tr>
										<td width="30%" align="right" nowrap="nowrap"><strong>Name:</strong></td>
										<td>&nbsp;&nbsp;<?php echo $this->_tpl_vars['patient_name']; ?>
</td>
									</tr>
									<tr>
										<td width="30%" align="right" nowrap="nowrap"><strong>Patient Type:</strong></td>
										<td>&nbsp;&nbsp;<?php echo $this->_tpl_vars['encounter_type']; ?>
</td>
									</tr>
                                    <tr>
                                        <td width="30%" align="right" nowrap="nowrap"><strong>Classification:</strong></td>
                                        <td>&nbsp;&nbsp;<?php echo $this->_tpl_vars['sClassification']; ?>
</td>
                                    </tr>
								</table>
							</td>
							<td>
								<table width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td width="30%" align="right" nowrap="nowrap"><strong>Reference No:</strong></td>
										<td>&nbsp;<?php echo $this->_tpl_vars['reference_no']; ?>
</td>
									</tr>
									<tr>
										<td align="right"><b>Request Date:</b></td>
										<td>
											<table cellpadding="0" cellspacing="2">
												<tr>
													<td valign="bottom"><?php echo $this->_tpl_vars['transaction_date_display']; ?>
</td>
													<td><?php echo $this->_tpl_vars['transaction_date_picker'];  echo $this->_tpl_vars['transaction_date'];  echo $this->_tpl_vars['transaction_date_calendar_script']; ?>
</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<div id="or_main_schedule" align="left">
	<br/>
	<fieldset>
		<legend>Miscellaneous Charges</legend>
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td align="right" colspan="2"><?php echo $this->_tpl_vars['add_misc_btn']; ?>
&nbsp;<?php echo $this->_tpl_vars['empty_misc_btn']; ?>
</td>
			</tr>
		</table>
		<table class="segList" width="100%" id="misc_list" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th width="1%" nowrap="nowrap"></th>
					<th align="center">Code</th>
					<th align="center">Item Description</th>
					<th align="center">Quantity</th>
					<th align="center">Unit Price</th>
					<th align="center">Net Price</th>
				</tr>
			</thead>
			<tbody>
				<tr id="empty_misc_row"><td colspan="8">Miscellaneous charges is empty...</td></tr>
			</tbody>
		</table>
		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
		<tbody>
			<tr>
				<td width="*" align="right" style="padding:4px" height=""><strong>Sub-Total</strong></th>
				<td id="misc_subtotal" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold"></td>
			</tr>
			<tr>
				<td align="right" style="padding:4px"><strong>Discount</strong></th>
				<td id="misc_discount_total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold"></td>
			</tr>
			<tr>
				<td align="right" style="padding:4px"><strong>Net Total</strong></th>
				<td id="misc_net_total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold"></td>
			</tr>
		 </tbody>
	</table>
	</fieldset>
	</div>
    <?php echo $this->_tpl_vars['sBtnDiscounts']; ?>

<div id="or_main_schedule">
<?php echo $this->_tpl_vars['other_charges_submit']; ?>

<?php echo $this->_tpl_vars['other_charges_cancel']; ?>

</div>
<?php echo $this->_tpl_vars['pid']; ?>

<?php echo $this->_tpl_vars['transaction_type']; ?>
 
<?php echo $this->_tpl_vars['view_from']; ?>

<?php echo $this->_tpl_vars['discount']; ?>

<?php echo $this->_tpl_vars['encounter_nr']; ?>

<?php echo $this->_tpl_vars['impression']; ?>

<?php echo $this->_tpl_vars['submitted']; ?>

<?php echo $this->_tpl_vars['mode']; ?>

<?php echo $this->_tpl_vars['area']; ?>

<?php echo $this->_tpl_vars['userid']; ?>

<?php echo $this->_tpl_vars['create_dt']; ?>

<?php echo $this->_tpl_vars['nonSocialDiscount']; ?>

<!--<?php echo $this->_tpl_vars['refno']; ?>
-->
<?php echo $this->_tpl_vars['form_end']; ?>


</div>

