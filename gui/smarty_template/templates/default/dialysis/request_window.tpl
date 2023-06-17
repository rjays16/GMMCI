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
	<table width="100%" cellpadding="0" cellspacing="0" style="font:12px Tahoma bold;">
		<tr>
			<td align="left" style="font:12px Arial bold;">
					<strong>TOTAL Charge: </strong>
					<span id="overall-total-charge" style="font:14px Arial bold; color:#ff0000">0.00</span>
			</td>
			<td align="right">
				<button class="segButton" onclick="viewRequestPrintout();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/printer.png" border="0"/>Request printout</button>
			</td>
		</tr>
		<tr>
			<td align="left" style="font:12px Arial bold;">
					<strong>TOTAL Cash: </strong>
					<span id="overall-total-cash" style="font:14px Arial bold; color:#ff0000">0.00</span>
			</td>
			<td align="right">
				<span id="show_seldate" class="segInput" style="color: rgb(0, 0, 192); padding: 0px 2px; width: 200px; height: 24px;">{{$dateToday}}</span>
				<input id="seldate" type="hidden" name="seldate" value="{{$dateTodayValue}}"/>
				<button class="segButton" id="tg_seldate" name="tg_seldate" onclick="return false;" style="cursor:pointer"><img src="../../gui/img/common/default/calendar.png" border="0"/>Date of Request</button>
				<img src="../../images/cashier_refresh.gif" border="0" onclick="requestByDate();" align="absmiddle" class="segSimulatedLink" title="Refresh!"/>
				<script type="text/javascript">
						Calendar.setup ({
								displayArea: "show_seldate",
								inputField : "seldate",
								ifFormat : "%Y-%m-%d",
								daFormat : "	%B %e, %Y",
								showsTime : false,
								button : "tg_seldate",
								singleClick : true,
								step : 1
						});
				</script>
			</td>
		</tr>
	</table>
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
								<button class="segButton" onclick="openLabRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/flask.png" border="0"/>New request</button>
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
							<button class="segButton" onclick="openBloodRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/heart_add.png" border="0"/>New request</button>
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
							<button class="segButton" onclick="openSpLabRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/folder_heart.png" border="0"/>New request</button>
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
							<button class="segButton" onclick="openRadioRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/film.png" border="0"/>New request</button>
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
							<button class="segButton" onclick="openPharmaRequest('IP');return false;" style="cursor:pointer"><img src="../../gui/img/common/default/pill.png" border="0"/>New request</button>
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
							<button class="segButton" onclick="openPharmaRequest('MG');return false;" style="cursor:pointer"><img src="../../gui/img/common/default/pill_add.png" border="0"/>New request</button>
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
							<button class="segButton" onclick="openMiscellaneousRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/order.gif" border="0"/>New request</button>
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
{{$encounter_nr}}
</div>