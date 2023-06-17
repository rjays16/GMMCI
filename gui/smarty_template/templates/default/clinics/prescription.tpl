{{*created by cha Feb 4, 2010*}}
{{$sFormStart}}
<div align="left" style="width: 775px; margin-top:4px">
    <label style="color:#2d2d2d; font-weight: bold">DD/DDP License No.:</label>
    {{$doctorLicenseNr}}
</div>
<div align="center" style="width: 775px; margin-top:4px">
	<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%;margin:4px">
		<tr>
			<td class="segPanelHeader">Patient details</td>
		</tr>
		<tr>
			<td class="segPanel" align="left" valign="top">
				<table width="100%" border="0" cellpadding="0" cellspacing="2" style="" >
					<tr>
						<td width="50%">

							<table id="" width="100%" border="0" cellpadding="0" cellspacing="2" style="font:12px Arial bold;">
								<tbody>
									<tr>
										<td width="" align="right" valign="middle"><label>PID:</label></td>
										<td valign="middle">{{$sPatientID}}</td>
									</tr>
                                    <tr>
                                        <td width="" align="right" valign="middle"><label>Encounter no.:</label></td>
                                        <td valign="middle">{{$sPatientEnc}}</td>
                                    </tr>
									<tr>
										<td align="right" valign="middle"><label>Patient:</label></td>
										<td valign="middle" nowrap="nowrap">
											{{$sPatientName}} {{$sSelectEnc}}{{$sClearEnc}}
										</td>
									</tr>
									<tr>
										<td align="right" valign="middle"><label>Address:</label></td>
										<td valign="middle">{{$sPatientAddress}}</td>
									</tr>
									<tr>
										<td align="right" valign="middle"><label>Age : </label></td>
										<td valign="middle">{{$sPatientAge}}</td>
									</tr>
									<tr>
										<td align="right">Gender:</label></td>
										<td valign="middle">{{$sPatientGender}}</td>
									</tr>
								</tbody>
							</table>
						</td>

						<td width="*" style="vertical-align: top;">
							<table id="" width="100%" border="0" cellpadding="0" cellspacing="2" style="font:12px Arial bold;">
								<tbody>
									<tr>
										<td align="right" valign="middle"><label>Prescription Date: </label></td>
										<td>
											{{$sRequestDate}}
											{{$sCalendarIcon}}
											{{$jsCalendarSetup}}
										</td>
									</tr>
									<tr>
										<td align="right" valign="middle">
                                            <label>Clinical impression:</label>
                                        </td>
										<td valign="middle">
                                            <button type="button" class="segButton" onclick="$J('#clinical-impression').val(''); return false"><img src="{{$sRootPath}}gui/img/common/default/delete.png"/>Clear</button>
                                        </td>
									</tr>
									<tr>
										<td align="center" valign="middle" colspan="2">
                                            <textarea id="clinical-impression" name="clinical_impression" class="segInput" style="width: 90%" rows="4">{{$clinicalImpression}}</textarea>
                                        </td>
									</tr>
								</tbody>
							</table>
						</td>

					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>

