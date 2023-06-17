<div class="dashlet">
	<div class="dashletHeader">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tbody>
				<tr>
					<td class="dashletTitle">
						<span id="title_{{$dashlet.id}}"
							style="padding-left:20px; background-position: left center; background-repeat: no-repeat; background-image: url({{$dashlet.icon}})"
							ondblclick="var p;if (p = prompt('Enter a new title for this Dashlet: ')) Dashboard.dashlets.sendAction('{{$dashlet.id}}', 'setTitle', { title: p}); return false;">{{$dashlet.title}}</span>
					</td>
					<td id="controls_{{$dashlet.id}}" class="dashletControls">
{{foreach from=$customModes item=mode}}
						<a id="custom_mode_{{$mode.name}}_{{$dashlet.id}}" href="#"><span class="dashlet-icon icon-{{$mode.icon}}"></span></a>
						<script type="text/javascript">
							(function($) {
								$("#custom_mode_{{$mode.name}}_{{$dashlet.id}}").click( function() {
									Dashboard.sendAction("{{$dashlet.id}}", "changeMode", { mode: "{{$mode.name}}"})
									return false;
								});
							})(jQuery);
						</script>
{{/foreach}}
						{{if $dashlet.state eq "normal"}}<a id="controls_up_{{$dashlet.id}}" href="#" onclick="Dashboard.dashlets.minimize('{{$dashlet.id}}'); return false;"><span class="dashlet-icon icon-up"></span></a>{{/if}}
						{{if $dashlet.state eq "minimized"}}<a id="controls_down_{{$dashlet.id}}" href="#" onclick="Dashboard.dashlets.restore('{{$dashlet.id}}'); return false;"><span class="dashlet-icon icon-down"></span></a>{{/if}}
						<a id="controls_refresh_{{$dashlet.id}}" href="#" onclick="Dashboard.dashlets.refresh('{{$dashlet.id}}'); return false;"><span class="dashlet-icon icon-refresh"></span></a>
						<a id="controls_edit_{{$dashlet.id}}" href="#" onclick="Dashboard.dashlets.sendAction('{{$dashlet.id}}', 'setMode', { mode: 'edit' }); return false;"><span class="dashlet-icon icon-edit"></span></a>
						<a id="controls_delete_{{$dashlet.id}}" href="#" onclick="if (confirm('Do you wish to remove this Dashlet?')) { Dashboard.dashlets.remove({dashlet:'{{$dashlet.id}}'}) } return false;"><span class="dashlet-icon icon-delete"></span></a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="dashletBody" {{if $dashlet.state eq "minimized"}}style="display:none"{{/if}}>
		<div id="content_{{$dashlet.id}}" class="dashletContents" style="height:{{$preferences.contentHeight|default:"auto"}}">
{{$dashlet.contents}}
		</div>
	</div>
	<div id="footer_{{$dashlet.id}}" class="dashletFooter"></div>
</div>