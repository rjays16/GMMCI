<?php /* Smarty version 2.6.0, created on 2018-04-24 13:12:50
         compiled from registration_admission/reg_show.tpl */ ?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tbody>
    <tr>
      <td><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "registration_admission/reg_tabs.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
    </tr>
	<!--added by VAN 02-28-08 -->
	<?php if ($this->_tpl_vars['is_discharged']): ?>
				<tr>
					<td bgcolor="red" colspan="3">
						&nbsp;
						<?php echo $this->_tpl_vars['sWarnIcon']; ?>

						<font color="#ffffff">
						<b>
						<?php echo $this->_tpl_vars['sDischarged']; ?>

						</b>
						</font>
					</td>
				</tr>
		<?php endif; ?>
    <tr>
      <td>
			<table cellspacing="0" cellpadding="0" width=800>
			<tbody>
				<tr valign="top">
					<td><?php echo $this->_tpl_vars['sRegForm']; ?>
</td>
					<td><?php echo $this->_tpl_vars['sRegOptions']; ?>
</td>
				</tr>
			</tbody>
			</table>
	  </td>
    </tr>
    
	<tr>
      <td valign="top">
	  <?php echo $this->_tpl_vars['pbNewSearch']; ?>
 <?php echo $this->_tpl_vars['pbUpdateData']; ?>
 <?php echo $this->_tpl_vars['pbRegInsurance']; ?>
 <?php echo $this->_tpl_vars['pbShowAdmData']; ?>
 <?php echo $this->_tpl_vars['pbAdmitInpatient']; ?>
 <?php echo $this->_tpl_vars['pbAdmitOutpatient']; ?>
 <?php echo $this->_tpl_vars['pbRegNewPerson']; ?>

<!--  Edited by Bong 2/21/2007 <span class="reg_input"><?php echo $this->_tpl_vars['sOtherNr']; ?>
</span> --></td>
    </tr>

    <tr>
      <td>
		<?php echo $this->_tpl_vars['sSearchLink']; ?>

		<br>
		<?php echo $this->_tpl_vars['sArchiveLink']; ?>

		<p>
		<?php echo $this->_tpl_vars['pbCancel']; ?>

		</td>
    </tr>

  </tbody>
</table>