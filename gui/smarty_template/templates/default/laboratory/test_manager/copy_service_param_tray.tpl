{{$form_start}}
<div class="dashlet" style="margin-top:10px;">
	<table align="center" cellpadding="2" cellspacing="2" border="0" width="100%" style="border-collapse: collapse; border: 1px solid rgb(204, 204, 204);">
		<tbody>
			<tr>
				<table class="segPanel" align="center" cellpadding="2" cellspacing="2" border="0" width="100%" style="border-collapse: collapse; border: 1px solid rgb(204, 204, 204);">
					<tr>
						<td colspan="2"  style="font:bold 12px Arial;">Copy parameters from:</td>
					</tr>
					<tr>
						<td>{{$serviceWithParamList}}</td>
					</tr>
					<tr id="view-param-list" style="display:none">
						<td>
							<div id="param-div" style="overflow-y:auto; height:230px;">
								<table id="param-list" class="segList" width="100%" cellpadding="0" cellspacing="0" border="0">
									<thead>
										<tr>
											<th width="1%" nowrap="nowrap"><input type="checkbox" id="copy_all" name="copy_all" onclick="checkAllParams(this.id)"/></th>
											<th width="10%" nowrap="nowrap" align="center">Parameter</th>
											<th width="10%" nowrap="nowrap" align="center">Order No.</th>
											<th width="30%" nowrap="nowrap" align="center">Group</th>
										</tr>
									</thead>
									<tbody id="param-list-body">

									</tbody>
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<td>{{$copyBtn}}{{$undoBtn}}{{$cancelBtn}}</td>
					</tr>
				</table>
			</tr>
		</tbody>
	</table>
</div>
{{$group_id}}
{{$service_id}}
{{$form_end}}