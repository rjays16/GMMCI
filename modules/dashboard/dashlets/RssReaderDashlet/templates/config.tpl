<div class="data-form">
	<form id="form-{{$dashlet.id}}" method="post" action="./">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tbody class="data-form-group">
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">RSS link</span>
							<span class="data-form-desc">URL of the RSS feed</span>
						</label>
					</td>
					<td class="data-form-field">
						<input type="text" class="input" id="url-{{$dashlet.id}}" name="url" style="width:90%" value="{{$preferences.url}}" />
					</td>
				</tr>
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Max. items</span>
							<span class="data-form-desc">Maximum number of items to be shown</span>
						</label>
					</td>
					<td class="data-form-field">
						<input type="text" class="input" id="max-{{$dashlet.id}}" name="maxItems" style="width:100px" value="{{$preferences.maxItems}}" />
					</td>
				</tr>
			</tbody>
		</table>
		<div class="data-form-controls" align="right">
			<button id="ui-save-{{$dashlet.id}}">Save settings</button>
			<button id="ui-close-{{$dashlet.id}}">Close</button>
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
		Dashboard.dashlets.doneEdit();
		return false;
	});

	$("#ui-close-{{$dashlet.id}}").button({
		icons: { primary: 'ui-icon-circle-close' }
	}).click(function() {
		Dashboard.dashlets.doneEdit();
		return false;
	});
})(jQuery);
</script>