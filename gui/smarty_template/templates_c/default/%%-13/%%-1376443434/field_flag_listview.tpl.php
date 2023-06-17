<?php /* Smarty version 2.6.0, created on 2018-05-17 18:53:02
         compiled from ../../../modules/codetable/dynamicfields/flag/field_flag_listview.tpl */ ?>
<div style="text-align:center">
<?php if ($this->_tpl_vars['value'] == 0): ?>
<?php if ($this->_tpl_vars['options']['offImage']): ?>
<img src="<?php echo $this->_tpl_vars['options']['imagesPath'];  echo $this->_tpl_vars['options']['offImage']; ?>
" align="absmiddle" />
<?php else: ?>
<?php echo $this->_tpl_vars['options']['offText']; ?>

<?php endif; ?>
<?php else: ?>
<?php if ($this->_tpl_vars['options']['onImage']): ?>
<img src="<?php echo $this->_tpl_vars['options']['imagesPath'];  echo $this->_tpl_vars['options']['onImage']; ?>
" align="absmiddle" />
<?php else: ?>
<?php echo $this->_tpl_vars['options']['onText']; ?>

<?php endif; ?>
<?php endif; ?>
</div>