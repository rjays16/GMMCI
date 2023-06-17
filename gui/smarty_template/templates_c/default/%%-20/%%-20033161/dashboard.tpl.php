<?php /* Smarty version 2.6.0, created on 2017-11-28 16:37:20
         compiled from templates/dashboard.tpl */ ?>
<div id="dashboard-area" style="">
	<div id="header">
		<div id="logo"><a href="#"></a></div>
		<div id="userPanel">
			<div id="welcome">Welcome, <span id="user"><?php echo $this->_tpl_vars['loginName']; ?>
</span>!</div>
			<div id="userLinks">
				<ul>
					<li><a href="#">Logout</a>
					<li><a href="#">my.panel</a>
					<li><a href="#">Help</a>
					<li><a href="#">About</a>
			</div>
		</div>
	</div>
	<div id="content">
		<div class="dashlet-tabs">
			<ul id="dashboard-tabs" >
<?php if (count($_from = (array)$this->_tpl_vars['tabs'])):
    foreach ($_from as $this->_tpl_vars['tab']):
?>
				<li class="<?php if ($this->_tpl_vars['tab']['isActive']): ?>ui-state-default active<?php endif; ?>">
					<a href="<?php if ($this->_tpl_vars['tab']['url']):  echo $this->_tpl_vars['tab']['url'];  else: ?>#<?php endif; ?>">
						<?php if ($this->_tpl_vars['tab']['icon']): ?><span class="ui-icon ui-icon-<?php echo $this->_tpl_vars['tab']['icon']; ?>
"></span><?php endif; ?>
						<span id="title-<?php echo $this->_tpl_vars['tab']['id']; ?>
"><?php echo $this->_tpl_vars['tab']['title']; ?>
</span>
					</a>
				</li>
<?php endforeach; unset($_from); endif; ?>
				<li style="margin-left:4px;">
					<a id="dashboard-create" href="#" style="">
						<span class="dashlet-button button-add"></span>
						<span>Add dashboard</span>
					</a>
				</li>
				<li>
					<a id="dashboard-settings" href="#" style="">
						<span class="dashlet-button button-config"></span>
						<span>Settings</span>
					</a>
				</li>
				<li>
					<a id="dashlet-add" href="#" style="">
						<span class="dashlet-button button-plugin"></span>
						<span>Add dashlet</span>
					</a>
				</li>
			</ul>
		</div>
		<div id="user-panel">
			<ul>
				<li id="user-panel-welcome"><span>Welcome to your dashboard, <strong><?php echo $this->_tpl_vars['user']['fullname']; ?>
</strong>!</span></li>
			</ul>
		</div>
		<table class="dashboardColumns" cellpadding="0" cellspacing="0" border="0" width="100%" style="empty-cells:hide; table-layout: fixed; border-spacing:0;">
			<tbody>
				<tr id="dashboard-column-container">
<?php if (count($_from = (array)$this->_tpl_vars['dashboard']['columns'])):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['column']):
?>
					<td class="dashlet-column flow-height" style="vertical-align: top; width:<?php echo $this->_tpl_vars['column']['width']; ?>
">
						<ul class="dashletList" columnIndex="<?php echo $this->_tpl_vars['key']; ?>
"></ul>
					</td>
<?php endforeach; unset($_from); endif; ?>
				</tr>
			</tbody>
		</table>
	</div>
	<!--<div id="footer"></div>-->
</div>
<div id="dashboard-ui-launcher" class="display:none" style="padding:0; overflow:hidden">
	<iframe id="dashboard-ui-launcher-iframe" class="" scrolling="auto" frameborder="0"></iframe>
</div>
<div id="dashboard-ui-dialog" class="display:none" style="padding:4px"><div id="dashboard-ui-dialog-contents" class=""></div></div>
<div id="config-dialog" class="display:none"><div id="config-dialog-contents" class=""></div></div>