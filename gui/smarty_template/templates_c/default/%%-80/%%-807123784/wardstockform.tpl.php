<?php /* Smarty version 2.6.0, created on 2017-12-13 14:13:49
         compiled from order/wardstockform.tpl */ ?>
<script type="text/javascript" language="javascript">
<!--
	function openWindow(url) {
		window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}
-->
</script>

<div align="center" style="font:bold 12px Tahoma; color:#990000; "><?php echo $this->_tpl_vars['sWarning']; ?>
</div><br />

<?php echo $this->_tpl_vars['sFormStart']; ?>


	<div align="center" style="width:100%">
		<table border="0">
			<tr>
				<td width="1"><strong style="white-space:nowrap">Pharmacy area</strong></td>
				<td width="5"></td>
				<td width="*"><?php echo $this->_tpl_vars['sSelectArea']; ?>
</td>
			</tr>
		</table>
	</div>
	<div style="width:600px" align="center">
		<table border="0" cellspacing="2" cellpadding="2" width="400" align="center">
			<tbody>
				<tr>
					<td class="jedPanelHeader">Ward stock information</td>
				</tr>
				<tr>
					<td class="jedPanel">
						<table width="100%" border="0" cellpadding="2" cellspacing="2" style="margin:4px">
							<tr>
								<td width="100" nowrap="nowrap">Reference no</td>
								<td>
									<?php echo $this->_tpl_vars['sRefNo']; ?>

									<?php echo $this->_tpl_vars['sResetRefNo']; ?>

								</td>
							</tr>
							<tr>
								<td nowrap="nowrap">Stock date</td>
								<td><?php echo $this->_tpl_vars['sOrderDate'];  echo $this->_tpl_vars['sCalendarIcon']; ?>
</td>
							</tr>
							<tr>
								<td nowrap="nowrap">Select ward</td>
								<td><?php echo $this->_tpl_vars['sSelectWard']; ?>
</td>
							</tr>
						</table>						
					</td>
				</tr>
			</tbody>
		</table>
		<!--
		<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center">
			<tbody>
				<tr>
					<td class="jedPanelHeader" width="*">
						Order Details
					</td>
					<td class="jedPanelHeader" width="170">
						Reference No.
					</td>
					<td class="jedPanelHeader" width="220">
						Order Date
					</td>
				</tr>
				<tr>
					<td rowspan="3" class="jedPanel" align="center" valign="top">
						<table width="95%" border="0" cellpadding="1" cellspacing="0" style="font:normal 12px Arial" >
							<tr>
								<td nowrap="nowrap"><strong>Transaction type</strong></td>
								<td align="left" nowrap="nowrap">
									<?php echo $this->_tpl_vars['sIsCash']; ?>

									<?php echo $this->_tpl_vars['sIsCharge']; ?>
									
								</td>
								<td align="left"><?php echo $this->_tpl_vars['sIsTPL']; ?>
</td>
							</tr>
						</table>
						<table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:4px; font:normal 12px Arial" >
							<tr>
								<td align="right" valign="top"><strong>Name</strong></td>
								<td width="1" valign="middle">
									<?php echo $this->_tpl_vars['sOrderEncNr']; ?>

									<?php echo $this->_tpl_vars['sOrderEncID']; ?>

									<?php echo $this->_tpl_vars['sOrderDiscountID']; ?>

									<?php echo $this->_tpl_vars['sOrderDiscount']; ?>

									<?php echo $this->_tpl_vars['sOrderName']; ?>

								</td>
								<td width="1" valign="middle">
									<?php echo $this->_tpl_vars['sSelectEnc']; ?>

								</td>
								<td valign="middle">
									<?php echo $this->_tpl_vars['sClearEnc']; ?>

								</td>
							</tr>
							<tr>
								<td valign="top"><strong>Address</strong></td>
								<td colspan="3"><?php echo $this->_tpl_vars['sOrderAddress']; ?>
</td>
							</tr>
						</table>
					</td>
					<td class="jedPanel" align="center">
						<?php echo $this->_tpl_vars['sRefNo']; ?>

						<?php echo $this->_tpl_vars['sResetRefNo']; ?>

					</td>
					<td class="jedPanel" align="center" valign="middle">
						<?php echo $this->_tpl_vars['sOrderDate'];  echo $this->_tpl_vars['sCalendarIcon']; ?>

					</td>
				</tr>
				<tr>
					<td class="jedPanelHeader">Discounts</td>
					<td class="jedPanelHeader">Order options</td>
				</tr>
				<tr>
					<td class="jedPanel" align="center">
						<table>
							<tr>
<?php if ($this->_tpl_vars['ssView']): ?>
<?php else: ?>
								<td valign="middle">
									<div style=""><strong>Classification: </strong><span id="sw-class" style="font:bold 14px Arial;color:#006633"><?php echo $this->_tpl_vars['sSWClass']; ?>
</span></div>
									<div style="margin-top:5px; vertical-align:middle; "><?php echo $this->_tpl_vars['sDiscountShow']; ?>
</div>
								</td>
								<td><?php echo $this->_tpl_vars['sDiscountInfo']; ?>
</td>
<?php endif; ?>
							</tr>
						</table>
						<?php echo $this->_tpl_vars['sBtnDiscounts']; ?>

					</td>
					<td class="jedPanel" align="center" style="padding-bottom:5px">
						<div style="padding-left:5px">
							<strong>Priority</strong>
							<?php echo $this->_tpl_vars['sNormalPriority']; ?>

							<?php echo $this->_tpl_vars['sUrgentPriority']; ?>

						</div>
						<div style="padding-left:35px; margin-bottom:4px; vertical-align:middle">
							<strong style="float:left; margin-top:10px">Notes</strong>
							<?php echo $this->_tpl_vars['sComments']; ?>

						</div>
					</td>
				</tr>
			</tbody>
		</table> -->

		<br />

		<table width="100%">
			<tr>
				<td width="50%" align="left">
					<?php echo $this->_tpl_vars['sBtnAddItem']; ?>

					<?php echo $this->_tpl_vars['sBtnEmptyList']; ?>

					<?php echo $this->_tpl_vars['sBtnPDF']; ?>

				</td>
				<td align="right">
					<?php echo $this->_tpl_vars['sContinueButton']; ?>

					<?php echo $this->_tpl_vars['sBreakButton']; ?>

				</td>
			</tr>
		</table>
		<table id="order-list" class="jedList" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr id="order-list-header">
					<th width="1%" nowrap="nowrap">&nbsp;</th>
					<th width="10%" nowrap="nowrap" align="left">Item No.</th>
					<th width="*" nowrap="nowrap" align="left">Item Description</th>
					<th width="10%" align="center" nowrap="nowrap">Quantity</th>
				</tr>
			</thead>
			<tbody>
<?php echo $this->_tpl_vars['sOrderItems']; ?>

			</tbody>
		</table>
	</div>

<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<br/>
<img src="" vspace="2" width="1" height="1"><br/>
<?php echo $this->_tpl_vars['sDiscountControls']; ?>

<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>

<div style="width:80%">
<?php echo $this->_tpl_vars['sUpdateControlsHorizRule']; ?>

<?php echo $this->_tpl_vars['sUpdateOrder']; ?>

<?php echo $this->_tpl_vars['sCancelUpdate']; ?>

</div>


</div>
<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>
 	