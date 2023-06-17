<div class="data-form">
	<form id="form-{{$dashlet.id}}" method="post" action="./">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tbody class="data-form-group">
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Skin</span>
							<span class="data-form-desc">Sets the clock's display theme</span>
						</label>
					</td>
					<td class="data-form-field">
						<select class="input" id="skin-{{$dashlet.id}}" name="skin">
							{{foreach from=$skins item=skin}}
							<option value="{{$skin}}" {{if $settings.clockSkin eq $skin}}selected="selected"{{/if}}>{{$skin}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Radius</span>
							<span class="data-form-desc">Sets the pixel-size of the clock's face</span>
						</label>
					</td>
					<td class="data-form-field">
						<input class="input" type="text" size="8" id="radius-{{$dashlet.id}}" name="radius" value="{{$settings.clockRadius}}" />
					</td>
				</tr>
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Digitial clock?</span>
							<span class="data-form-desc">Show/hide the digital clock</span>
						</label>
					</td>
					<td class="data-form-field">
						<input class="input" type="checkbox" size="15" id="digital-{{$dashlet.id}}" name="digital" value="1" {{if $settings.showDigital == 1}}checked="checked"{{/if}} />
					</td>
				</tr>
			</tbody>
		</table>
		<div class="data-form-controls" align="right">
			<button id="ui-save-{{$dashlet.id}}">Save settings</button>
			<button id="ui-close-{{$dashlet.id}}">Back</button>
		</div>
	</form>
</div>
<script type="text/javascript">
(function($) {
	$("#ui-save-{{$dashlet.id}}").button({
		icons: { primary: 'ui-icon-circle-check' }
	}).click(function() {
		Dashboard.dashlets.sendAction("{{$dashlet.id}}", "save", {
			data: $('#form-{{$dashlet.id}}').serializeArray()
		});
		return false;
	});

	$("#ui-close-{{$dashlet.id}}").button({
		icons: { primary: 'ui-icon-arrowreturnthick-1-w' }
	}).click(function() {
		Dashboard.dashlets.sendAction("{{$dashlet.id}}", "setMode", {mode:'view'});
		return false;
	});
})(jQuery);
</script>