<div class="segContentPane" style="width: 775px; padding: 4px;">
	<div style="width:100%; text-align:left;">
		<div class="dashlet" style="margin-top: 6px; padding: 8px 4px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tbody>
					<tr>
						<td width="1%" align="left" nowrap="nowrap">
							<button type="button" class="segButton" onclick="searchDrug(); return false"><img src="{{$sRootPath}}gui/img/common/default/pill_add.png"/>Add drug</button>
							<button type="button" class="segButton" onclick="openPrescriptionTemplates(); return false"><img src="{{$sRootPath}}gui/img/common/default/table_multiple.png"/>Templates</button>
							<!--<button class="segButton" onclick="openPatientHistory(); return false"><img src="{{$sRootPath}}gui/img/common/default/calendar.png"/>Patient history</button>-->
						</td>
						<td width="*" align="right" nowrap="nowrap">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>


	<table id="prescriptionlist" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
		<thead>
			<tr class="nav">
				<th colspan="10" align="left">List of prescribed medicines</th>
			</tr>
			<tr>
				<th width="*" nowrap="nowrap">Drug name</th>
				<th width="10%">Availability</th>
				<th width="10%">Quantity</th>
				<th width="20%">Dosage</th>
				<th width="15%">Period</th>
				<th width="3%"></th>
			</tr>
		</thead>
		<tbody id="prescriptionlist-body">
			<tr id="row_empty"><td colspan="6">No medicines added...</td></tr>
		</tbody>
	</table>

	<table width="100%" border="0" cellpadding="2" cellspacing="0" style="margin-top: 10px; font:12px Arial bold;">
		<tr>
			<td colspan="3" align="left" valign="top" style="padding:4px"><label>Special Instructions:</label></td>
		</tr>
		<tr>
			<td colspan="3" align="left" valign="middle">
				<textarea class="segInput" id="instructions" name="instructions" rows="2" style="width:100%"></textarea>
			</td>
		</tr>
		<tr style="height:6px"></td>
		<tr>
			<td width="1%" align="left" valign="top">
				<input type="checkbox" class="input" name="is_save" id="is_save" onclick="$('template_name').disabled = !this.checked; $('template_name').focus()"/>
			</td>
			<td width="1%" align="left" nowrap="nowrap">
				<label for="is_save" class="segInput">Save into prescription template</label>
			</td>
			<td width="*" align="left" style="padding-left:10px">
				<label>Template name:</label>
				<input type="text" id="template_name" name="template_name" class="segInput" value="" disabled="disabled"/>
			</td>
		</tr>
	</table>
	<div style="text-align:left; margin-top: 20px">
		<button id="save_prescription" type="button" class="segButton" onclick="savePrescription();return false;"><img src="{{$sRootPath}}gui/img/common/default/tag_blue.png" />Save prescription</button>
		<button id="print_prescription" type="button" class="segButton" onclick="printPrescription();return false;" style="display:none"><img src="{{$sRootPath}}gui/img/common/default/page_white_acrobat.png" />Print prescription</button>
		<button id="done_prescription" type="button" class="segButton" onclick="self.close();"><img src="{{$sRootPath}}gui/img/common/default/cancel.png" />Close</button>
	</div>
</div>

<div id="select-drug" style="display:none">
	<table id="" border="0" cellspacing="2" cellpadding="0" width="100%">
		<tbody>
			<tr>
				<td>
					<div class="segPanel" style="padding: 5px">
						<input id="search-meds" type="text" class="segInput" style="width:300px; padding:2px; font:bold 14px Arial; color: #006" value=""/>
						<button class="segButton" type="button" onclick="addEmptyDrug(); return false;" id="addemptydrug_btn"><img src="{{$sRootPath}}gui/img/common/default/pill_add.png"/>Add to Rx</button>
                        <button class="segButton" type="button" onclick="addExternalDrug(); return false"><img src="{{$sRootPath}}gui/img/common/default/pill_add.png"/>Add external</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="dashlet" style="margin-top:15px">
						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 11px Tahoma;">
							<tbody>
								<tr>
									<td width="30%" valign="top"><h1 style="white-space:nowrap">List of recent medicines</h1></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div id="items-list" style="width: 100%;"></div>
				</td>
			</tr>
	</table>
</div>
<div id="select-template" style="display:none;">
	<table id="" border="0" cellspacing="2" cellpadding="0" width="100%">
		<tbody>
			<tr>
				<td>
					<div class="segPanel" style="padding: 5px">
						<label>Template Name</label>
						<input id="search-template" type="text" class="segInput" style="width:300px; padding:2px; font:bold 14px Arial; color: #4d4d4d" value="" />
						<button type="button" class="segButton" onclick="searchTemplate(); return false;"><img src="{{$sRootPath}}gui/img/common/default/magnifier.png"/>Search</button>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="dashlet" style="margin-top:15px">
						<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 11px Tahoma;">
							<tbody>
								<tr>
									<td width="30%" valign="top"><h1 style="white-space:nowrap">List of standard prescription</h1></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div id="templates-list"></div>
				</td>
			</tr>
	</table>
</div>

<!-- added by VAN 11-12-2012-->
<div class="segPanel" id="printgrpDialog" style="display:none">
    <span>Print as a group?</span><br><br>
    <div align="center" style="overflow:hidden">
        <button onclick="printAsGrp('1');">
        <img src="../../gui/img/common/default/accept.png">
        Yes
        </button> &nbsp;
        <button onclick="printAsGrp('0');">
        <img src="../../gui/img/common/default/stop.png">
        No
        </button>
    </div>    
</div>


<input type="hidden" id="drug_code"/>
<input type="hidden" id="drug_name"/>
<input type="hidden" id="drug_generic"/>
<input type="hidden" id="drug_avail"/>
<input type="hidden" id="prescription_id"/>
{{$sFormEnd}}
{{$sTailScripts}}
