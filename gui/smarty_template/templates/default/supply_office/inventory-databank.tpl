{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<!--{{$sFormStart}}-->

<style type="text/css">
<!--
	.tabFrame {
		padding:5px;
		min-height:150px;
	}
-->
</style>

<div style="width:100%">
	<table border="0" cellspacing="1" cellpadding="2" width="60%" align="center" style="">
		<tbody>
			<tr>
				<td class="segPanelHeader">
					Search pharmacy product
				</td>
			</tr>
			<tr>
				<td class="segPanel" style="padding:2px">
					<table border="0" cellspacing="0" cellpadding="2" width="100%" align="center" style="font:normal 12px Arial">
						<tr>
							<td colspan="3" style="height:10px"></td>
						<tr>
						<tr>
							<td align="right" valign="middle" width="20%"><strong>Code/Name</strong></td>
							<td align="left" valign="middle" width="30%" style="">
								{{$sCodeName}}
							</td>
							<td align="left" valign="middle" width="*" style="">
								<strong>Search products by code or name</strong>
							</td>
						</tr>
						<tr>
							<td align="right" valign="middle"><strong>Generic name</strong></td>
							<td align="left" valign="middle" style="border-right:0">
								{{$sGenericName}}
							</td>
							<td align="left" valign="middle" style="border-left:0">
								<strong>Search products by generic name</strong>
							</td>
						</tr>
						<tr>
							<td align="right" valign="middle"><strong>Type</strong></td>
							<td align="left" valign="middle" style="border-right:0">
								{{$sProdClass}}
							</td>
							<td align="left" valign="middle" style="border-left:0">
								<strong>Filter products by type</strong>
							</td>
						</tr>
						<tr>
							<td align="right" valign="middle"><strong>Deleted items</strong></td>
							<td align="left" valign="middle" style="border-right:0">
								<select id="show_deleted" class="segInput">
									<option value="exclude">Exclude deleted items</option>
									<option value="all">Show all</option>
									<option value="show">Show only deleted items</option>
								</select>
							</td>
							<td align="left" valign="middle" style="border-left:0">
								<strong>Include deleted items in listing</strong>
							</td>
						</tr>
						<tr>
							<td align="right" valign="middle"><strong>Area</strong></td>
							<td align="left" valign="middle" style="border-right:0">
								{{$sAreaList}}
							</td>
							<td align="left" valign="middle" style="border-left:0">
								<strong>Filter Hospital Stock by area</strong>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td colspan="2" style="height:2px">
								<input id="btnsearch" class="segButton" type="button" value="Search" onclick="search()"/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<br />
	<div align="left" style="width:85%">
		<div style="padding:2px 0px">
			{{$sCreateProduct}}{{$sCreateClassification}}
		</div>
		{{$sProductList}}

		<div style="margin-top:2px">
			<span style="font:bold 11px Arial">Legend:</span>
			<span style="margin-left:5px; color:#000066">
				Medicine
				<img src="{{$sRootPath}}gui/img/common/default/pharma_meds.png" align="absmiddle" />
			</span>
			<span style="margin-left:5px; color:#006600">
				Supplies
				<img src="{{$sRootPath}}gui/img/common/default/pharma_supplies.png" align="absmiddle" />
			</span>
			<span style="margin-left:5px; color:#000066">
				Non-Med Supplies
				<img src="{{$sRootPath}}gui/img/common/default/pharma_nonmeds.png" align="absmiddle" />
			</span>
			<span style="margin-left:5px; color:#000066">
				Equipment
				<img src="{{$sRootPath}}gui/img/common/default/pharma_equip.png" align="absmiddle" />
			</span>
			<span style="margin-left:5px; color:#000066">
				Blood
				<img src="{{$sRootPath}}gui/img/common/default/pharma_blood.png" align="absmiddle" />
			</span>
			<span style="margin-left:5px; color:#000066">
				Housekeeping
				<img src="{{$sRootPath}}gui/img/common/default/pharma_housekeeping.png" align="absmiddle" />
			</span><br>
			<span style="margin-left:5px; color:#000066">
				No Classification
				<img src="{{$sRootPath}}gui/img/common/default/blank_tag.png" align="absmiddle" />
			</span>
		</div>
	</div>
</div>

{{$sHiddenInputs}}
{{$jsCalendarSetup}}
<!--{{$sFormEnd}}-->
{{$sTailScripts}}
