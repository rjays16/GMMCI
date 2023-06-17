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
{foreach name=search key=searchId item=searchItem from=$listview.search}
	<li><a href="#{$searchId}">{$searchItem.label}</a></li>
{/foreach}
	</ul>
{foreach name=search key=searchId item=searchItem from=$listview.search }
	<div id="{$searchId}" class="ui-tabs-hide">
		<table width="100%" cellpadding="0" cellspacing="4" border="0" style="empty-cells:show">
		{eval var=$searchItem.columns|default:1 assign="columns"}
		{eval var=$searchItem.widths.label/$columns assign="labelWidth"}
		{eval var=$searchItem.widths.field/$columns assign="fieldWidth"}
		{eval var=$searchItem.widths.filler/$columns assign="fillerWidth"}
		{counter name=filterCounter start=0 assign=fCntr}
		{foreach name=filters key=filterId item=filter from=$filters[$searchId]}
			{if $columns > 1}
				{if $fCntr is div by $columns}
			<tr>
				{/if}
				<td id="{$filterId|cat:"_lbl"}" class="filterLabel" width="{$labelWidth}%" style="white-space:nowrap">{$filter.label}:</td>
				<td id="{$filterId|cat:"_fld"}" class="filterField" width="{$fieldWidth}%" style="white-space:nowrap">{$filter.field}</td>
				<td width="{$fillerWidth}%"></td>
				{counter name=filterCounter}
				{if $fCntr is div by $columns or $smarty.foreach.filters.last}
				<td width="*"></td>
			</tr>
				{/if}
		{else}
			<tr>
				<td id="{$filterId|cat:"_lbl"}" class="filterLabel" width="{$labelWidth}%" style="white-space:nowrap">{$filter.label}:</td>
				<td id="{$filterId|cat:"_fld"}" class="filterField" width="{$fieldWidth}%" style="white-space:nowrap">{$filter.field}</td>
				<td width="{$fillerWidth}%"></td>
			</tr>
		{/if}
	{/foreach}
	</table>
</div>
{/foreach}
</div>
<div style="margin:3px 2px; text-align:left; white-space:nowrap">
<button class="button" onclick="CodeTable.refresh(); return false"><img src="{$root_path}gui/img/common/default/magnifier.png"/>Search</button>
<button class="button" onclick="openEditView(); return false"><img src="{$root_path}gui/img/common/default/add.png"/>New item</button>
</div>
<div id="listview"></div>
<div id="listview_history" style="display:none"></div>
<iframe id="listview_edit" frameBorder="0" scrolling="auto" style="display:none"></iframe>
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">