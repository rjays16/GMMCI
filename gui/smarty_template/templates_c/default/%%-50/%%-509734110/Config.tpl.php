<?php /* Smarty version 2.6.0, created on 2017-12-18 16:52:37
         compiled from ../../../modules/dashboard/dashlets/PatientList/templates/Config.tpl */ ?>
<div class="data-form">
	<form id="form-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" method="post" action="./">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tbody class="data-form-group">
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Page size</span>
							<span class="data-form-desc">Maximum number of items per page</span>
						</label>
					</td>
					<td class="data-form-field">
						<select type="text" class="input" id="pageSize-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" name="pageSize">
							<option value="5" <?php if ("{{".($this->_tpl_vars['settings']).".pageSize" == 5): ?>selected="selected"<?php endif; ?>>5</option>
							<option value="10" <?php if ("{{".($this->_tpl_vars['settings']).".pageSize" == 10): ?>selected="selected"<?php endif; ?>>10</option>
							<option value="20" <?php if ("{{".($this->_tpl_vars['settings']).".pageSize" == 20): ?>selected="selected"<?php endif; ?>>20</option>
						</select>
					</td>
				</tr>

				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">View type</span>
							<span class="data-form-desc">Display results in listing view or itemized view</span>
						</label>
					</td>
					<td class="data-form-field">
						<select type="text" class="input" id="view-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" name="viewType">
							<option value="list" <?php if ($this->_tpl_vars['settings']['viewType'] == 'list'): ?>selected="selected"<?php endif; ?>>List view</option>
							<option value="item" <?php if ($this->_tpl_vars['settings']['viewType'] == 'item'): ?>selected="selected"<?php endif; ?>>Item view</option>
						</select>
					</td>
				</tr>

				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Filter list</span>
							<span class="data-form-desc">Show only these patients</span>
						</label>
					</td>
					<td class="data-form-field">
						<select type="text" class="input" id="filter-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" name="filter">
							<option value="assigned" <?php if ($this->_tpl_vars['settings']['filter'] == 'assigned'): ?>selected="selected"<?php endif; ?>>My patients</option>
							<option value="department" <?php if ($this->_tpl_vars['settings']['filter'] == 'department'): ?>selected="selected"<?php endif; ?>>My department</option>
                            <option value="all" <?php if ($this->_tpl_vars['settings']['filter'] == 'all'): ?>selected="selected"<?php endif; ?>
                            	<?php if (! $this->_tpl_vars['settings']['isShowAllDept']): ?>style="display:none"<?php endif; ?>>All departments
                            </option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="data-form-controls" align="right">
			<button id="ui-save-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
">Save settings</button>
			<button id="ui-close-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
">Back</button>
		</div>
	</form>
</div>

<script type="text/javascript">
(function($) {
	$("#ui-save-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
").button({
		icons: { primary: 'ui-icon-circle-check' }
	}).click(function() {
		Dashboard.dashlets.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "save", {
			data: $('#form-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
').serializeArray()
		});
		return false;
	});

	$("#ui-close-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
").button({
		icons: { primary: 'ui-icon-arrowreturnthick-1-w' }
	}).click(function() {
		Dashboard.dashlets.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "setMode", {mode:'view'});
		return false;
	});
})(jQuery);
</script>