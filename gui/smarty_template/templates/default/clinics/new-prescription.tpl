{{*created by cha Feb 4, 2010*}}
{{$sFormStart}}
<div align="center" style="width:90%; min-width: 600px; margin-top:10px">
	<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%;margin:4px">
		<tr>
			<td class="segPanelHeader">Patient details</td>
		</tr>
		<tr>
			<td class="segPanel" align="left" valign="top">
				<table width="100%" border="0" cellpadding="0" cellspacing="2" style="" >
					<tr>
						<td width="50%">

							<table id="" width="100%" border="0" cellpadding="0" cellspacing="2">
								<tbody>
									<tr>
										<td width="" align="right" valign="middle"><label>PID: </label></td>
										<td valign="middle">{{$sPatientID}}</td>
									</tr>
									<tr>
										<td align="right" valign="middle"><label>Patient: </label></td>
										<td valign="middle" nowrap="nowrap">
											{{$sPatientEncNr}}{{$sPatientName}} {{$sSelectEnc}}{{$sClearEnc}}
										</td>
									</tr>
									<tr>
										<td align="right" valign="middle"><label>Address : </label></td>
										<td valign="middle">{{$sPatientAddress}}</td>
									</tr>
									<tr>
										<td align="right" valign="middle"><label>Age : </label></td>
										<td valign="middle">{{$sPatientAge}}</td>
									</tr>
									<tr>
										<td align="right">Sex:</label></td>
										<td>
											<input type="radio" id="psexm" name="psex" value="M"/><label>Male</label>
											<input type="radio" id="psexf" name="psex" value="F"/><label>Female</label>
										</td>
									</tr>
								</tbody>
							</table>
						</td>

						<td width="*">
							<table id="" width="100%" border="0" cellpadding="0" cellspacing="2">
								<tbody>
									<tr>
										<td align="right" valign="middle"><label>Request Date : </label></td>
										<td>
											{{$sRequestDate}}
											{{$sCalendarIcon}}
											{{$jsCalendarSetup}}
										</td>
									</tr>
									<tr>
										<td align="right" valign="middle"><label>Chief Complaint : </label></td>
										<td valign="middle">
											{{$sPatientComplaint}}
										</td>
									</tr>
									<tr>
										<td align="right" valign="middle"><label>Diagnosis : </label></td>
										<td valign="middle">
											{{$sPatientDiagnosis}}
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
	<div style="width:100%; text-align:left;">
		<div class="dashlet" style="margin-top:20px; padding: 8px 4px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tbody>
					<tr>
						<td width="1%" align="left" nowrap="nowrap">
							<button class="segButton" onclick="open_add_drug_prescription(); return false"><img src="../../../gui/img/common/default/pill_add.png"/>Add drug</button>
							<button class="segButton" onclick="open_standard_drug_prescription(); return false"><img src="../../../gui/img/common/default/note_edit.png"/>Standard</button>
							<button class="segButton" onclick="open_prescription_history(); return false"><img src="../../../gui/img/common/default/calendar.png"/>Patient history</button>
						</td>
						<td width="*" align="right" nowrap="nowrap">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="segContentPane" style="width:775px;padding:4px;height:100px;overflow-x:hidden;overflow-y:auto;">
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
				<th width="15%">Options</th>
			</tr>
		</thead>
		<tbody id="prescriptionlist-body">
			<tr style="height:32px">
				<td>Paracetamol 100mg tablet</td>
				<td align="center"><span style="color:#080">Available</span></td>
				<td align="center">
					<input class="segInput" type="text" value="10.00" onfocus="this.select()" style="width:100%;text-align: right" />
				</td>
				<td align="center" style="padding: 3px">
					<textarea class="segInput" type="text" onfocus="this.select()" style="width:100%;text-align: left;" rows="1">Once a day</textarea>
				</td>
				<td align="center" nowrap="nowrap">
					<input class="segInput" type="text" value="1" onfocus="this.select()" style="width:30%;text-align:right" />
					<select class="segInput" style="width:60%">
						<option value="D">day/s</option>
						<option value="W">week/s</option>
						<option value="M">month/s</option>
					</select>
				</td>
				<td>
				</td>
		</tbody>
	</table>
	</div>

	<div style="width:775px; margin-top:10px" align="center">
	<table width="100%" border="0" cellpadding="0" cellspacing="2" style="font:normal 12px Arial; padding:4px" >
		<tr>
			<td align="left" valign="top"><label><label>Special Instructions : </label></label></td>
			<td valign="middle"></td>
		</tr>
		<tr>
			<td align="left" valign="middle">
			<textarea class="segInput" id="p_instruction" name="p_instruction" cols="140" rows="3" style="font:bold 12px Arial"></textarea>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">
				{{$sSavePrescription}}
				<label><label>Save prescription </label></label>
				 {{$sSaveOptions}}
			</td>
		</tr>
		<tr>
		<td>
			<table style="font:normal 12px Arial; padding:4px">
				<tr>
					<td align="left" valign="bottom">
						<label/><label>Tags : </label>
					</td>
					<td align="right">{{$sPrescriptionTags}}</td>
				</tr>
			</table>
		</td>
		</tr>
		<tr>
			<td align="left" valign="bottom"><label style="font:normal 11px Arial; padding:4px">HINT: Type in the keywords you wish to tag with this prescrption, separated by commas. (e.g. headache, pain)</label></td>
		</tr>
	</table>
	</div>
	<div style="width:68%; text-align:right; padding:2px 4px">
			<img src="../../../images/btn_save.gif" style="cursor:pointer" align="middle" id="save_prescription">
			<img src="../../../images/btn_cancelorder.gif" style="cursor:pointer" align="middle" id="cancel_prescription">
	</div>

<span style="font:bold 15px Arial">{{$sDebug}}</span>

{{$sFormEnd}}
{{$sTailScripts}}
