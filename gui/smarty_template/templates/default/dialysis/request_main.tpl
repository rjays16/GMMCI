<style type="text/css">
	.tabFrame {
		/*padding:5px;*/
		min-height:140px;
	}
</style>
<script language="javascript" type="text/javascript">
	function tabClick(obj) {
		if (obj.className=='segActiveTab') return false;
		var dList = obj.parentNode;
		var tab;
		if (dList) {
			var listItems = dList.getElementsByTagName("LI");
			if (obj) {
				for (var i=0;i<listItems.length;i++) {
					if (obj!=listItems[i]) {
						listItems[i].className = "";
						tab = listItems[i].getAttribute('segTab');
						if ($(tab))
							$(tab).style.display = "none";
					}
				}
				tab = obj.getAttribute('segTab');
				if ($(tab))	$(tab).style.display = "block";
				obj.className = "segActiveTab";
			}
		}
	}

	function toggleTBody(list) {
		var dTable = $(list);
		if (dTable) {
			var dBody = dTable.getElementsByTagName("TBODY")[0];
			if (dBody) dBody.style.display = (dBody.style.display=="none") ? "" : "none";
		}
	}

	function enableInputChildren(id, enable) {
		var el=$(id);
		if (el) {
			var children = el.getElementsByTagName("INPUT");
			if (children) {
				for (i=0;i<children.length;i++) {
					children[i].disabled = !enable;
				}
				return true;
			}
		}
		return false;
	}
</script>

{{$sFormStart}}
<div style="width:750px; margin-top:10px">
<table border="0" cellspacing="1" cellpadding="0" width="100%" align="center" style="">
			<tbody>
				<tr height="5"><td class="segPanelHeader" colspan="4">Patient Information</td></tr>
				<tr>
					<td width="53%" valign="top" class="segPanel">
						<table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
							<tr valign="top">
								<td align="right" nowrap="nowrap"><label>PID</label></td>
								<td align="left" valign="middle">{{$sPatientID}}</td>
								<td rowspan="5" class="photo_id" align="center" id="photo_row" style="background-color:transparent">
									<img width="180px" height="150px" src="{{$img_source}}" name="headpic" id="headpic" border="0">
									<input type="hidden" id="photo_src" name="photo_src" value=""/>
								</td>
							</tr>
							<tr valign="top">
								<td align="right" nowrap="nowrap"><label>Fullname</label></td>
								<td align="left" nowrap="nowrap" valign="middle">
									{{$sPatientEncNr}}
									{{$sPatientName}}
									{{$sSelectEnc}}
									{{$sClearEnc}}
								</td>
							</tr>
							<tr valign="top">
								<td align="right" nowrap="nowrap"><label>Address</label></td>
								<td align="left" nowrap="nowrap" valign="middle">{{$sAddress}}</td>
							</tr>
							<tr valign="top">
								<td align="right" nowrap="nowrap"><label>Age</label></td>
								<td align="left" nowrap="nowrap" valign="middle">
									{{$sPatientAge}}
									<label>Birthday</label>
									{{$sPatientBirthday}}
								</td>
							</tr>
							<tr valign="top">
								<td align="right" nowrap="nowrap"><label>Gender</label></td>
								<td align="left" nowrap="nowrap" valign="middle">
									{{$sPatientGender}}
									<label>Civil Status</label>
									{{$sPatientStatus}}
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="53%" valign="top" class="segPanel">
						<table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
							<tr valign="top">
								<td nowrap="nowrap" align="right"><label>Admitting Diagnosis</label></td>
								<td valign="middle">{{$sPatientDiagnosis}}</td>
								<td nowrap="nowrap" align="right">
									<table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
										<tr valign="top">
											<td nowrap="nowrap" align="left">
													<label>Admission Date</label>&nbsp;&nbsp;
													{{$sPatientAdmissionDate}}
											</td>
										</tr>
										<tr valign="top">
											<td nowrap="nowrap" align="left">
													 <label>Discharged Date</label>
													 {{$sPatientDischargeDate}}
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr valign="top">
								<td nowrap="nowrap" align="right"><label>Location/Clinic</label></td>
								<td align="left" valign="middle">{{$sPatientLocation}}</td>
								<td nowrap="nowrap" align="right">
									<table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
										<tr valign="top">
											<td nowrap="nowrap" align="left">
													<label>Patient Type</label>&nbsp;&nbsp;&nbsp;&nbsp;
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sPatientType}}
											</td>
										</tr>
									</table>

								</td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
