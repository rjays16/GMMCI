<?php /* Smarty version 2.6.0, created on 2017-11-22 00:07:20
         compiled from social_service/social_search_list_row.tpl */ ?>

<tr  <?php if ($this->_tpl_vars['toggle']): ?> class="wardlistrow2" <?php else: ?> class="wardlistrow1" <?php endif; ?>>
	<td>&nbsp;<?php echo $this->_tpl_vars['sPID']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sCaseNr']; ?>
 <?php echo $this->_tpl_vars['sOutpatientIcon']; ?>
 <font size=1 color="red"><?php echo $this->_tpl_vars['LDAmbulant']; ?>
</font></td>
	<!--<td>&nbsp;<?php echo $this->_tpl_vars['sEncDate']; ?>
</td>-->
	
	<td>&nbsp;<?php echo $this->_tpl_vars['sMSSno']; ?>
</td>
	
	<td>&nbsp;<?php echo $this->_tpl_vars['sSex']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sAge']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sLastName']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sFirstName']; ?>
 <?php echo $this->_tpl_vars['sCrossIcon']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sMiddleName']; ?>
</td>
	
	<td>&nbsp;<?php echo $this->_tpl_vars['sAdmissionDate']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sDischargeDate']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sDepartment']; ?>
</td>
	
	<!-- commented by VAN 06-26-08 -->
	<!--
	<td>&nbsp;<?php echo $this->_tpl_vars['sBday']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sBrgy']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sMuni']; ?>
</td>
	-->
<!--	
	<td>&nbsp;<?php echo $this->_tpl_vars['sZipCode']; ?>
</td>
-->
	<td align="center">&nbsp;<?php echo $this->_tpl_vars['sOptions']; ?>
 <?php echo $this->_tpl_vars['sHiddenBarcode']; ?>
</td>
</tr>