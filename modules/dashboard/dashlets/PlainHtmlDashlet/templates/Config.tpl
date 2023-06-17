<div class="data-form">
	<form id="form-{{$dashlet.id}}" method="post" action="./">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tbody class="data-form-group">
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Content</span>
							<span class="data-form-desc">HTML content to be displayed</span>
						</label>
					</td>
					<td class="data-form-field">
						<textarea type="text" class="input" id="content-{{$dashlet.id}}" name="content" style="width:100%" rows="3">{{$preferences.content}}</textarea>
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