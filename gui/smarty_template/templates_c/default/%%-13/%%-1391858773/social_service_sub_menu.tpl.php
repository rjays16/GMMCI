<?php /* Smarty version 2.6.0, created on 2018-05-23 17:05:23
         compiled from social_service/social_service_sub_menu.tpl */ ?>
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
					<TBODY >
						<TR>
							<TD class="submenu_title" colspan=3>Social Service Classification</TD>
						</tr>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sRequestTestIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDClassifyNewPatient']; ?>
</`nobr></TD>
							<TD>Classify admitted patient or ER patient</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sLabServicesRequestIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDListOfClassifiedPatient']; ?>
</nobr></TD>
							<TD>View and update Social Service Classification status</TD>
						</TR>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TABLE>
			<br/>
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
					<TBODY>
						<TR>
							<TD class="submenu_title" colspan=3>Social Service Management</TD>
						</tr>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sBloodRequestIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDManageClassification']; ?>
</nobr></TD>
							<TD>Manage social service classifications and discounts</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						
						<!-- added by VAN 07-05-08 -->
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sModifierIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDManageModifiers']; ?>
</nobr></TD>
							<TD>Manage social service modifiers</TD>
						</TR>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<TR>
							<TD width="1%"><?php echo $this->_tpl_vars['sBloodTestReceptionIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDSocialReports']; ?>
</nobr></TD>
							<TD>View and print specific status reports</TD>
						</TR>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TABLE>
			<br/>
			<a href="<?php echo $this->_tpl_vars['breakfile']; ?>
"><img <?php echo $this->_tpl_vars['gifClose2']; ?>
 alt="<?php echo $this->_tpl_vars['LDCloseAlt']; ?>
" <?php echo $this->_tpl_vars['dhtml']; ?>
></a>