<?php /* Smarty version 2.6.0, created on 2017-11-28 16:37:22
         compiled from ../../../modules/dashboard/templates/dashlet.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '../../../modules/dashboard/templates/dashlet.tpl', 34, false),)), $this); ?>
<div class="dashlet">
	<div class="dashletHeader">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tbody>
				<tr>
					<td class="dashletTitle">
						<span id="title_<?php echo $this->_tpl_vars['dashlet']['id']; ?>
"
							style="padding-left:20px; background-position: left center; background-repeat: no-repeat; background-image: url(<?php echo $this->_tpl_vars['dashlet']['icon']; ?>
)"
							ondblclick="var p;if (p = prompt('Enter a new title for this Dashlet: ')) Dashboard.dashlets.sendAction('<?php echo $this->_tpl_vars['dashlet']['id']; ?>
', 'setTitle', { title: p}); return false;"><?php echo $this->_tpl_vars['dashlet']['title']; ?>
</span>
					</td>
					<td id="controls_<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" class="dashletControls">
<?php if (count($_from = (array)$this->_tpl_vars['customModes'])):
    foreach ($_from as $this->_tpl_vars['mode']):
?>
						<a id="custom_mode_<?php echo $this->_tpl_vars['mode']['name']; ?>
_<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" href="#"><span class="dashlet-icon icon-<?php echo $this->_tpl_vars['mode']['icon']; ?>
"></span></a>
						<script type="text/javascript">
							(function($) {
								$("#custom_mode_<?php echo $this->_tpl_vars['mode']['name']; ?>
_<?php echo $this->_tpl_vars['dashlet']['id']; ?>
").click( function() {
									Dashboard.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "changeMode", { mode: "<?php echo $this->_tpl_vars['mode']['name']; ?>
"})
									return false;
								});
							})(jQuery);
						</script>
<?php endforeach; unset($_from); endif; ?>
						<?php if ($this->_tpl_vars['dashlet']['state'] == 'normal'): ?><a id="controls_up_<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" href="#" onclick="Dashboard.dashlets.minimize('<?php echo $this->_tpl_vars['dashlet']['id']; ?>
'); return false;"><span class="dashlet-icon icon-up"></span></a><?php endif; ?>
						<?php if ($this->_tpl_vars['dashlet']['state'] == 'minimized'): ?><a id="controls_down_<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" href="#" onclick="Dashboard.dashlets.restore('<?php echo $this->_tpl_vars['dashlet']['id']; ?>
'); return false;"><span class="dashlet-icon icon-down"></span></a><?php endif; ?>
						<a id="controls_refresh_<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" href="#" onclick="Dashboard.dashlets.refresh('<?php echo $this->_tpl_vars['dashlet']['id']; ?>
'); return false;"><span class="dashlet-icon icon-refresh"></span></a>
						<a id="controls_edit_<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" href="#" onclick="Dashboard.dashlets.sendAction('<?php echo $this->_tpl_vars['dashlet']['id']; ?>
', 'setMode', { mode: 'edit' }); return false;"><span class="dashlet-icon icon-edit"></span></a>
						<a id="controls_delete_<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" href="#" onclick="if (confirm('Do you wish to remove this Dashlet?')) { Dashboard.dashlets.remove({dashlet:'<?php echo $this->_tpl_vars['dashlet']['id']; ?>
'}) } return false;"><span class="dashlet-icon icon-delete"></span></a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="dashletBody" <?php if ($this->_tpl_vars['dashlet']['state'] == 'minimized'): ?>style="display:none"<?php endif; ?>>
		<div id="content_<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" class="dashletContents" style="height:<?php echo ((is_array($_tmp=@$this->_tpl_vars['preferences']['contentHeight'])) ? $this->_run_mod_handler('default', true, $_tmp, 'auto') : smarty_modifier_default($_tmp, 'auto')); ?>
">
<?php echo $this->_tpl_vars['dashlet']['contents']; ?>

		</div>
	</div>
	<div id="footer_<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" class="dashletFooter"></div>
</div>