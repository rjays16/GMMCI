<?php /* Smarty version 2.6.0, created on 2018-02-06 21:11:05
         compiled from nursing/ward_transferbed_list.tpl */ ?>
<table>
		<tr>
			<td class="adm_item">
				Date and Time (if Not Real time):
			</td>
			<td colspan=2 class="adm_input">
				<?php echo $this->_tpl_vars['sLDDateFrom']; ?>

				<?php echo $this->_tpl_vars['sDateMiniCalendar']; ?>

				<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

				&nbsp;&nbsp;
				<?php echo $this->_tpl_vars['sLDTimeFrom']; ?>

			</td>
		</tr>
</table>
&nbsp;&nbsp;
<table cellspacing="0" width="100%">
<tbody>
	<tr>
		<td class="adm_item"><?php echo $this->_tpl_vars['LDRoom']; ?>
</td>
		<!-- added by Mats 07262016 -->
		<td class="adm_item"><?php echo $this->_tpl_vars['LDDescription']; ?>
</td>
		
		<td class="adm_item"><?php echo $this->_tpl_vars['LDBed']; ?>
</td>
		<td class="adm_item">&nbsp;</td>
		<td class="adm_item"><?php echo $this->_tpl_vars['LDFamilyName']; ?>
, <?php echo $this->_tpl_vars['LDName']; ?>
</td>
		<td class="adm_item"><?php echo $this->_tpl_vars['LDBirthDate']; ?>
</td>
		<td class="adm_item"><?php echo $this->_tpl_vars['LDBillType']; ?>
</td>
		<td class="adm_item">&nbsp;</td>
	</tr>

	<?php echo $this->_tpl_vars['sOccListRows']; ?>


 </tbody>
</table>