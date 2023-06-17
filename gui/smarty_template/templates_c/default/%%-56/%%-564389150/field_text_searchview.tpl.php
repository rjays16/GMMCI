<?php /* Smarty version 2.6.0, created on 2018-05-17 18:52:57
         compiled from ../../modules/codetable/dynamicfields/text/field_text_searchview.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '../../modules/codetable/dynamicfields/text/field_text_searchview.tpl', 2, false),array('modifier', 'contains', '../../modules/codetable/dynamicfields/text/field_text_searchview.tpl', 4, false),array('modifier', 'escape', '../../modules/codetable/dynamicfields/text/field_text_searchview.tpl', 10, false),)), $this); ?>
<?php if ($this->_tpl_vars['options']['modes']): ?>
<select filter="<?php echo $this->_tpl_vars['options']['filter']; ?>
" param="mode" id="<?php echo $this->_tpl_vars['options']['id']; ?>
_mode" name="<?php echo $this->_tpl_vars['options']['name']; ?>
[mode]" class="<?php echo ((is_array($_tmp=@$this->_tpl_vars['className'])) ? $this->_run_mod_handler('default', true, $_tmp, 'input') : smarty_modifier_default($_tmp, 'input')); ?>
">
<option value="startswith">Starts with</option>
<?php if (((is_array($_tmp=$this->_tpl_vars['options']['modes'])) ? $this->_run_mod_handler('contains', true, $_tmp, 'endswith') : smarty_modifier_contains($_tmp, 'endswith'))): ?><option value="endswith">Ends with</option><?php endif; ?>
<?php if (((is_array($_tmp=$this->_tpl_vars['options']['modes'])) ? $this->_run_mod_handler('contains', true, $_tmp, 'contains') : smarty_modifier_contains($_tmp, 'contains'))): ?><option value="contains">Contains</option><?php endif; ?>
<?php if (((is_array($_tmp=$this->_tpl_vars['options']['modes'])) ? $this->_run_mod_handler('contains', true, $_tmp, 'doesnotcontain') : smarty_modifier_contains($_tmp, 'doesnotcontain'))): ?><option value="doesnotcontain">Does not contain</option><?php endif; ?>
<?php if (((is_array($_tmp=$this->_tpl_vars['options']['modes'])) ? $this->_run_mod_handler('contains', true, $_tmp, 'exactly') : smarty_modifier_contains($_tmp, 'exactly'))): ?><option value="exactly">Exactly</option><?php endif; ?>
</select>
<?php endif; ?>
<input filter="<?php echo $this->_tpl_vars['options']['filter']; ?>
" param="value" id="<?php echo $this->_tpl_vars['options']['id']; ?>
_value" name="<?php echo $this->_tpl_vars['options']['name']; ?>
[value]" class="<?php echo ((is_array($_tmp=@$this->_tpl_vars['className'])) ? $this->_run_mod_handler('default', true, $_tmp, 'input') : smarty_modifier_default($_tmp, 'input')); ?>
" type="text" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['value'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" style="width:<?php echo $this->_tpl_vars['width']; ?>
" />