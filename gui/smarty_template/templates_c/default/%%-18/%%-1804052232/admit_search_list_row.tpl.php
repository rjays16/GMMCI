<?php /* Smarty version 2.6.0, created on 2017-11-30 14:33:23
         compiled from registration_admission/admit_search_list_row.tpl */ ?>

<tr  <?php if ($this->_tpl_vars['toggle']): ?> class="wardlistrow2" <?php else: ?> class="wardlistrow1" <?php endif; ?>>
	<td>&nbsp;<?php echo $this->_tpl_vars['sCaseNr']; ?>
 <?php echo $this->_tpl_vars['sOutpatientIcon']; ?>
 <font size=1 color="red"><?php echo $this->_tpl_vars['LDAmbulant']; ?>
</font></td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sEncDate']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sCurrentDept']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sSex']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sLastName']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sFirstName']; ?>
 <?php echo $this->_tpl_vars['sCrossIcon']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sMiddleName']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sBday']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sBrgy']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sMuni']; ?>
</td>
<!--	
	<td>&nbsp;<?php echo $this->_tpl_vars['sZipCode']; ?>
</td>
-->
	<td align="center">&nbsp;<?php echo $this->_tpl_vars['sOptions']; ?>
 <?php echo $this->_tpl_vars['sHiddenBarcode']; ?>
</td>
	<?php if ($this->_tpl_vars['sServeOption']): ?>
		<td align="center"><?php echo $this->_tpl_vars['sServeOption']; ?>
</td>        
	<?php endif; ?>	
</tr>