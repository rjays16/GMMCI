<?php /* Smarty version 2.6.0, created on 2018-04-30 12:25:02
         compiled from cashier/cashier_main.tpl */ ?>
<div align="center">
<?php echo $this->_tpl_vars['sFormStart']; ?>

<div style="width:98%">
	<div style="width:100%">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cashier/gui_cashier_info.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
	<div style="width:100%;margin-top:5px">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td width="50%">&nbsp;
			</td>
				<td align="right">
					<?php echo $this->_tpl_vars['sContinueButton'];  echo $this->_tpl_vars['sBreakButton']; ?>

				</td>
		</tr>
	</table>
</div>
	<div style="width:100%; padding:4px;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top">

	<ul id="request-tabs" class="segTab" style="padding-left:10px;">
						<li id="tab_request" <?php if ($this->_tpl_vars['bTabRequest']): ?>class="segActiveTab"<?php endif; ?> onclick="tabClick(this)" segTab="requests">
			<h2 class="segTabText">Requests</h2>
		</li>
						<li id="tab_billing" <?php if ($this->_tpl_vars['bTabBilling']): ?>class="segActiveTab"<?php endif; ?> onclick="tabClick(this)" segTab="billing">
			<h2 class="segTabText">Billing</h2>
		</li>
						<li id="tab_deposit" <?php if ($this->_tpl_vars['bTabDeposit']): ?>class="segActiveTab"<?php endif; ?> onclick="tabClick(this)" segTab="deposit">
			<h2 class="segTabText">Deposits</h2>
		</li>
						<li id="tab_other" <?php if ($this->_tpl_vars['bTabOther']): ?>class="segActiveTab"<?php endif; ?> onclick="tabClick(this)" segTab="other">
			<h2 class="segTabText">Other Payments</h2>
		</li>
	</ul>

					<div class="" style="width:100%; border-top:2px solid #4e8ccf">

		<div id="requests" style="padding:2px;padding-top:3px;<?php if (! $this->_tpl_vars['bTabRequest']): ?>display:none<?php endif; ?>">
							<div align="left" style="margin:2px;">
				<?php echo $this->_tpl_vars['sRequestAdd']; ?>

			<?php echo $this->_tpl_vars['sRequestAddSocial']; ?>

				<?php echo $this->_tpl_vars['sRequestClearAll']; ?>

			</div>
							<div id="request_dashlet" style="margin-top:15px">
	<?php echo $this->_tpl_vars['sRequests']; ?>

		</div>
						</div>


		<div id="billing" style="padding:2px;padding-top:3px;<?php if (! $this->_tpl_vars['bTabBilling']): ?>display:none<?php endif; ?>">
			<div align="left" style="margin:2px; color:#888888">
				<?php echo $this->_tpl_vars['sBillingAdd'];  echo $this->_tpl_vars['sBillingRemove'];  echo $this->_tpl_vars['sBillingClear']; ?>

			</div>
							<!-- hospital bills dashlet -->
							<div id="fb_dashlet" class="dashlet" style="margin-top:15px">
								<table class="dashletHeader" cellpadding="0" cellspacing="0">
									<tr>
										<td width="1%"></td>
										<td width="1%" nowrap="nowrap">
											<h1>Hospital bills</h1>
										</td>
										<td></td>
									</tr>
								</table>
								<div id="fb_dashlet_content">
			<input name="requests[]" type="hidden" srcDept="fb" refNo="0000000000" value="fb0000000000"/>
			<input name="iscash[]" type="hidden" srcDept="fb" refNo="0000000000" value="1"/>
			<table id="list_fb0000000000" class="segList" border="0" cellpadding="0" cellspacing="0" style="width:100%;">
				<thead>
					<tr id="row_fb0000000000">
						<th width="3%" class="centerAlign"><input type="checkbox" onchange="flagCheckBoxesByName('fb0000000000[]',this.checked); calcSubTotal('other','0000000000')" checked="checked"></th>
						<th align="left" width="10%" nowrap>Item No</th>
						<th align="left" width="*" nowrap="nowrap">Item Description</th>
						<th align="right" width="9%" nowrap="nowrap" style="font-size:90%">Price/item (Orig)</th>
						<th align="right" width="9%" nowrap="nowrap" style="font-size:90%">Price/item (Adj)</th>
						<th align="right" width="9%" nowrap="nowrap">Quantity</th>
						<th align="right" width="9%" nowrap="nowrap">Price (Orig)</th>
						<th align="right" width="9%" nowrap="nowrap" >Price (Adj)</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
	<?php echo $this->_tpl_vars['sBillingList']; ?>

				</tbody>
				<tfoot>
					<tr>
						<th align="left" nowrap="nowrap"><img class="segSimulatedLink" src="../../images/cashier_up_small.gif" align="absmiddle" onclick="toggleTBody('list_fb0000000000')" /></th>
						<th align="left" colspan="3" nowrap="nowrap">Items (<span id="items_fb0000000000">0</span>)</th>
						<th align="right" nowrap="nowrap">Orig Subtotal:</th>
						<th align="left" nowrap="nowrap" style="font-weight:normal">
							<input type="hidden" id="subtotal_orig_fb0000000000" name="subtotal_orig_fb0000000000" value="0"/>
							<span style="" id="show_subtotal_orig_fb0000000000">0.00</span>
						</th>
						<th align="right" nowrap="nowrap">Adj Subtotal:</th>
						<th align="left" nowrap="nowrap" style="font-weight:normal" colspan="2">
							<input type="hidden" id="charity_fb0000000000" name="charity_fb0000000000" value="-1"/>
							<input type="hidden" id="charity_icon_fb0000000000" disabled="disabled"/>
							<input type="hidden" id="subtotal_fb0000000000" name="subtotal_fb0000000000" value="0.00"/>
							<span style="" id="show_subtotal_fb0000000000">0.00</span>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
							</div>
						</div>

		<div id="deposit" style="padding:2px;padding-top:3px;<?php if (! $this->_tpl_vars['bTabDeposit']): ?>display:none<?php endif; ?>">
			<div align="left" style="margin:2px; color:#888888">
                <!--added <?php echo $this->_tpl_vars['sHospitalServiceOB']; ?>
 by jasper 08/29/2013 -Fix for co-payments ob annex Bug#279-->
				<?php echo $this->_tpl_vars['sDepositAdd'];  echo $this->_tpl_vars['sPartialAdd'];  echo $this->_tpl_vars['sHospitalServiceOB'];  echo $this->_tpl_vars['sDepositClear']; ?>

			</div>
							<!-- deposit dashlet -->
							<div id="deposit_dashlet" class="dashlet" style="margin-top:15px">
								<table class="dashletHeader" cellpadding="0" cellspacing="0">
									<tr>
										<td width="1" nowrap="nowrap">
											<h1>Deposits</h1>
										</td>
									</tr>
								</table>
								<div id="deposit_dashlet_content">
			<input name="requests[]" type="hidden" srcDept="pp" refNo="0000000000" value="pp0000000000"/>
			<input name="iscash[]" type="hidden" srcDept="pp" refNo="0000000000" value="1"/>
									<table id="list_pp0000000000" class="segList" border="0" cellpadding="0" cellspacing="0" style="width:100%;">
				<thead>
					<tr id="row_pp0000000000">
						<th width="3%" class="centerAlign"><input type="checkbox" onchange="flagCheckBoxesByName('pp0000000000[]',this.checked); calcSubTotal('pp','0000000000')" checked="checked"></th>
						<th align="left" width="10%" nowrap>Item No</th>
						<th align="left" width="*" nowrap="nowrap">Item Description</th>
						<th align="right" width="9%" nowrap="nowrap" style="font-size:90%">Price/item (Orig)</th>
						<th align="right" width="9%" nowrap="nowrap" style="font-size:90%">Price/item (Adj)</th>
						<th align="right" width="9%" nowrap="nowrap">Quantity</th>
						<th align="right" width="9%" nowrap="nowrap">Price (Orig)</th>
						<th align="right" width="9%" nowrap="nowrap" >Price (Adj)</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
	<?php echo $this->_tpl_vars['sDepositList']; ?>

				</tbody>
				<tfoot>
					<tr>
						<th align="left" nowrap="nowrap"><img class="segSimulatedLink" src="../../images/cashier_up_small.gif" align="absmiddle" onclick="toggleTBody('list_pp0000000000')" /></th>
						<th align="left" colspan="3" nowrap="nowrap">Items (<span id="items_pp0000000000">0</span>)</th>
						<th align="right" nowrap="nowrap">Orig Subtotal:</th>
						<th align="left" nowrap="nowrap" style="font-weight:normal">
							<input type="hidden" id="subtotal_orig_pp0000000000" name="subtotal_orig_pp0000000000" value="0"/>
							<span style="" id="show_subtotal_orig_pp0000000000">0.00</span>
						</th>
						<th align="right" nowrap="nowrap">Adj Subtotal:</th>
						<th align="left" nowrap="nowrap" colspan="2" style="font-weight:normal">
							<input type="hidden" id="charity_pp0000000000" name="charity_pp0000000000" value="-1"/>
							<input type="hidden" id="charity_icon_pp0000000000" disabled="disabled"/>
							<input type="hidden" id="subtotal_pp0000000000" name="subtotal_pp0000000000" value="0.00"/>
							<span style="" id="show_subtotal_pp0000000000">0.00</span>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
							</div>
						</div>


		<div id="other" style="padding:2px;padding-top:3px;<?php if (! $this->_tpl_vars['bTabOther']): ?>display:none<?php endif; ?>">
			<div align="left" style="margin:2px; color:#888888">
				<?php echo $this->_tpl_vars['sHospitalServiceConsultation'];  echo $this->_tpl_vars['sHospitalServiceOrtho'];  echo $this->_tpl_vars['sHospitalServiceENT'];  echo $this->_tpl_vars['sHospitalServiceDental'];  echo $this->_tpl_vars['sHospitalServicePTOT'];  echo $this->_tpl_vars['sHospitalServicePedia'];  echo $this->_tpl_vars['sHospitalServiceSpecialLab'];  echo $this->_tpl_vars['sHospitalServiceAdd']; ?>

				<?php echo $this->_tpl_vars['sHospitalServiceRemove']; ?>
