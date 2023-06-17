<?php /* Smarty version 2.6.0, created on 2017-11-23 21:40:54
         compiled from dependents/form_new.tpl */ ?>
<div align="center" style="font:bold 12px Tahoma; color:#990000; "><?php echo $this->_tpl_vars['sWarning']; ?>
</div><br />

<?php echo $this->_tpl_vars['sFormStart']; ?>

	<table border="0" cellspacing="2" cellpadding="2" width="95%" align="center">
		<tbody>
			<tr>
				<td class="segPanelHeader" width="*">
					Personal Information
				</td>
			</tr>
			<tr>
				<td rowspan="3" class="segPanel" align="center" valign="top">
				  <table width="100%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
				  	<tr>
					<td width="60%">	
					<table width="100%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
						<tr>
							<td valign="top" width="20%"><strong>Name</strong></td>
							<td width="1" valign="middle">
								<?php echo $this->_tpl_vars['sOrderName']; ?>

							</td>
						</tr>
						<tr>
							<td valign="top"><strong>Hospital No.</strong></td>
							<td width="1" valign="middle">
								<?php echo $this->_tpl_vars['sOrderPID']; ?>

							</td>
						</tr>
						<tr>
							<td valign="top"><strong>Member ID.</strong></td>
							<td width="1" valign="middle">
								<?php echo $this->_tpl_vars['sOrderMemberID']; ?>

							</td>
						</tr>
						<tr>
							<td valign="top"><strong>Address</strong></td>
							<td><?php echo $this->_tpl_vars['sOrderAddress']; ?>
</td>
						</tr>
					</table>
					</td>
					<td width="40%">
						<table width="100%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
						<tr>
							<td valign="top" width="50%" align="left"><strong>Age</strong></td>
							<td width="1" valign="middle">
								<?php echo $this->_tpl_vars['sAge']; ?>

							</td>
						</tr>
						<tr>
							<td valign="top"><strong>Sex</strong></td>
							<td width="1" valign="middle">
								<?php echo $this->_tpl_vars['sSex']; ?>

							</td>
						</tr>
						<tr>
							<td valign="top"><strong>Civil Status</strong></td>
							<td width="1" valign="middle">
								<?php echo $this->_tpl_vars['sCivilStatus']; ?>

							</td>
						</tr>
						<tr>
							<td valign="top"><strong>Membership Date</strong></td>
							<td><?php echo $this->_tpl_vars['sMemberDate']; ?>
</td>
						</tr>
						<tr>
							<td valign="top"><strong>Covered Date</strong></td>
							<td><?php echo $this->_tpl_vars['sCoveredDate']; ?>
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

<br>
	<div align="left" style="width:95%">
		<table width="100%">
			<tr>
				<td width="50%" align="left">
					<?php echo $this->_tpl_vars['sBtnAddItem']; ?>

					<?php echo $this->_tpl_vars['sBtnEmptyList']; ?>

					<?php echo $this->_tpl_vars['sBtnPDF']; ?>

				</td>
				<td align="right">
					<!--<?php echo $this->_tpl_vars['sContinueButton']; ?>
-->
					<?php echo $this->_tpl_vars['sBreakButton']; ?>

				</td>
			</tr>
		</table>
		<table id="dep-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr id="dep-list-header">
					<th width="4%" nowrap align="left">Cnt : <span id="counter"></span></th>
					<th width="0.5%"></th>
					<th width="10%" nowrap align="left">&nbsp;&nbsp;HRN</th>
					<th width="*" nowrap align="left">&nbsp;&nbsp;Dependents</th>
					<th width="15%" nowrap align="left">&nbsp;&nbsp;Relationship</th>
					<th width="10%" nowrap align="left">&nbsp;&nbsp;Bdate</th>
					<th width="10%" nowrap align="left">&nbsp;&nbsp;Age</th>
					<th width="5%" nowrap align="left">&nbsp;&nbsp;Sex</th>
					<th width="10%" nowrap align="left">&nbsp;&nbsp;Civil Status</th>
					<!--<th width="5%" nowrap align="left">&nbsp;&nbsp;Delete</th>-->
				</tr>
			</thead>
			<tbody>
			<?php echo $this->_tpl_vars['sOrderItems']; ?>

			</tbody>
		</table>
		<!-- added by: syboy 12/16/2015 : meow -->
		<fieldset>
			<legend>Remarks:</legend>
			<table id="dep-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
				<thead>
					<tr id="remarks-list">
						<th align="center" width="10%">Actions</th>
						<th align="center" width="*%">Remarks</th>
					</tr>
				</thead>
				<tbody id="remarkslist">
					<?php if (count($_from = (array)$this->_tpl_vars['remarksData'])):
    foreach ($_from as $this->_tpl_vars['myId'] => $this->_tpl_vars['i']):
