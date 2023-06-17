{{$form_start}}
<div class="dashlet" style="margin-top:10px;">
	<table align="center" cellpadding="2" cellspacing="2" border="0" width="100%" style="border-collapse: collapse; border: 1px solid rgb(204, 204, 204);">
		<tbody>
			<tr>
				<table class="segPanel" align="center" cellpadding="2" cellspacing="2" border="0" width="100%" style="border-collapse: collapse; border: 1px solid rgb(204, 204, 204);">
					<tr>
						<td style="width:50px" nowrap="nowrap" align="right"><b>Test Group</b></td>
						<td nowrap="nowrap">{{$testGroup}}{{$testGroupid}}</td>
					</tr>
					<tr>
						<td style="width:50px" nowrap="nowrap" align="right"><b>Parameter Name</b></td>
						<td nowrap="nowrap">{{$paramName}}</td>
					</tr>
					<tr>
						<td style="width:50px" nowrap="nowrap" align="right"><b>Assign Param Group</b></td>
						<td nowrap="nowrap">{{$paramGroups}}</td>
					</tr>
					<tr>
						<td style="width:50px" nowrap="nowrap" align="right" ><b>Data Type</b></td>
						<td nowrap="nowrap">{{$dataTypes}}</td>
					</tr>
					<tr>
						<td style="width:50px" nowrap="nowrap" align="right"><b>Order Number</b></td>
						<td nowrap="nowrap">{{$orderNumber}}</td>
					</tr>
					<tr>
						<td style="width:50px" nowrap="nowrap" align="right"><b>Norm Type</b></td>
						<td nowrap="nowrap">{{$gender}}</td>
					</tr>
					<tr>
						<td style="width:50px" nowrap="nowrap" align="right"><b>SI Range</b></td>
						<td nowrap="nowrap">{{$siLow}}-{{$siHigh}}&nbsp;{{$siUnit}}</td>
					</tr>
					<tr>
						<td style="width:50px" nowrap="nowrap" align="right"><b>CU Range</b></td>
						<td nowrap="nowrap">{{$cuLow}}-{{$cuHigh}}&nbsp;{{$cuUnit}}</td>
					</tr>
					<tr>
						<td style="width:50px" nowrap="nowrap" align="center" colspan = '2'>{{$saveBtn}}&nbsp;{{$cancelBtn}}</td>
					</tr>
				</table>
			</tr>
		</tbody>
	</table>
</div>
{{$service_code}}
{{$mode}}
{{$param_id}}
{{$form_end}}