|
				<?php echo $this->_tpl_vars['sHospitalServiceClear']; ?>

			</div>
							<!-- other payments dashlet -->
							<div id="other_dashlet" class="dashlet" style="margin-top:15px">
								<table class="dashletHeader" cellpadding="0" cellspacing="0">
									<tr>
										<td width="1" nowrap="nowrap">
											<h1>Other payments</h1>
										</td>
									</tr>
								</table>
								<div id="other_dashlet_content">
			<input name="requests[]" type="hidden" srcDept="other" refNo="0000000000" value="other0000000000"/>
			<input name="iscash[]" type="hidden" srcDept="other" refNo="0000000000" value="1"/>
									<table id="list_other0000000000" class="segList" border="0" cellpadding="0" cellspacing="0" style="width:100%;">
				<thead>
					<tr id="row_other0000000000">
						<th width="3%" class="centerAlign"><input type="checkbox" onchange="flagCheckBoxesByName('other0000000000[]',this.checked); calcSubTotal('other','0000000000')" checked="checked"></th>
						<th align="left" width="10%" nowrap>Item No</th>
						<th align="left" width="*" nowrap="nowrap">Item Description</th>
						<th align="right" width="9%" nowrap="nowrap" style="font-size:90%">Price/item (Orig)</th>
						<th align="right" width="9%" nowrap="nowrap" style="font-size:90%">Price/item (Adj)</th>
						<th align="right" width="9%" nowrap="nowrap">Quantity</th>
						<th align="right" width="9%" nowrap="nowrap">Price (Orig)</th>
						<th align="right" width="9%" nowrap="nowrap" >Price (Adj)</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
	<?php echo $this->_tpl_vars['sOtherHospitalServices']; ?>

				</tbody>
				<tfoot>
					<tr>
						<th align="left" nowrap="nowrap"><img class="segSimulatedLink" src="../../images/cashier_up_small.gif" align="absmiddle" onclick="toggleTBody('list_other0000000000')" /></th>
						<th align="left" colspan="3" nowrap="nowrap">Items (<span id="items_other0000000000">0</span>)</th>
						<th align="right" nowrap="nowrap">Orig Subtotal:</th>
						<th align="left" nowrap="nowrap" style="font-weight:normal">
							<input type="hidden" id="subtotal_orig_other0000000000" name="subtotal_orig_other0000000000" value="0"/>
							<span style="" id="show_subtotal_orig_other0000000000">0.00</span>
						</th>
						<th align="right" nowrap="nowrap">Adj Subtotal:</th>
						<th align="left" nowrap="nowrap" style="font-weight:normal" colspan="2">
							<input type="hidden" id="charity_other0000000000" name="charity_other0000000000" value="-1"/>
							<input type="hidden" id="charity_icon_other0000000000" disabled="disabled"/>
							<input type="hidden" id="subtotal_other0000000000" name="subtotal_other0000000000" value="0.00"/>
							<span style="" id="show_subtotal_other0000000000">0.00</span>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

						</div>


					</div>

	</td>
	<td width="5"></td>
	<td width="120" valign="top">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "cashier/gui_totals.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
	</table>
</div>
	</div>
</div>

<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup1']; ?>

<br/>
<img src="" vspace="2" width="1" height="1"><br/>
<?php echo $this->_tpl_vars['sDiscountControls']; ?>

<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>
