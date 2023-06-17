<?php /* Smarty version 2.6.0, created on 2018-04-24 13:17:50
         compiled from common/mainframe2.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'config_load', 'common/mainframe2.tpl', 4, false),)), $this); ?>

<?php echo smarty_function_config_load(array('file' => "test.conf",'section' => 'setup'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<table width=100% border=0 cellspacing=0 height=100%>
<tbody class="main">
	<tr>
		<td bgcolor=<?php echo $this->_tpl_vars['body_bgcolor']; ?>
 valign=top>
		
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
			
		</td>
	</tr>
	</tbody>
 </table>