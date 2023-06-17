<?php /* Smarty version 2.6.0, created on 2017-11-28 16:37:20
         compiled from common/main.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'config_load', 'common/main.tpl', 4, false),)), $this); ?>

<?php echo smarty_function_config_load(array('file' => "test.conf",'section' => 'setup'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div style="width:auto; padding:2px;">
<table id="main" width="100%" border="0" cellspacing="0">
	<tbody class="main">
<?php if (! $this->_tpl_vars['bHideTitleBar']): ?>
		<tr>
			<td  valign="top" align="middle" height="35">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/header_topblock.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</td>
		</tr>
<?php endif; ?>
		<tr>
			<td valign="top">
				<div align="center" style="">
				<?php if ($this->_tpl_vars['sysInfoMessage'] != ""): ?>
					<dl id="system-message">
						<dt>Information</dt>
						<dd>
							<?php echo $this->_tpl_vars['sysInfoMessage']; ?>

						</dd>
					</dl>
				<?php elseif ($this->_tpl_vars['sysErrorMessage'] != ""): ?>
					<dl id="error-message">
						<dt>System error</dt>
						<dd>
							<?php echo $this->_tpl_vars['sysErrorMessage']; ?>

						</dd>
					</dl>
				<?php endif; ?>

								<?php if ($this->_tpl_vars['sMainBlockIncludeFile'] != ""): ?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['sMainBlockIncludeFile'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php endif; ?>
				<?php if ($this->_tpl_vars['sMainFrameBlockData'] != ""): ?>
					<?php echo $this->_tpl_vars['sMainFrameBlockData']; ?>

				<?php endif; ?>
				
				</div>
			</td>
		</tr>
<?php if (! $this->_tpl_vars['bHideCopyright']): ?>
		<tr valign=top label="copyright">
			<td bbgcolor=<?php echo $this->_tpl_vars['bot_bgcolor']; ?>
 bgcolor="ffffff">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/copyright.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</td>
		</tr>
<?php endif; ?>
	</tbody>
</table>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>