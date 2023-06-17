{{$form_start}}
<div style="width:660px">
	<table border="0" cellspacing="1" cellpadding="0" width="100%" align="center" style="">
		<tbody>
			<tr>
				<td colspan="4" class="segPanelHeader">Template Details</td>
			</tr>
			<tr>
				<td class="segPanel">
					<table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
						<tbody>
							<tr>
								<td nowrap="nowrap" align="right" style="width:90px"><b>Template Name</b></td>
								<td nowrap="nowrap" align="left" style="width:400px">{{$template_search}}&nbsp;{{$search_btn}}</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="dashlet" style="margin-top:20px">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 11px Tahoma;">
			<tbody>
				<tr>
					<td width="30%" valign="top"><h1 style="white-space:nowrap">List of standard prescription</h1></td>
					<td align="right">{{$add_template}}</td>
				</tr>
			</tbody>
		</table>
		<div id="templates-list"></div>
	</div>
</div>

<div id="add-template" style="display:none">
	<div class="dashlet" style="margin-top:5px">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 11px Tahoma;">
			<tbody>
				<tr>
					<td width="30%" valign="top"><h1 style="white-space:nowrap">Template details</h1></td>
					<td align="right">{{$save_template}}{{$close_template}}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<table border="0" cellspacing="1" cellpadding="2" width="99%" align="center" style="font:12px Arial">
		<tbody>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="20%"><strong>Name</strong></td>
				<td align="left" valign="middle" width="*" nowrap="nowrap">{{$template_name}}</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="20%"><strong>Owner</strong></td>
				<td align="left" valign="middle" width="*" nowrap="nowrap">{{$template_owner}}{{$ownerHidden}}</td>
			</tr>
			<tr>
				<td class="segPanel" align="right" valign="middle" width="20%"><strong>Item Name</strong></td>
				<td align="left" valign="middle" width="*" nowrap="nowrap">{{$template_itemname}}{{$add_drug_btn}}</td>
			</tr>
		</tbody>
	</table>
	<table id="prescriptionlist" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:5px">
	<thead>
		<tr class="nav">
			<th colspan="10" align="left">List of prescribed medicines</th>
		</tr>
		<tr>
			<th width="*" nowrap="nowrap">Drug name</th>
			<th width="10%">Quantity</th>
			<th width="25%">Dosage</th>
			<th width="20%">Period</th>
			<th width="5%"></th>
		</tr>
	</thead>
	<tbody id="prescriptionlist-body">
		<tr id="row_empty"><td colspan="6">No medicines added...</td></tr>
	</tbody>
</table>
</div>
<input type="hidden" id="drug_code"/>
<input type="hidden" id="drug_name"/>
<input type="hidden" id="drug_generic"/>
<input type="hidden" id="modeval" value="save"/>
<input type="hidden" id="template_id"/>
{{$form_end}}