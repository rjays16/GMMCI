<?php /* Smarty version 2.6.0, created on 2018-02-06 21:11:05
         compiled from nursing/ward_transferbed_list_row.tpl */ ?>

 <?php if ($this->_tpl_vars['bHighlightRow']): ?>
 	<tr class="hilite">
 <?php elseif ($this->_tpl_vars['bToggleRowClass']): ?>
	<tr class="wardlistrow1">

 <?php else: ?>
	<tr class="wardlistrow2">
 <?php endif; ?>
		<td>&nbsp;<?php echo $this->_tpl_vars['sRoom']; ?>
</td>
                <!-- added by Mats 07262016 -->
		<td>&nbsp;<?php echo $this->_tpl_vars['sDescription']; ?>
</td>
		<td>&nbsp;<?php echo $this->_tpl_vars['sBed'];  echo $this->_tpl_vars['sBedPlusIcon']; ?>
</td> <!-- edited by: syboy 06/30/2015 -->
		<td>
			<table>
				<?php echo $this->_tpl_vars['sBedIcon']; ?>

			</table>
		</td>
		<td>
			<table>
				<?php echo $this->_tpl_vars['sTitle']; ?>
 <?php echo $this->_tpl_vars['sFamilyName'];  echo $this->_tpl_vars['cComma']; ?>
 <?php echo $this->_tpl_vars['sName']; ?>

			</table>
		</td>
		<td>
			<table>
				<?php echo $this->_tpl_vars['sBirthDate']; ?>

			</table>
		</td>
		<td>
			<table>
				<?php echo $this->_tpl_vars['sInsuranceType']; ?>

			</table>
		</td>
		<td>
			<table>
				<?php echo $this->_tpl_vars['sNotesIcon']; ?>

			</table>
		</td>
		</tr>
		<tr>
		<td colspan="8" class="thinrow_vspacer"><?php echo $this->_tpl_vars['sOnePixel']; ?>
</td>
	</tr>