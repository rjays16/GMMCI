<div class="data-form">
	<form id="form-{{$dashlet.id}}" method="post" action="./">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tbody class="data-form-group">
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Page size</span>
							<span class="data-form-desc">Maximum number of items to be displayed per page</span>
						</label>
					</td>
					<td class="data-form-field">
						<select type="text" class="input" id="pageSize-{{$dashlet.id}}" name="pageSize">
							<option value="5" {{if {{$settings.pageSize eq 5}}selected="selected"{{/if}}>5</option>
							<option value="10" {{if {{$settings.pageSize eq 10}}selected="selected"{{/if}}>10</option>
							<option value="20" {{if {{$settings.pageSize eq 20}}selected="selected"{{/if}}>20</option>
						</select>
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
	$('#form-{{$dashlet.id}}').submit( function() {
		return false;
	});
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
		Dashboard.dashlets.sendAction("{{$dashlet.id}}", "setMode", "view");
		return false;
	});
})(jQuery);
</script>