<br/>
</div>
<div style="width:800px;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top">
					<ul id="request-tabs" class="segTab" style="padding-left:10px;">
						<li id="tab_request" {{if $bTabRequest}}class="segActiveTab"{{/if}} onclick="tabClick(this)" segTab="new_transact">
							<h2 class="segTabText">New Transaction</h2>
						</li>
						<li id="tab_history" {{if $bTabHistory}}class="segActiveTab"{{/if}} onclick="tabClick(this)" segTab="history">
							<h2 class="segTabText">History</h2>
						</li>
					</ul>
					<div class="" style="width:100%;height:300px; border-top:2px solid #4e8ccf; margin-left:10px" align="center">
						<div id="new_transact" style="padding:2px;padding-top:3px;{{if !$bTabRequest}}display:none{{/if}}">
							<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" style="">
								<tbody>
									<tr height="5">
										<td class="segPanelHeader" colspan="2">Transaction Details</td>
									</tr>
									<tr>
										<td width="53%" valign="top"  class="segPanel">
											<table border="0" cellspacing="1" cellpadding="0" width="100%" style="font-family:Arial, Helvetica, sans-serif">
												<!--<tr valign="top">
													<td align="right" nowrap="nowrap"><label>Reference</label></td>
													<td align="left" valign="middle">{{$requestReferenceNo}}</td>
												</tr>-->
												<tr valign="top">
													<td align="right" nowrap="nowrap"><label>No. of Visits</label></td>
													<td align="left" nowrap="nowrap" valign="middle">{{$requestVisitNo}}</td>
												</tr>
												<tr valign="top">
													<td align="right" nowrap="nowrap"><label>Requesting Doctor</label></td>
													<td align="left" nowrap="nowrap" valign="middle">{{$requestDoctors}}</td>
												</tr>
												<tr valign="top">
													<td align="right" nowrap="nowrap"><label>Attending Nurse</label></td>
													<td align="left" nowrap="nowrap" valign="middle">{{$requestNurses}}</td>
												</tr>
												<tr valign="top">
													<td align="right" nowrap="nowrap"><label>Request Type</label></td>
													<td align="left" nowrap="nowrap" valign="middle">{{$requestDialysisType}}</td>
												</tr>
												<tr valign="top">
													<td align="right" nowrap="nowrap"><label>Remarks</label></td>
													<td align="left" nowrap="nowrap" valign="middle">{{$requestRemarks}}</td>
												</tr>
												<tr valign="top">
													<td align="right" nowrap="nowrap"><label>Encoded by</label></td>
													<td align="left" nowrap="nowrap" valign="middle">{{$requestEncoder}}</td>
												</tr>
												<!--<tr valign="top">
													<td align="right" nowrap="nowrap"><label>Status</label></td>
													<td align="left" nowrap="nowrap" valign="middle">{{$requestStatus}}</td>
												</tr>-->
											</table>
										</td>
										<td width="*" valign="top"  class="segPanel">
											<table border="0" cellspacing="2" cellpadding="0" width="100%" style="font-family:Arial, Helvetica, sans-serif">
												<tr valign="top">
													<td width="1%" nowrap="nowrap" align="right"><label>Transaction Date</label></td>
													<td width="*" align="left" valign="middle">{{$requestDate}}{{$sCalendarIcon}}{{$jsCalendarSetup}}</td>
												</tr>
												<tr>
													<td colspan="2"><strong>VITAL SIGNS</strong></td>
												</tr>
												<tr valign="top">
													<td align="right" nowrap="nowrap"><label>Blood Pressure</label></td>
													<td align="left" nowrap="nowrap" valign="middle">{{$bp_systole}}&nbsp;/&nbsp;{{$bp_diastole}}&nbsp;<span style="font: 11px Arial;">mm Hg</span></td>
												</tr>
												<tr valign="top">
													<td align="right" nowrap="nowrap"><label>Temperature</label></td>
													<td align="left" nowrap="nowrap" valign="middle">{{$temperature}}&nbsp;<span style="font: 11px Arial;">°C</span></td>
												</tr>
												<tr valign="top">
													<td align="right" nowrap="nowrap"><label>Weight</label></td>
													<td align="left" nowrap="nowrap" valign="middle">{{$weight}}&nbsp;<span style="font: 11px Arial;">kg</span></td>
												</tr>
												<tr valign="top">
													<td align="right" nowrap="nowrap"><label>Resp. Rate</label></td>
													<td align="left" nowrap="nowrap" valign="middle">{{$resp_rate}}&nbsp;<span style="font: 11px Arial;">br/m</span></td>
												</tr>
												<tr valign="top">
													<td align="right" nowrap="nowrap"><label>Pulse Rate</label></td>
													<td align="left" nowrap="nowrap" valign="middle">{{$pulse_rate}}&nbsp;<span style="font: 11px Arial;">b/m</span></td>
												</tr>
												<tr valign="bottom">
													<td align="right" nowrap="nowrap" colspan="4">
														{{$submitBtn}}<!--{{$cancelBtn}}-->
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div id="history" style="padding:2px;padding-top:3px;{{if !$bTabHistory}}display:none{{/if}}">
							<table border="0" cellspacing="1" cellpadding="0" width="100%" align="center" style="font-family:Arial, Helvetica, sans-serif">
								<tbody>
									<tr height="5">
										<td class="segPanelHeader" colspan="4">History Details</td>
									</tr>
									<tr>
										<td class="segPanel">
											<table style="font-family:Arial, Helvetica, sans-serif" width="100%" align="center">
												<tr>
													<td align="left">No. of Visits: &nbsp;&nbsp;{{$visit_number}}</td>
													<td align="right"><!--{{$requestBtn}}-->{{$historyBtn}}{{$continuousReportBtn}}</td>
												</tr>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
							<div id="history_list" style="margin-top:5px;"></div>
							</div>
				</td>
			</table>
</div>
{{$submitted}}
<!--{{$dialysis_type}} -->
{{$encounter_nr}}
{{$encounter_type}}
{{$pid}}
{{$sHiddenInputs}}
{{$jsCalendarSetup}}
{{$sFormEnd}}
{{$sTailScripts}}