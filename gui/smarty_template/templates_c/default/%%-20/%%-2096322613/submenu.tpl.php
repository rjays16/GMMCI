<?php /* Smarty version 2.6.0, created on 2018-03-26 15:51:21
         compiled from dialysis/submenu.tpl */ ?>
<?php if (isset($this->_foreach['menuFrames'])) unset($this->_foreach['menuFrames']);
$this->_foreach['menuFrames']['name'] = 'menuFrames';
$this->_foreach['menuFrames']['total'] = count($_from = (array)$this->_tpl_vars['aMenu']);
$this->_foreach['menuFrames']['show'] = $this->_foreach['menuFrames']['total'] > 0;
if ($this->_foreach['menuFrames']['show']):
$this->_foreach['menuFrames']['iteration'] = 0;
    foreach ($_from as $this->_tpl_vars['frameTitle'] => $this->_tpl_vars['menuFrame']):
        $this->_foreach['menuFrames']['iteration']++;
        $this->_foreach['menuFrames']['first'] = ($this->_foreach['menuFrames']['iteration'] == 1);
        $this->_foreach['menuFrames']['last']  = ($this->_foreach['menuFrames']['iteration'] == $this->_foreach['menuFrames']['total']);
?>
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE cellSpacing=0 cellPadding=0 width=600 class="submenu_group">
								<TBODY>
									<TR>
										<TD class="submenu_title" colspan=3><?php echo $this->_tpl_vars['frameTitle']; ?>
</TD>
									</TR>
	<?php if (isset($this->_foreach['menuItems'])) unset($this->_foreach['menuItems']);
$this->_foreach['menuItems']['name'] = 'menuItems';
$this->_foreach['menuItems']['total'] = count($_from = (array)$this->_tpl_vars['menuFrame']);
$this->_foreach['menuItems']['show'] = $this->_foreach['menuItems']['total'] > 0;
if ($this->_foreach['menuItems']['show']):
$this->_foreach['menuItems']['iteration'] = 0;
    foreach ($_from as $this->_tpl_vars['item']):
        $this->_foreach['menuItems']['iteration']++;
        $this->_foreach['menuItems']['first'] = ($this->_foreach['menuItems']['iteration'] == 1);
        $this->_foreach['menuItems']['last']  = ($this->_foreach['menuItems']['iteration'] == $this->_foreach['menuItems']['total']);
?>
									<TR>
										<TD align="center" class="submenu_icon"><img <?php echo $this->_tpl_vars['item']['icon']; ?>
 /></td>
										<!-- edited by art 03/16/2015 added permission -->
										<?php if ($this->_tpl_vars['item']['permission'] !== ''): ?>
											<?php if ($this->_tpl_vars['item']['permission'] == 1): ?>
												<TD class="submenu_item"><a href="<?php echo $this->_tpl_vars['item']['href']; ?>
"><?php echo $this->_tpl_vars['item']['label']; ?>
</a></TD>
											<?php else: ?>
												<TD class="submenu_item"><span onclick="alert('No Access Permission')"><?php echo $this->_tpl_vars['item']['label']; ?>
</span></TD>
											<?php endif; ?>
										<?php else: ?>
											<TD class="submenu_item"><a href="<?php echo $this->_tpl_vars['item']['href']; ?>
"><?php echo $this->_tpl_vars['item']['label']; ?>
</a></TD>
										<?php endif; ?>
										<!-- end art -->
										<TD class="submenu_text"><?php echo $this->_tpl_vars['item']['description']; ?>
</TD>
									</TR>
		<?php if ($this->_foreach['menuItems']['last']): ?>
									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>
									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>
	<?php endforeach; unset($_from); endif; ?>
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			<BR/>
<?php endforeach; unset($_from); endif; ?>
			<A href="<?php echo $this->_tpl_vars['breakfile']; ?>
"><img <?php echo $this->_tpl_vars['gifClose2']; ?>
 alt="<?php echo $this->_tpl_vars['LDCloseAlt']; ?>
" <?php echo $this->_tpl_vars['dhtml']; ?>
></a>
			<BR/>