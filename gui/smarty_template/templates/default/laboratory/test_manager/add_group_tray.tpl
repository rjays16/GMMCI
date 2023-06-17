{{$form_start}}
<div style="width:595px;">
	<table border="0" width="100%" class="Search">
		<tbody>
			<tr>
				<td class="segPanel">
					<table border="0" width="100%" class="Search">
						<tbody>
							<tr>
								<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; width:130px" align="right">Group Name:</td>
								<td align="left" valign="middle">&nbsp;{{$groupName}}</td>
							</tr>
							<tr>
								<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; width:130px" align="right">Form:</td>
								<td align="left" valign="middle">&nbsp;{{$specialForms}}</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="dashlet" style="margin-top:15px">
		<table border="0" width="100%">
			<tbody>
				<tr>
					<td align="left" colspan="2">{{$addItems}}{{$clearItems}}</td>
					<td align="right" colspan="2">{{$saveGroup}}{{$updateGroup}}{{$deleteGrp}}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div style="overflow-y:auto; height:290px;">
		<table id="grp-service-list" class="segList" width="100%" cellpadding="0" cellspacing="0" border="0">
			<thead>
				<tr>
					<!--<th width="1%" nowrap="nowrap">&nbsp;</th>-->
					<th width="10%" nowrap="nowrap" align="center">Service Code</th>
					<th width="30%" nowrap="nowrap" align="center">Description</th>
					<th width="10%" nowrap="nowrap" align="center">Order No.</th>
					<th width="1%" nowrap="nowrap" align="center">Options</th>
				</tr>
			</thead>
			<tbody>
				<tr id="empty_list"><td colspan="5">No services on the list..</td></tr>
			</tbody>
		</table>
		<input type="hidden" id="order_cnt" name="order_cnt" value="0"/>
	</div>
</div>
{{$submitted}}
{{$mode}}
{{$group_id}}
{{$form_end}}