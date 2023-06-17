<div class="data-form">
	<form id="form-{{$suffix}}" method="post" action="./">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tbody class="data-form-group">
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Dashboard name</span>
							<span class="data-form-desc">Title for the current dashboard</span>
						</label>
					</td>
					<td class="data-form-field">
						<input type="text" class="input" id="title-{{$suffix}}" name="title_{{$suffix}}" value="{{$settings.title}}" style="width:200px" />
					</td>
				</tr>
			</tbody>
		</table>
		<div class="data-form-controls" align="right">
			<button id="ui-save-{{$suffix}}">Save settings</button>
			<button id="ui-close-{{$suffix}}">Close</button>
		</div>
	</form>
</div>

<script type="text/javascript">
(function($) {
	$("#ui-save-{{$suffix}}").button({
		icons: { primary: 'ui-icon-circle-check' }
	}).click(function(){
		Dashboard.addDashboard({
			title: $('#title-{{$suffix}}').val()
		});
		Dashboard.dialog.close();
		return false;
	});

	$("#ui-close-{{$suffix}}").button({
		icons: { primary: 'ui-icon-circle-close' }
	}).click(function(){
		Dashboard.dialog.close();
		return false;
	});
})(jQuery);

</script>