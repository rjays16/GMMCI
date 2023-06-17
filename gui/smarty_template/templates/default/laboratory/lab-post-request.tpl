{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
{{$sFormStart}}
<table border="0" cellspacing="2" cellpadding="2" width="95%" align="center">
	 <tbody>
			<tr>
				<td class="segPanelHeader" width="*">
					Request Details
				</td>
			</tr>
			<tr>
				<td class="segPanel" align="center" valign="top">
					<table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
						<tr>
							<td align="left" width="20%"><strong>HRN</strong></td>
							<td valign="middle" width="40%"></strong><span id="hrn" style="font:bold 12px Arial; color:#0000FF;">{{$sHRN}}</span></td>
                            <td align="right" width="1%"><strong>&nbsp;</strong></td>
                            <td align="left" width="15%"><strong>Reference No.</strong></td>
                            <td valign="middle" width="20%"></strong><span id="refno" style="font:bold 12px Arial; color:#0000FF;">{{$sRefno}}</span></td>
						</tr>
                        <tr>
                            <td align="left"><strong>Patient Name</strong></td>
                            <td valign="middle" colspan="4"></strong><span id="patient_name" style="font:bold 12px Arial; color:#0000FF;">{{$sPatientName}}</span></td>
                        </tr>
                        <tr>
                            <td align="left"><strong>Age</strong></td>
                            <td valign="middle"></strong><span id="hrn" style="font:bold 12px Arial; color:#0000FF;">{{$sAge}}</span></td>
                            <td align="right"><strong>&nbsp;</strong></td>
                            <td align="left"><strong>Gender</strong></td>
                            <td valign="middle"></strong><span id="refno" style="font:bold 12px Arial; color:#0000FF;">{{$sGender}}</span></td>
                        </tr>
						<tr>
							<td align="left"><strong>Test Name</strong></td>
							<td valign="middle"></strong><span id="test_name" style="font:bold 12px Arial; color:#0000FF;">{{$sTestName}}</span></td>
							<td align="right"><strong>&nbsp;</strong></td>
							<td align="left"><strong>Test Code</strong></td>
							<td valign="middle"></strong><span id="test_code" style="font:bold 12px Arial; color:#0000FF;">{{$sTestCode}}</span></td>
						</tr>

					</table>
				</td>
			</tr>
	 </tbody>
</table>

<div align="left" style="width:95%">
		<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr id="order-list-header">
					<th width="7%" nowrap align="left">Cnt : <span id="counter">0</span></th>
					<th width="16%" nowrap align="left">Serial</th>
					<th width="*%" align="center">Date Served</th>
					<th width="10%" align="center">Control</th>
                   <!--  <th width="5%" align="center">Result</th> -->
				</tr>
			</thead>
			<tbody>
{{$sOrderItems}}
			</tbody>
		</table>

	</div>

{{$sHiddenInputs}}
{{$jsCalendarSetup}}
<br/>

<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}
<hr/>