?>
						<tr id="dependentsRem_<?php echo $this->_tpl_vars['i']['id']; ?>
">
							<td>
							
							<?php if (! $this->_tpl_vars['sAllow_depmanager'] && $this->_tpl_vars['sAllow_searchEmp']): ?>
								<img class='segSimulatedLin disabled' style="cursor: pointer;" src='../../images/cashier_delete_small.gif' border='0'/>
								<img class="segSimulatedLin disabled" style="cursor: pointer;" src="../../images/cashier_edit_3.gif" border="0"/>
							<?php else: ?>
								<?php if ($this->_tpl_vars['showAddRemarks']): ?>
									<img class='segSimulatedLin' style="cursor: pointer;" src='../../images/cashier_delete_small.gif' border='0' onClick='deleteRemarks(<?php echo $this->_tpl_vars['i']['id']; ?>
)'/>
									<img class="segSimulatedLin" style="cursor: pointer;" src="../../images/cashier_edit_3.gif" border="0" onClick="updateRemarks(<?php echo $this->_tpl_vars['i']['id']; ?>
)"/>
								<?php else: ?>
									<img class='segSimulatedLin disabled' style="cursor: pointer;" src='../../images/cashier_delete_small.gif' border='0'/>
									<img class="segSimulatedLin disabled" style="cursor: pointer;" src="../../images/cashier_edit_3.gif" border="0"/>
								<?php endif; ?>
							<?php endif; ?>
							</td>
							<td align="left"><?php echo $this->_tpl_vars['i']['remarks']; ?>
</td>
						</tr>
					<?php endforeach; unset($_from); endif; ?>
				</tbody>

			</table>

			<?php if ($this->_tpl_vars['showAddRemarks']): ?>
			<div id="remarks-form">
				<fieldset>
				  <legend>Add Remarks:</legend>
				  Remarks <font color="red">*</font> : <?php echo $this->_tpl_vars['sDependentsSetRemarks']; ?>

				  <input type="hidden" id="hidden_idDep" size="5">
				  <br />
				  <?php echo $this->_tpl_vars['sSetRemarks']; ?>

				  <img name="EditRemarks" id="EditRemarks" style="cursor: pointer; display : none;" src="../../gui/img/control/default/en/en_edit_02.gif" />
				  <img name="CancelRemarks" id="CancelRemarks" style="cursor: pointer; display : none;" src="../../gui/img/control/default/en/en_cancel.gif" />
				 </fieldset>
			</div>
			<?php endif; ?>
		</fieldset>
		<!-- Ended syboy -->
	</div>
    
<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<?php echo $this->_tpl_vars['sIntialRequestList']; ?>

<br/>
<img src="" vspace="2" width="1" height="1"><br/>
<?php echo $this->_tpl_vars['sDiscountControls']; ?>

<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>



<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>
 	
<hr/>
<!--
<input type="button" name="btnRefreshDiscount" id="btnRefreshDiscount" onclick="refreshDiscount()" value="Refresh Discount">
<input type="button" name="btnRefreshTotal" id="btnRefreshTotal" onclick="refreshTotal()" value="Refresh Totals">
-->
<?php echo $this->_tpl_vars['srelprompter']; ?>