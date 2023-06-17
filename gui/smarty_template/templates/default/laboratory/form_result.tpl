{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}
	<div>
		<table>
			<tr>
				<td>{{$sRepeatRequest}}</td>
				<td>{{$sViewPDF}}</td>
				<td>{{$sViewResultPDF}}</td>
			</tr>	
		</table>
	</div>

	<table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">
		<tbody>
			<tr>
				<td class="segPanelHeader" width="50%">
					Patient Demographic Information
				</td>
				
				<td class="segPanelHeader" width="50%">
					Patient Request Details
				</td>
			</tr>
			<tr>
				<td rowspan="3" class="segPanel" align="center" valign="top">
					<table width="100%" border="0" cellpadding="1" cellspacing="0" style="font-size:11px">
						<tr>
							<td width="30%"><strong>Patient ID</strong></td>
							<td width=".5"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sOrderEncID}}</td>
						</tr>
						<tr>
							<td width="30%"><strong>Name</strong></td>
							<td width=".5"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sOrderName}}</td>
						</tr>
						<tr>
							<td width="30%"><strong>Patient Type</strong></td>
							<td width="1"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sPatientType}}</td>
						</tr>
						<tr>
							<td width="30%"><strong>Birth Date</strong></td>
							<td width="1"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sBirthDate}}</td>
						</tr>
						<tr>
							<td width="30%"><strong>Sex</strong></td>
							<td width="1"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sPatientSex}}</td>
						</tr>
						<tr>
							<td width="30%"><strong>Address</strong></td>
							<td width="1"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sOrderAddress}}</td>
						</tr>
						<tr>
							<td width="30%"><strong>Classification</strong></td>
							<td width="1"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sClassification}}</td>
						</tr>
					</table>
				</td>
				<td rowspan="3" class="segPanel" align="center" valign="top">
					<table width="95%" border="0" cellpadding="1" cellspacing="0" style="font-size:11px">
						<tr>
							<td width="40%"><strong>Order No.</strong></td>
							<td width=".5"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sRefNo}}</td>
						</tr>
						<tr>
							<td width="40%"><strong>Order Date</strong></td>
							<td width=".5"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sOrderDate}}</td>
						</tr>
						<tr>
							<td width="40%"><strong>Location</strong></td>
							<td width="1"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sLocation}}</td>
						</tr>
						<tr>
							<td width="40%"><strong>Doctor</strong></td>
							<td width="1"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sDoctor}}</td>
						</tr>
						<tr>
							<td width="40%"><strong>Priority</strong></td>
							<td width="1"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sPriority}}</td>
						</tr>
						<tr>
							<td width="40%"><strong>Clinical Info</strong></td>
							<td width="1"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sClinicalInfo}}</td>
						</tr>
						<tr>
							<td width="40%"><strong>Case/Visition No.</strong></td>
							<td width="1"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sVisit}}</td>
						</tr>
						<tr>
							<td width="40%"><strong>Lab No.</strong></td>
							<td width="1"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sLabNo}}</td>
						</tr>
						<tr>
							<td width="40%"><strong>Test</strong></td>
							<td width="1"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sTestName}}</td>
						</tr>
						<tr>
							<td width="40%"><strong>Test Type</strong></td>
							<td width="1"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sTestType}}</td>
						</tr>
						<tr>
							<td width="40%"><strong>Test Group</strong></td>
							<td width="1"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sTestGroup}}</td>
						</tr>
						<tr>
							<td width="40%"><strong>Ctl Seq. No.</strong></td>
							<td width="1"><strong>:&nbsp;</strong></td>
							<td width="*">{{$sControlNo}}</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	
	<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll;overflow-x:scroll; height:290px; width:98%; background-color:#e5e5e5">
		<table id="ResultList" class="segList" width="200%" border="0" cellpadding="0" cellspacing="0" style="overflow:auto">
			<thead>
				<tr>
					<th width="8%" align="center">Test Code</th>
					<th width="15%" align="left">Test Name</th>
					<th width="1%" align="center">Result</th>
					<th width="1%" align="center">Unit</th>
					<th width="11%" align="center">Normal Range</th>
					<th width="1%" align="center">Flag</th>
					<th width="1%" align="center">Status</th>
					<th width="10%" align="center">Reported On</th>
					<th width="7%" align="center">MLT Responsible</th>
					<th width="7%" align="center">Performed Lab</th>
					<th width="7%" align="center">Test Comments</th>
					<th width="7%" align="center">Parent Item</th>
					<th width="7%" align="center">Line No.</th>
				</tr>
			</thead>
			<tbody id="ResultList-body">
				<!--<tr><td colspan="13">No laboratory results available at this time...</td></tr>-->
				{{$sResultItems}}
			</tbody>
		</table>
	</div>
	
	<br />
{{$sHiddenInputs}} 

<br/>
<img src="" vspace="2" width="1" height="1"><br/>

<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>

<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}} 	
<hr/>
