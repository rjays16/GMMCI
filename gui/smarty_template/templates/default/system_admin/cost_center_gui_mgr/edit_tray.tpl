{{$form_start}}
	<div style="padding:4px;overflow-y:auto" align="center">
		 <table border="0" cellspacing="2" cellpadding="1" width="60%" align="center">
			<tr>
				<td class="segPanelHeader" colspan="2">Details</td>
			</tr>
			<tr>
				<td colspan="2">
					<table class="segPanel" border="0" cellspacing="2" cellpadding="1" width="100%" align="center">
						<tr>
							<td><strong>Cost Center:</strong></td>
							<td>{{$sCostCenters}}</td>
						</tr>
						<tr id="lab_section_row" style="display:none">
							<td><strong>Sections:</strong></td>
							<td>{{$sLabSections}}</td>
						</tr>
						<tr id="radio_section_row" style="display:none">
							<td><strong>Area:</strong></td>
							<td>{{$sRadioArea}}</td>
						</tr>
						<tr id="radio_specific_row" style="display:none">
							<td><strong>Sections:</strong></td>
							<td>{{$sRadioSections}}</td>
						</tr>
						<tr>
							<td><strong>No of Rows:</strong></td>
							<td>{{$sRow}}</td>
						</tr>
						<tr>
							<td><strong>No of Columns:</strong></td>
							<td>{{$sColumn}}</td>
						</tr>
					</table>
				</td>
			</tr>
			<!--<tr>
				<td class="segPanel"><strong>Cost Center:</strong></td>
				<td class="segPanel">{{$sCostCenters}}</td>
			</tr>
			<tr id="lab_section_row" style="display:none">
				<td class="segPanel"><strong>Sections:</strong></td>
				<td class="segPanel">{{$sLabSections}}</td>
			</tr>
			<tr id="radio_section_row" style="display:none">
				<td class="segPanel"><strong>Area:</strong></td>
				<td class="segPanel">{{$sRadioArea}}</td>
			</tr>
			<tr id="radio_specific_row" style="display:none">
				<td class="segPanel"><strong>Sections:</strong></td>
				<td class="segPanel">{{$sRadioSections}}</td>
			</tr>
			<tr>
				<td class="segPanel"><strong>No of Rows:</strong></td>
				<td class="segPanel">{{$sRow}}</td>
			</tr>
			<tr>
				<td class="segPanel"><strong>No of Columns:</strong></td>
				<td class="segPanel">{{$sColumn}}</td>
			</tr>-->
		 </table>

		 <div align="center" style="width:80%">
			 <div style="width:100%; text-align:right; padding:2px 4px">
				<img src="../../../images/btn_add.gif" style="cursor:pointer" align="middle" id="add_gui" onclick="add_rows_cols()">
			 </div>
			 <div class="segPanel" id="service_list" align="center" style="width:100%; "></div>
		 </div>

		 <div id="control_buttons" style="display:none;width:165px;height:30px">
		 {{$package_submit}}
		 {{$package_cancel}}
		 {{$is_submitted}}
		 {{$edit_nr}}
		 </div>
	</div>
<br/>
{{$form_end}}
