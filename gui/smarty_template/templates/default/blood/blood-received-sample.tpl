{{$sFormStart}}
<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center">
	 
			<tr>
				<td class="segPanelHeader" width="*">
					Request Details
				</td>
			</tr>
			<tr>
				<td class="segPanel" align="center" valign="top">
					<table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
						<tr>
							<td align="left" width="20%"><strong>Reference No.</strong></td>
							<td valign="middle"></strong><span id="refno" style="font:bold 12px Arial; color:#0000FF;">{{$sRefno}}</span></td>
                            <td align="right" width="1"><strong>&nbsp;</strong></td>
                            <td align="left" width="15%"><strong>HRN</strong></td>
                            <td valign="middle"></strong><span id="hrn" style="font:bold 12px Arial; color:#0000FF;">{{$sHRN}}</span></td>
						</tr>
                        <tr>
                            <td align="left" width="20%"><strong>Patient Name</strong></td>
                            <td valign="middle"></strong><span id="pat_name" style="font:bold 12px Arial; color:#0000FF;">{{$sPatientName}}</span></td>
                            <td align="right" width="1"><strong>&nbsp;</strong></td>
                            <td align="left" width="15%"><strong>Blood Type</strong></td>
                            <td valign="middle"></strong><span id="hrn" style="font:bold 12px Arial; color:#0000FF;">{{$sBloodType}}</span></td>
                        </tr>
                        <tr>
                            <td align="left" width="20%"><strong>Sex</strong></td>
                            <td valign="middle"></strong><span id="pat_name" style="font:bold 12px Arial; color:#0000FF;">{{$sSex}}</span></td>
                            <td align="right" width="1"><strong>&nbsp;</strong></td>
                            <td align="left" width="15%"><strong>Age</strong></td>
                            <td valign="middle"></strong><span id="hrn" style="font:bold 12px Arial; color:#0000FF;">{{$sAge}}</span></td>
                        </tr>
						<tr>
							<td align="left" width="20%"><strong>Test Name</strong></td>
							<td valign="middle"></strong><span id="test_name" style="font:bold 12px Arial; color:#0000FF;">{{$sTestName}}</span></td>
							<td align="right" width="1"><strong>&nbsp;</strong></td>
							<td align="left" width="15%"><strong>Test Code</strong></td>
							<td valign="middle"></strong><span id="test_code" style="font:bold 12px Arial; color:#0000FF;">{{$sTestCode}}</span></td>
						</tr>
                        <tr>
                            <td align="left" width="15%"><strong>Date Encoded</strong></td>
                            <td valign="middle"></strong><span id="date_encoded" style="font:bold 12px Arial; color:#0000FF;">{{$sDateEncoded}}</span></td>                            
                            <td align="right" width="1"><strong>&nbsp;</strong></td>
                            <td align="left" width="20%"><strong>Quantity Requested</strong></td>
                            <td valign="middle"></strong><span id="qty" style="font:bold 12px Arial; color:#0000FF;">{{$sQuantity}}</span></td>
                        </tr>

					</table>
				</td>
			</tr>
	
</table>
  
<div align="left" style="width:100%">
		<table id="RequestList" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="2%" nowrap align="left">Cnt : <span id="counter">0</span></th>
					<th width="*" nowrap align="left">Unit No.</th>
                    <th width="10%" nowrap align="left">Serial No.</th>
					<th width="5%" align="center">Components</th>
                    <th width="13%" align="center">Date Received</th>
                    <th width="15%" align="center">Date Done</th>
                    <th width="10%" align="center">Result</th>
                    <th width="15%" align="center">Issuance Date</th>
                    <th width="15%" align="center">Returned</th>
                    <th width="15%" align="center">Reissue</th>
                    <th width="20%" align="center">Consumed</th>
					<!--<th width="10%" align="center">Submit</th>-->
					<!--<th width="10%" align="center">Repeat?</th>-->
				</tr>
			</thead>
			<tbody id="RequestList-body">
{{$sOrderItems}}
			</tbody>
		</table>

	</div>
{{$printDialog}}
{{$sHiddenInputs}}
{{$jsCalendarSetup}}
{{$jsPrintDialog}}
<br/>
{{$sSubmitButton}}{{$sCloseButton}}{{$sPrintButton}}
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}
<hr/>

