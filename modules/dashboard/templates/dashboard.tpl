<div id="dashboard-area" style="">
	<div id="header">
		<div id="logo"><a href="#"></a></div>
		<div id="userPanel">
			<div id="welcome">Welcome, <span id="user">{{$loginName}}</span>!</div>
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
{{foreach from=$tabs item=tab}}
				<li class="{{if $tab.isActive}}ui-state-default active{{/if}}">
					<a href="{{if $tab.url}}{{$tab.url}}{{else}}#{{/if}}">
						{{if $tab.icon}}<span class="ui-icon ui-icon-{{$tab.icon}}"></span>{{/if}}
						<span id="title-{{$tab.id}}">{{$tab.title}}</span>
					</a>
				</li>
{{/foreach}}
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
				<li id="user-panel-welcome"><span>Welcome to your dashboard, <strong>{{$user.fullname}}</strong>!</span></li>
			</ul>
		</div>
		<table class="dashboardColumns" cellpadding="0" cellspacing="0" border="0" width="100%" style="empty-cells:hide; table-layout: fixed; border-spacing:0;">
			<tbody>
				<tr id="dashboard-column-container">
{{foreach from=$dashboard.columns key=key item=column}}
					<td class="dashlet-column flow-height" style="vertical-align: top; width:{{$column.width}}">
						<ul class="dashletList" columnIndex="{{$key}}"></ul>
					</td>
{{/foreach}}
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