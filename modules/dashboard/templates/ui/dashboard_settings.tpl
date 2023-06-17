<div class="data-form">
<!--	<form id="form-{{$suffix}}" method="post" action="./">-->
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
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Delete dashboard</span>
							<span class="data-form-desc" style="color:#c00000">Delete the current active dashboard?</span>
						</label>
					</td>
					<td class="data-form-field">
						<input type="checkbox" class="input" id="delete-{{$suffix}}" name="delete_{{$suffix}}" value="delete" />
					</td>
				</tr>
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Column layout</span>
							<span class="data-form-desc">Select the number of columns for this dashboard</span>
						</label>
					</td>
					<td class="data-form-field">
						<div id="layout-buttonset-{{$suffix}}">
							<input type="radio" id="layout-1-{{$suffix}}" name="column_layout_{{$suffix}}" {{if $settings.columns == 1 }}checked="checked"{{/if}} value="1" /><label for="layout-1-{{$suffix}}">One</label>
							<input type="radio" id="layout-2-{{$suffix}}" name="column_layout_{{$suffix}}" {{if $settings.columns == 2 }}checked="checked"{{/if}} value="2" /><label for="layout-2-{{$suffix}}">Two</label>
							<input type="radio" id="layout-3-{{$suffix}}" name="column_layout_{{$suffix}}" {{if $settings.columns == 3 }}checked="checked"{{/if}} value="3" /><label for="layout-3-{{$suffix}}">Three</label>
						</div>
					</td>
				</tr>
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Column widths</span>
							<span class="data-form-desc">Specify the widths for each dashlet column</span>
						</label>
					</td>
					<td class="data-form-fields">
						<div id="layout-widths-{{$suffix}}" class="ui-slider"></div>
						<table id="slider-counter-{{$suffix}}" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:2px">
							<tr height="">
								<td width="" align="center" class="slider-1 slider-2 slider-3" style="background-color: #eaeaea">
									<input type="text" id="widths-0-{{$suffix}}" style="text-align:center; border:0; color:#f6931f; font-weight:bold; width:50px; background-color:transparent" value=""/>
								</td>
								<td width="" align="center" class="slider-2 slider-3">
									<input type="text" id="widths-1-{{$suffix}}" style="text-align:center; border:0; color:#f6931f; font-weight:bold; width:50px; background-color:transparent" value=""/>
								</td>
								<td width="" align="center" class="slider-3" style="background-color: #eaeaea">
									<input type="text" id="widths-2-{{$suffix}}" style="text-align:center; border:0; color:#f6931f; font-weight:bold; width:50px; background-color:transparent" value=""/>
								</td>
							</tr>
						</table>

					</td>
				</tr>
			</tbody>
		</table>
		<div class="data-form-controls" align="right">
			<button id="ui-save-{{$suffix}}">Save settings</button>
			<button id="ui-close-{{$suffix}}">Close</button>
		</div>
<!--	</form>-->
</div>

<script type="text/javascript">
(function($) {
	$("#ui-save-{{$suffix}}").button({
		icons: { primary: 'ui-icon-circle-check' }
	}).click(function() {
		if ($("#delete-{{$suffix}}:checked").val())
		{
			if (confirm('Do you really wish to delete this dashboard?'))
			{
				Dashboard.dialog.close();
				Dashboard.scrap();
				return false;
			}
			else
			{
				return false;
			}
		}

		var columns = parseInt($("#layout-buttonset-{{$suffix}}").find(":checked").first().val());
		var value = $("#layout-widths-{{$suffix}}").slider("option", "value") || 100;
		var values = $("#layout-widths-{{$suffix}}").slider("option", "values") || [value,100];

		columnWidths = [];
		columnWidths.push( columns==1 ? 100 : values[0] );
		if (columns>1)
			columnWidths.push( values[1]-values[0] );
		if (columns>2)
			columnWidths.push( 100-values[1] );

		var title = $("#title-{{$suffix}}").val();
		Dashboard.dialog.close();
		Dashboard.layout({
			title: title,
			columns: columns,
			columnWidths : columnWidths
		});
		return false;
	});

	$("#ui-close-{{$suffix}}").button({
		icons: { primary: 'ui-icon-circle-close' }
	}).click(function(){
		Dashboard.dialog.close();
		return false;
	});

	$("#layout-buttonset-{{$suffix}}").buttonset().click(function() {
		var columns = parseInt( $("#layout-buttonset-{{$suffix}}").find(":checked").first().val() );
		var columnWidths = [0,0,0];
		for (var i=0; i<columns; i++)
			columnWidths[i] = parseInt( 100.0/columns );
		Dashboard.utilities._updateCwSliders('{{$suffix}}', columns, columnWidths);
		return true;
	});

	Dashboard.utilities._updateCwSliders( '{{$suffix}}', {{$settings.columns}}, {{$settings.widths}} );
})(jQuery);

</script>