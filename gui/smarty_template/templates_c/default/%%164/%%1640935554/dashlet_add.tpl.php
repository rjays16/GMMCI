<?php /* Smarty version 2.6.0, created on 2018-01-09 23:52:14
         compiled from ../../../modules/dashboard/templates/ui/dashlet_add.tpl */ ?>
<div class="data-form">
	<form id="form-<?php echo $this->_tpl_vars['suffix']; ?>
" method="post" action="./">
		<div style="padding:4px">Select a Dashlet to add:</div>
		<div id="accordion-<?php echo $this->_tpl_vars['suffix']; ?>
" style="width:100%">
<?php if (count($_from = (array)$this->_tpl_vars['categories'])):
    foreach ($_from as $this->_tpl_vars['category']):
?>
			<h3><a href="#"><?php echo $this->_tpl_vars['category']['name']; ?>
</a></h3>
			<div style="padding:0; margin:0">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tbody>
<?php if (count($_from = (array)$this->_tpl_vars['category']['dashlets'])):
    foreach ($_from as $this->_tpl_vars['dashlet']):
?>
<!--Added by Jarel 10/02/2013 
	Show Radiology Findings Dashlet if user has permission
-->
						<tr height="24" <?php if ($this->_tpl_vars['dashlet']['hide'] == $this->_tpl_vars['dept']): ?>
											style="display:none"
										<?php elseif (! $this->_tpl_vars['showradiofindingsdashlet'] && $this->_tpl_vars['dashlet']['id'] == 'PatientRadioFindingsDashlet'): ?>
											style="display:none"
										<?php else: ?>
											style=""	
										<?php endif; ?>	>
							<td width="20%" align="center" style="border-bottom:1px solid #bebebe;">
								<img src="<?php echo $this->_tpl_vars['sRootPath']; ?>
gui/img/common/default/<?php echo $this->_tpl_vars['dashlet']['icon']; ?>
" align="absmiddle" border="0"/>
							</td>
							<td align="left" style="border-bottom:1px solid #bebebe;">
								<?php if ($this->_tpl_vars['onlyPatientList'] && $this->_tpl_vars['is_doctor'] == ""): ?>
									<?php if ($this->_tpl_vars['dashlet']['id'] == 'PatientList'): ?>
										<a id="add-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
-<?php echo $this->_tpl_vars['suffix']; ?>
" href="#" onclick="Dashboard.dialog.close(); Dashboard.dashlets.add({name:'<?php echo $this->_tpl_vars['dashlet']['id']; ?>
'}); return false;">
											<span style="font:bold 12px Arial"><?php echo $this->_tpl_vars['dashlet']['name']; ?>
</span>
										</a>
									<?php else: ?>
										<a id="add-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
-<?php echo $this->_tpl_vars['suffix']; ?>
" href="#" disabled>
											<span style="font:12px Arial"><?php echo $this->_tpl_vars['dashlet']['name']; ?>
</span>
										</a>
									<?php endif; ?>
								<?php else: ?>
									<a id="add-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
-<?php echo $this->_tpl_vars['suffix']; ?>
" href="#" onclick="Dashboard.dialog.close(); Dashboard.dashlets.add({name:'<?php echo $this->_tpl_vars['dashlet']['id']; ?>
'}); return false;">
										<span style="font:bold 12px Arial"><?php echo $this->_tpl_vars['dashlet']['name']; ?>
</span>
									</a>
								<?php endif; ?>
							</td>
						</tr>

<?php endforeach; unset($_from); endif; ?>
					</tbody>
				</table>
			</div>
<?php endforeach; unset($_from); endif; ?>
		</div>
	</form>
</div>

<script type="text/javascript">
(function($) {
	$("#accordion-<?php echo $this->_tpl_vars['suffix']; ?>
").accordion({
		autoHeight: false,
		animated: "slide",
	});
})(jQuery);

</script>