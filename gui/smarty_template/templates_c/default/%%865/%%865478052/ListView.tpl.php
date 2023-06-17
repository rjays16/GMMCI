<?php /* Smarty version 2.6.0, created on 2018-05-17 18:52:57
         compiled from ListView.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'eval', 'ListView.tpl', 24, false),array('function', 'counter', 'ListView.tpl', 28, false),array('modifier', 'default', 'ListView.tpl', 24, false),array('modifier', 'cat', 'ListView.tpl', 34, false),)), $this); ?>
<div id="listview_info" class="ui-widget" style="display:none; width:400px; cursor:pointer; -moz-user-select:none; opacity:0.8">
	<div class="ui-state-highlight ui-corner-all" style="margin: 10px 0; padding: 0 .5em;"> 
		<p><span id="listview_info_icon" class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong id="listview_info_title"></strong>
		<span id="listview_info_message"></span></p>
	</div> 
</div>
<div id="listview_alert" class="ui-widget" style="display:none; width:400px; cursor:pointer; -moz-user-select:none; opacity:0.8">
	<div class="ui-state-error ui-corner-all" style="margin: 10px 0; padding: 0 .5em;"> 
		<p><span id="listview_alert_icon" class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
		<strong id="listview_alert_title"></strong>
		<span id="listview_alert_message"></span></p> 
	</div> 
</div>
<div id="listview_search" class="ui-tabs">
	<ul>
<?php if (isset($this->_foreach['search'])) unset($this->_foreach['search']);
$this->_foreach['search']['name'] = 'search';
$this->_foreach['search']['total'] = count($_from = (array)$this->_tpl_vars['listview']['search']);
$this->_foreach['search']['show'] = $this->_foreach['search']['total'] > 0;
if ($this->_foreach['search']['show']):
$this->_foreach['search']['iteration'] = 0;
    foreach ($_from as $this->_tpl_vars['searchId'] => $this->_tpl_vars['searchItem']):
        $this->_foreach['search']['iteration']++;
        $this->_foreach['search']['first'] = ($this->_foreach['search']['iteration'] == 1);
        $this->_foreach['search']['last']  = ($this->_foreach['search']['iteration'] == $this->_foreach['search']['total']);
?>
	<li><a href="#<?php echo $this->_tpl_vars['searchId']; ?>
"><?php echo $this->_tpl_vars['searchItem']['label']; ?>
</a></li>
<?php endforeach; unset($_from); endif; ?>
	</ul>
<?php if (isset($this->_foreach['search'])) unset($this->_foreach['search']);
$this->_foreach['search']['name'] = 'search';
$this->_foreach['search']['total'] = count($_from = (array)$this->_tpl_vars['listview']['search']);
$this->_foreach['search']['show'] = $this->_foreach['search']['total'] > 0;
if ($this->_foreach['search']['show']):
$this->_foreach['search']['iteration'] = 0;
    foreach ($_from as $this->_tpl_vars['searchId'] => $this->_tpl_vars['searchItem']):
        $this->_foreach['search']['iteration']++;
        $this->_foreach['search']['first'] = ($this->_foreach['search']['iteration'] == 1);
        $this->_foreach['search']['last']  = ($this->_foreach['search']['iteration'] == $this->_foreach['search']['total']);
?>
	<div id="<?php echo $this->_tpl_vars['searchId']; ?>
" class="ui-tabs-hide">
		<table width="100%" cellpadding="0" cellspacing="4" border="0" style="empty-cells:show">
		<?php echo smarty_function_eval(array('var' => ((is_array($_tmp=@$this->_tpl_vars['searchItem']['columns'])) ? $this->_run_mod_handler('default', true, $_tmp, 1) : smarty_modifier_default($_tmp, 1)),'assign' => 'columns'), $this);?>

		<?php echo smarty_function_eval(array('var' => $this->_tpl_vars['searchItem']['widths']['label']/$this->_tpl_vars['columns'],'assign' => 'labelWidth'), $this);?>

		<?php echo smarty_function_eval(array('var' => $this->_tpl_vars['searchItem']['widths']['field']/$this->_tpl_vars['columns'],'assign' => 'fieldWidth'), $this);?>

		<?php echo smarty_function_eval(array('var' => $this->_tpl_vars['searchItem']['widths']['filler']/$this->_tpl_vars['columns'],'assign' => 'fillerWidth'), $this);?>

		<?php echo smarty_function_counter(array('name' => 'filterCounter','start' => 0,'assign' => 'fCntr'), $this);?>

		<?php if (isset($this->_foreach['filters'])) unset($this->_foreach['filters']);
$this->_foreach['filters']['name'] = 'filters';
$this->_foreach['filters']['total'] = count($_from = (array)$this->_tpl_vars['filters'][$this->_tpl_vars['searchId']]);
$this->_foreach['filters']['show'] = $this->_foreach['filters']['total'] > 0;
if ($this->_foreach['filters']['show']):
$this->_foreach['filters']['iteration'] = 0;
    foreach ($_from as $this->_tpl_vars['filterId'] => $this->_tpl_vars['filter']):
        $this->_foreach['filters']['iteration']++;
        $this->_foreach['filters']['first'] = ($this->_foreach['filters']['iteration'] == 1);
        $this->_foreach['filters']['last']  = ($this->_foreach['filters']['iteration'] == $this->_foreach['filters']['total']);
?>
			<?php if ($this->_tpl_vars['columns'] > 1): ?>
				<?php if (!($this->_tpl_vars['fCntr'] % $this->_tpl_vars['columns'])): ?>
			<tr>
				<?php endif; ?>
				<td id="<?php echo ((is_array($_tmp=$this->_tpl_vars['filterId'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_lbl') : smarty_modifier_cat($_tmp, '_lbl')); ?>
" class="filterLabel" width="<?php echo $this->_tpl_vars['labelWidth']; ?>
%" style="white-space:nowrap"><?php echo $this->_tpl_vars['filter']['label']; ?>
:</td>
				<td id="<?php echo ((is_array($_tmp=$this->_tpl_vars['filterId'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_fld') : smarty_modifier_cat($_tmp, '_fld')); ?>
" class="filterField" width="<?php echo $this->_tpl_vars['fieldWidth']; ?>
%" style="white-space:nowrap"><?php echo $this->_tpl_vars['filter']['field']; ?>
</td>
				<td width="<?php echo $this->_tpl_vars['fillerWidth']; ?>
%"></td>
				<?php echo smarty_function_counter(array('name' => 'filterCounter'), $this);?>

				<?php if (!($this->_tpl_vars['fCntr'] % $this->_tpl_vars['columns']) || $this->_foreach['filters']['last']): ?>
				<td width="*"></td>
			</tr>
				<?php endif; ?>
		<?php else: ?>
			<tr>
				<td id="<?php echo ((is_array($_tmp=$this->_tpl_vars['filterId'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_lbl') : smarty_modifier_cat($_tmp, '_lbl')); ?>
" class="filterLabel" width="<?php echo $this->_tpl_vars['labelWidth']; ?>
%" style="white-space:nowrap"><?php echo $this->_tpl_vars['filter']['label']; ?>
:</td>
				<td id="<?php echo ((is_array($_tmp=$this->_tpl_vars['filterId'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_fld') : smarty_modifier_cat($_tmp, '_fld')); ?>
" class="filterField" width="<?php echo $this->_tpl_vars['fieldWidth']; ?>
%" style="white-space:nowrap"><?php echo $this->_tpl_vars['filter']['field']; ?>
</td>
				<td width="<?php echo $this->_tpl_vars['fillerWidth']; ?>
%"></td>
			</tr>
		<?php endif; ?>
	<?php endforeach; unset($_from); endif; ?>
	</table>
</div>
<?php endforeach; unset($_from); endif; ?>
</div>
<div style="margin:3px 2px; text-align:left; white-space:nowrap">
<button class="button" onclick="CodeTable.refresh(); return false"><img src="<?php echo $this->_tpl_vars['root_path']; ?>
gui/img/common/default/magnifier.png"/>Search</button>
<button class="button" onclick="openEditView(); return false"><img src="<?php echo $this->_tpl_vars['root_path']; ?>
gui/img/common/default/add.png"/>New item</button>
</div>
<div id="listview"></div>
<div id="listview_history" style="display:none"></div>
<iframe id="listview_edit" frameBorder="0" scrolling="auto" style="display:none"></iframe>
<input type="hidden" name="sid" value="<?php echo '<?php'; ?>
 echo $sid<?php echo '?>'; ?>
">
<input type="hidden" name="lang" value="<?php echo '<?php'; ?>
 echo $lang<?php echo '?>'; ?>
">
<input type="hidden" name="cat" value="<?php echo '<?php'; ?>
 echo $cat<?php echo '?>'; ?>
">
<input type="hidden" name="userck" value="<?php echo '<?php'; ?>
 echo $userck <?php echo '?>'; ?>
">