<?php /* Smarty version 2.6.0, created on 2018-04-24 13:28:16
         compiled from medocs/main.tpl */ ?>
</style>
<table width="100%" cellspacing="0" cellpadding="0">
  <tbody>
    <tr>
      <td><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "medocs/tabs.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
    </tr>

    <tr>
      <td>
		<table width="100%" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<!--<td width="840">-->
				<!--edited by VAN 02-18-08 -->
				<?php if ($this->_tpl_vars['sShow']): ?>
					<td width="78%">
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "registration_admission/basic_data.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>				
					</td>
					<td width="22%"><?php echo $this->_tpl_vars['sRegOptions']; ?>
</td>
				<?php else: ?>
					<td width="840">
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "registration_admission/basic_data.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>				
					</td>
				<?php endif; ?>	
			</tr>
			<?php if ($this->_tpl_vars['sPHICShow']): ?>
			<tr>
				<!--<?php if ($this->_tpl_vars['sPHICShow']): ?>-->
				<td colspan="2">
					<table border="0" width="74%" >
						<tr>
							<td <?php echo $this->_tpl_vars['sClassItem']; ?>
 width="31%"><?php echo $this->_tpl_vars['LDDischargedDate']; ?>
</td>
							<td bgcolor="#ffffee" class="vi_data"><b><?php echo $this->_tpl_vars['sDischargedDate']; ?>
</b></td>
						</tr>
						<tr>
							<td <?php echo $this->_tpl_vars['sClassItem']; ?>
 width="31%"><?php echo $this->_tpl_vars['LDReceivedDate']; ?>
</td>
							<td bgcolor="#ffffee" class="vi_data"><b><?php echo $this->_tpl_vars['sReceivedDate']; ?>
</b></td>
						</tr>
						<tr>
							<td <?php echo $this->_tpl_vars['sClassItem']; ?>
 width="31%"><?php echo $this->_tpl_vars['LDPHIC']; ?>
</td>
							<td bgcolor="#ffffee" class="vi_data"><b><?php echo $this->_tpl_vars['sPHIC']; ?>
</b></td>
						</tr>
                        <tr>
                            <td <?php echo $this->_tpl_vars['sClassItem']; ?>
 width="31%"><?php echo $this->_tpl_vars['LDTrans']; ?>
</td>
                            <td bgcolor="#ffffee" class="vi_data"><b><?php echo $this->_tpl_vars['sTrans']; ?>
</b></td>
                        </tr>
					</table>
				</td>
				<!--
				<?php else: ?>
					<td colspan="2">
					<table border="0" width="74%" >
						<tr>
							<td <?php echo $this->_tpl_vars['sClassItem']; ?>
 width="31%"><?php echo $this->_tpl_vars['LDDischargedDate']; ?>
</td>
							<td bgcolor="#ffffee" class="vi_data"><b><?php echo $this->_tpl_vars['sDischargedDate']; ?>
</b></td>
						</tr>
						<tr>
							<td <?php echo $this->_tpl_vars['sClassItem']; ?>
 width="40%"><?php echo $this->_tpl_vars['LDReceivedDate']; ?>
</td>
							<td bgcolor="#ffffee" class="vi_data"><b><?php echo $this->_tpl_vars['sReceivedDate']; ?>
</b></td>
						</tr>
					</table>
				</td>
			
			   <?php endif; ?>	
			   	-->
			</tr>
			<?php endif; ?>	
			<tr>
				<td>
					<?php if ($this->_tpl_vars['bShowNoRecord']): ?>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "registration_admission/common_norecord.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					<?php else: ?>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['sDocsBlockIncludeFile'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					<?php endif; ?>
	  			</td>
			</tr>
		</tbody>
		</table>

	  </td>
    </tr>
    
	<tr>
      <td> 
	  	<?php if ($this->_tpl_vars['sHideNewRecLink']): ?>	
			<?php echo $this->_tpl_vars['sNewLinkIcon']; ?>
 <?php echo $this->_tpl_vars['sNewRecLink']; ?>
<br />
			<?php echo $this->_tpl_vars['sKeyListener']; ?>

		<?php endif; ?>	
			<?php echo $this->_tpl_vars['sPdfLinkIcon']; ?>
 <?php echo $this->_tpl_vars['sMakePdfLink']; ?>
<br />
			<?php echo $this->_tpl_vars['sListLinkIcon']; ?>
 <?php echo $this->_tpl_vars['sListRecLink']; ?>
<p>
			<?php echo $this->_tpl_vars['pbBottomClose']; ?>

			<?php echo $this->_tpl_vars['segPrint']; ?>

	  </p></td>
    </tr>

  </tbody>
</table>