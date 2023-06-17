<?php /* Smarty version 2.6.0, created on 2018-05-17 18:52:56
         compiled from ../../modules/codetable/dynamicfields/sequence/field_sequence_searchview.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '../../modules/codetable/dynamicfields/sequence/field_sequence_searchview.tpl', 1, false),array('modifier', 'escape', '../../modules/codetable/dynamicfields/sequence/field_sequence_searchview.tpl', 1, false),)), $this); ?>
<input filter="<?php echo $this->_tpl_vars['options']['filter']; ?>
" param="value" id="<?php echo $this->_tpl_vars['options']['id']; ?>
_value" name="<?php echo $this->_tpl_vars['options']['name']; ?>
[value]" class="<?php echo ((is_array($_tmp=@$this->_tpl_vars['className'])) ? $this->_run_mod_handler('default', true, $_tmp, 'input') : smarty_modifier_default($_tmp, 'input')); ?>
" type="text" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['value'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" style="width:140px" onfocus="this.value=parseInt(this.value); if (isNaN(this.value)) this.value='';" onblur="this.value=parseInt(this.value); if (isNaN(this.value)) this.value='';" />