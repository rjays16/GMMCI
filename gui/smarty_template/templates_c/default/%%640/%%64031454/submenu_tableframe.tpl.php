<?php /* Smarty version 2.6.0, created on 2019-07-22 14:43:25
         compiled from common/submenu_tableframe.tpl */ ?>
<TABLE cellSpacing=0 cellPadding=0 border=0 class="submenu_frame">
	<TBODY>
	<TR>
		<TD>
			<TABLE cellSpacing=0 cellPadding=0  class="submenu">
 				<TBODY>
					<?php if ($this->_tpl_vars['sSubMenuRows']): ?>
						<?php echo $this->_tpl_vars['sSubMenuRows']; ?>

					<?php endif; ?>

					<?php if ($this->_tpl_vars['sSubMenuRowsIncludeFile']): ?>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['sSubMenuRowsIncludeFile'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					<?php endif; ?>
				</TBODY>
			</TABLE>
		</TD>
	</TR>
	</TBODY>
</TABLE>
<a href="<?php echo $this->_tpl_vars['breakfile']; ?>
"><img <?php echo $this->_tpl_vars['gifClose2']; ?>
 alt="<?php echo $this->_tpl_vars['LDCloseAlt']; ?>
" <?php echo $this->_tpl_vars['dhtml']; ?>
></a>