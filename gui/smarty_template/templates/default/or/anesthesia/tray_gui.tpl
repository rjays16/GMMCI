{{$sFormStart}}
<div style="width:100%">
	<table border="0" width="20%" class="Search" align="center">
			<tbody>
				<tr>
					<td class="segPanelHeader" colspan="4">Add anesthesia procedure</td>
				</tr>
				<tr>
					<td class="segPanel" style="white-space:nowrap"><label>Category id</label></td>
					<td class="segPanel" align="left" valign="middle">
						{{$categoryId}}
					</td>
					<td class="segPanel" style="white-space:nowrap"><label>Category name</label></td>
					<td class="segPanel" align="left" valign="middle">
						{{$categoryName}}
					</td>
				</tr>
				<tr>
					<td class="segPanel"><label>Specific id</label></td>
					<td class="segPanel" align="left" style="white-space:nowrap" valign="middle">{{$specificId}}</td>
					<td class="segPanel"><label>Specific name</label></td>
					<td class="segPanel" align="left" style="white-space:nowrap" valign="middle">{{$specificName}}&nbsp;{{$addSpecific}}
					</td>
				</tr>
			</tbody>
		</table>
</div>
<br>
<div id="anesthesia_specific_list" style="width:90%">
</div>
<div id="anesthesia_specific_table" style="padding:2px; height:150px; overflow-y:auto; display:none; background-color:#e5e5e5; border:1px solid #8cadc0;">
	<table id="specific_list" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th>Specific Id</th>
				<th>Specific Name</th>
				<th>Options</th>
				<th></th>
			</tr>
		</thead>
		<tbody id="specific_list-body">
			<tr id="row_specific_null" style="display"><td colspan="5" style="">No specific anesthesia added...</td></tr>
		</tbody>
	</table>
</div>
<div style="text-align:right; padding:5px;">
{{$saveBtn}}&nbsp;{{$cancelBtn}}
</div>
{{$sFormEnd}}