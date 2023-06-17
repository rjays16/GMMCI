<?php /* Smarty version 2.6.0, created on 2018-02-02 11:09:01
         compiled from nursing/ward_occupancy_list_row.tpl */ ?>

 <?php if ($this->_tpl_vars['bToggleRowClass']): ?>
	<tr class="<?php echo $this->_tpl_vars['class_label']; ?>
">
 <?php else: ?>
	<tr class="<?php echo $this->_tpl_vars['class_label']; ?>
">
 <?php endif; ?>
 		<!-- <td><?php echo $this->_tpl_vars['sMiniColorBars']; ?>
</td> commented by mats 06-24-2016-->
		<td>&nbsp;<?php echo $this->_tpl_vars['sRoom']; ?>
</td>
		<td><?php echo $this->_tpl_vars['sDescription']; ?>
</td>
		<td style="font-size:x-small">
			<table>
				<tr>
				<?php echo $this->_tpl_vars['sBed'];  echo $this->_tpl_vars['sBedPlusIcon']; ?>

				</tr>
			</table>
		</td> <!-- added by: syboy 06/30/2015 -->
		<td style="font-size:x-small">
			<table width="100%" cellspacing="0">
				<?php echo $this->_tpl_vars['sBedIcon']; ?>

			</table>
		</td> <!-- edited by: syboy; 05/20/2015 -->
		<td>
			<table width="100%" cellspacing="0">
			<?php echo $this->_tpl_vars['sTitle']; ?>
 <?php echo $this->_tpl_vars['fullnames']; ?>

			</table>
		</td> <!-- <?php echo $this->_tpl_vars['cComma']; ?>
 <?php echo $this->_tpl_vars['sName']; ?>
 -->
		<td style="font-size:x-small; padding: 0;">
			<table width="100%" cellspacing="0">
				<?php echo $this->_tpl_vars['sBirthDate']; ?>

			</table>
		</td>
		<td style="font-size:x-small ">
			<table width="100%" cellspacing="0">
				<?php echo $this->_tpl_vars['sPatNr']; ?>

			</table>
		</td>
		<td style="font-size:x-small ">
			<table width="100%" cellspacing="0">
				<?php echo $this->_tpl_vars['sCaseNo']; ?>

			</table>
		</td>
		<td>
			<table>
				<tr>
					<td>
						<table>
							<?php echo $this->_tpl_vars['sAdmitDataIcon']; ?>

						</table>
					</td>
					<td>
						<table>
						<?php echo $this->_tpl_vars['sChartFolderIcon']; ?>

						</table>
					</td>
					<td>
						<table>
							<?php echo $this->_tpl_vars['sNotesIcon']; ?>

						</table>
					</td>
					<td>
						<table>
							<?php echo $this->_tpl_vars['sTransferIcon']; ?>

						</table>
					</td>
					<td>
						<table>
							<?php echo $this->_tpl_vars['sDischargeIcon']; ?>

						</table>
					</td>
					<td>
						<table>
							<?php echo $this->_tpl_vars['sTransXpiredIcon']; ?>

						</table>
					</td>
                                        <td>
						<table>
							<?php echo $this->_tpl_vars['patient_to_be_discharge']; ?>

						</table>
					</td>
				</tr>
			</table>
		</td>
		</tr>
				 
				 <?php if ($this->_tpl_vars['isBaby']): ?>
					<?php echo $this->_tpl_vars['BabyRows']; ?>

				 <?php else: ?>
				 <?php endif; ?>

		<!-- dati code, jan. 24, 2010
				<?php if ($this->_tpl_vars['isBaby']): ?>
				<?php if ($this->_tpl_vars['bToggleRowClass']): ?>
				<tr class="wardlistrow1">
			 <?php else: ?>
				<tr class="wardlistrow2">
			 <?php endif; ?>
					<td></td>
					<td style="font-size:x-small"><?php echo $this->_tpl_vars['sRoom']; ?>
</td>
					<td style="font-size:x-small ">&nbsp;<?php echo $this->_tpl_vars['sBed']; ?>
 <?php echo $this->_tpl_vars['sBabyBedIcon']; ?>
</td>
					<td><?php echo $this->_tpl_vars['sBabyIcon']; ?>
 <?php echo $this->_tpl_vars['sBabyFamilyName'];  echo $this->_tpl_vars['cComma']; ?>
 <?php echo $this->_tpl_vars['sBabyName']; ?>
</td>
					<td style="font-size:x-small "><?php echo $this->_tpl_vars['sBabyBirthDate']; ?>
</td>
					<td style="font-size:x-small ">&nbsp;<?php echo $this->_tpl_vars['sBabyPatNr']; ?>
</td>
					<td></td>
					<td>&nbsp;<?php echo $this->_tpl_vars['sBabyNotesIcon']; ?>
 <?php echo $this->_tpl_vars['sBabyTransferIcon']; ?>
 </td>
					</tr>
			 <?php else: ?>
			 <?php endif; ?>
		-->
		
		<tr>
		<td colspan="17" class="thinrow_vspacer"><?php echo $this->_tpl_vars['sOnePixel']; ?>
</td>
		</tr>