<div>
{{$form_start}}

<div style="width:90%; margin-top:10px" align="left">
	<table border="0" cellspacing="2" cellpadding="3" align="center" width="100%">
		<tbody>
			<tr>
				<td class="segPanelHeader" width="*" colspan="2">Patient Details</td>
			</tr>
			<tr>
				<td class="segPanel" align="left" valign="top">
					<table  width="100%" class="transaction_details_table" cellpadding="0" cellspacing="0" style="font:normal 12px Arial; padding:4px" >
						<tr>
							<td align="left" width="30%" nowrap="nowrap"><strong>PID : </strong>{{$sPatientID}}</td>
							<td nowrap="nowrap"><strong>Name : </strong>{{$patient_name}}</td>
							<td width="30%" nowrap="nowrap"><strong>Patient Type : </strong>{{$encounter_type}}</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<div align="right">
	<button class="segButton" id="viewRequestPrintoutBtn" name="viewRequestPrintoutBtn" onclick="viewRequestPrintout();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/printer.png" border="0"/>Request printout</button>
	</div>                       
</div>

<div id="tabs" style="width:90%;margin-top:5px">
	<ul>
		<li><a href="#tab-laboratory">Laboratory</a></li>
		<li><a href="#tab-bloodbank">Blood Bank</a></li>
		<li><a href="#tab-splab">Special Lab</a></li>
		<li><a href="#tab-radiology">Radiology</a></li>
		<li><a href="#tab-ip">Inpatient Pharmacy</a></li>
		<li><a href="#tab-mg">Murang Gamot</a></li>
		<li><a href="#tab-miscellaneous">Miscellanous</a></li>
	</ul>

	<div id="tab-laboratory">
			<div class="dashlet" style="margin-top:5px">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
					<tbody>
						<tr>
							<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
							<td align="right">
								<button class="segButton"  id="openLabRequestBtn" name="openLabRequestBtn"  onclick="openLabRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/flask.png" border="0"/>New request</button>
								<button class="segButton" onclick="openLabResults();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/page_white_acrobat.png" border="0"/>Results</button>
							</td>
						</tr>
						<tr>
							<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
							<td><span id="lab-total-charge">0.00</span></td>
						</tr>
						<tr>
							<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
							<td><span id="lab-total-cash">0.00</span></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="lab_requests" align="center">
			</div>
	</div>

	<div id="tab-bloodbank">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openBloodRequestBtn" name="openBloodRequestBtn" onclick="openBloodRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/heart_add.png" border="0"/>New request</button>
							<button class="segButton" onclick="openBloodResults();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/page_white_acrobat.png" border="0"/>Results</button>
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="blood-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="blood-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="blood_requests" align="center">
		</div>
	</div>

	<div id="tab-splab">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openSpLabRequestBtn" name="openSpLabRequestBtn" onclick="openSpLabRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/folder_heart.png" border="0"/>New request</button>
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="splab-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="splab-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="splab_requests" align="center">
		</div>
	</div>

	<div id="tab-radiology">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openRadioRequestBtn" name="openRadioRequestBtn" onclick="openRadioRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/film.png" border="0"/>New request</button>
							<button class="segButton" onclick="openRadioResults();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/page_white_acrobat.png" border="0"/>Results</button>
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="radio-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="radio-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="radio_requests" align="center">
		</div>
	</div>

	<div id="tab-ip">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openPharmaRequestBtnIP" name="openPharmaRequestBtnIP" onclick="openPharmaRequest('IP');return false;" style="cursor:pointer"><img src="../../gui/img/common/default/pill.png" border="0"/>New request</button>
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="ip-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="ip-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="ip_requests" align="center">
		</div>
	</div>

	<div id="tab-mg">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openPharmaRequestBtnMG" name="openPharmaRequestBtnMG" onclick="openPharmaRequest('MG');return false;" style="cursor:pointer"><img src="../../gui/img/common/default/pill_add.png" border="0"/>New request</button>
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="mg-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="mg-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="mg_requests" align="center">
		</div>
	</div>

	<div id="tab-miscellaneous">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openMiscellaneousRequestBtn" name="openMiscellaneousRequestBtn" onclick="openMiscellaneousRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/order.gif" border="0"/>New request</button>
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="misc-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="misc-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="misc_requests" align="center">
		</div>
	</div>

</div>

{{$form_end}}
{{$ptype}}
{{$request_source}}
{{$is_bill_final}}
{{$encounter_nr}}
</div>