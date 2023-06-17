<?php /* Smarty version 2.6.0, created on 2017-10-04 19:46:37
         compiled from registration_admission/reg_duplicate_main.tpl */ ?>
<style type="text/css">
<!--
body {
	background-color: #EBF0FE;
}
-->
</style>


<?php echo $this->_tpl_vars['LDSearchFound']; ?>


<?php if ($this->_tpl_vars['bShowResult']): ?>

<div align="center">
	<table border=0 cellpadding=2 cellspacing=1>

				<tr class="reg_list_titlebar">
			<td width="10%"><?php echo $this->_tpl_vars['LDRegistryNr']; ?>
</td>
			<td width="1%"><?php echo $this->_tpl_vars['LDSex']; ?>
</td>
			<td width="*"><?php echo $this->_tpl_vars['LDLastName']; ?>
</td>
			<td width="15%"><?php echo $this->_tpl_vars['LDFirstName']; ?>
</td>
			<td width="10%"><?php echo $this->_tpl_vars['LDMiddleName']; ?>
</td>
			<td width="5%"><?php echo $this->_tpl_vars['LDBday']; ?>
</td>
			<td width="10%"><?php echo $this->_tpl_vars['segBrgy']; ?>
</td>
			<td width="10%"><?php echo $this->_tpl_vars['segMuni']; ?>
</td>
			<td width="5%"><?php echo $this->_tpl_vars['LDZipCode']; ?>
</td>
			<td width="16%"><?php echo $this->_tpl_vars['LDOptions']; ?>
</td>
		</tr>

				<?php echo $this->_tpl_vars['sResultListRows']; ?>


		<tr>
			<td colspan=8><?php echo $this->_tpl_vars['sPreviousPage']; ?>
</td>
			<td align=right><?php echo $this->_tpl_vars['sNextPage']; ?>
</td>
		</tr>
	</table>
<?php endif; ?>
	<?php echo $this->_tpl_vars['sPostText']; ?>

</div>