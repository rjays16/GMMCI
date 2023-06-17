{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}
	<table>
		<tr>
			<td>{{$sAddNewRequest}}</td>
		</tr>
	</table>
	<table border="0" cellspacing="2" cellpadding="2" width="95%" align="center">
		<tbody>
			<tr>
				<td class="segPanelHeader" width="*">
					Request Details
				</td>
				<td class="segPanelHeader" width="150">
					<!--Reference No.-->
					Batch No.
				</td>
				<td class="segPanelHeader" width="220">
					Request Date
				</td>
			</tr>
			<tr>
				<td rowspan="3" class="segPanel" align="center" valign="top">
					
					<table width="95%" border="0" cellpadding="1" cellspacing="0" style="font-size:11px" >
						<tr>
							<td><strong>Transaction type</strong>
							&nbsp;&nbsp;&nbsp;
								{{$sIsCash}}
								{{$sIsCharge}}
								{{$sChargeTyp}}
							</td>
						</tr>
					</table>
					<table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
						<tr>
							<td align="right" valign="top"><strong>Name</strong></td>
							<td width="1" valign="middle">
								{{$sOrderEncID}}
								{{$sOrderName}}
							</td>
							<td width="1" valign="middle">
								{{$sSelectEnc}}
							</td>
							<td valign="middle">
								{{$sClearEnc}}
							</td>
						</tr>
						<tr>
							<td align="right" valign="top"><strong>Address</strong></td>
							<td colspan="3">{{$sOrderAddress}}</td>
						</tr>
						<tr>
							<td align="right" valign="top"><strong>Age</strong></td>
							<td colspan="3">
								{{$sAge}}
							    <strong>Sex</strong>&nbsp;{{$sSex}}
							    <strong>Civil Status</strong>&nbsp;{{$sCivilStatus}}
							</td>
						</tr>
						<tr>
							<td valign="top" align="right"><strong>Requesting Physician</strong></td>
							<td colspan="3">{{$sDoctor}}</td>	
						</tr>
						<tr>
							<td align="right" valign="top"><strong>Blood Type</strong></td>
							<td colspan="3">
								{{$sBloodType}}
							    <strong>Source of Blood</strong>&nbsp;{{$sBloodSource}}
							</td>
						</tr>
						<tr>
							<td align="right" valign="top"><strong>Component</strong></td>
							<td colspan="3">
								{{$sBloodComponent}}
							    &nbsp;<strong>Serial No.</strong>&nbsp;{{$sSerialNo}}
							</td>
						</tr>
						<tr>
							<td align="right" valign="top"><strong>Extraction Date</strong></td>
							<td colspan="3">
								{{$sDateExtract}}
							    &nbsp;<strong>Expiry Date</strong>&nbsp;{{$sDateExpiry}}
							</td>
						</tr>
						<tr>
							<td align="right" valign="top"><strong>Rh Type</strong></td>
							<td colspan="3">
								{{$sRhType}}
							    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>HbsAG</strong>&nbsp;{{$sHbsAG}}
							</td>
						</tr>
						<tr>
							<td align="right" valign="top"><strong>HCV</strong></td>
							<td colspan="3">
								{{$sHCV}}
							    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>HIV</strong>&nbsp;{{$sHIV}}
							</td>
						</tr>
						<tr>
							<td align="right" valign="top"><strong>VDRL</strong></td>
							<td colspan="3">
								{{$sVDRL}}
							    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>VCV</strong>&nbsp;{{$sVCV}}
							</td>
						</tr>
						<tr>
							<td align="right" valign="top"><strong>BSMP</strong></td>
							<td colspan="3">
								{{$sBSMP}}
							  <!--  <strong>VCV</strong>&nbsp;{{$sVCV}}-->
							</td>
						</tr>
						<!-- added by VAN 06-14-08 -->
						<tr>
							<td valign="top" colspan="4">
								<div style="">&nbsp;<strong>Patient Type&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </strong><span id="patient_enctype" style="font:bold 10px Arial; color:#0000FF;">{{$sPatientType}}</span></div>
							</td>
						</tr>
						<tr>
							<td valign="top" colspan="4">
								<div style="">&nbsp;<strong>Location/Clinic&nbsp;: </strong><span id="patient_location" style="font:bold 10px Arial; color:#0000FF;">{{$sPatientLoc}}</span></div>
							</td>
						</tr>
						<tr>
							<td valign="top" colspan="4">
								<div style="">&nbsp;<strong>Medico Legal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </strong><span id="patient_medico_legal" style="font:bold 10px Arial; color:#0000FF;">{{$sPatientMedicoLegal}}</span></div>
							</td>
						</tr>
						
						<tr>
							<td valign="top" colspan="4">
								<table>
									<tr>
										<td valign="top" align="right"><strong>Repeat Request</strong></td>
										<td valign="top" colspan="3">{{$sRepeat}}</td>
									</tr>
									<tr id="repeatinfo01">
										<td valign="top" align="right"><strong>Previous Batch No.</strong></td>
										<td valign="top" colspan="3">{{$sParentBatchNr}}</td>
									</tr>	
									<tr id="repeatinfo02">
										<td valign="top" align="right"><strong>Remarks</strong></td>
										<td valign="top" colspan="3">{{$sRemarks}}</td>
									</tr>	
									<tr id="repeatinfo03">
										<td valign="top" align="right"><strong>Approved By</strong></td>
										<td valign="top" colspan="3">{{$sHead}}</td>
									</tr>	
									<tr id="repeatinfo04">
										<td valign="top" align="right"><strong>User ID</strong></td>
										<td valign="top" colspan="3">{{$sHeadID}}</td>
									</tr>
									<tr id="repeatinfo05">
										<td valign="top" align="right"><strong>Password</strong></td>
										<td valign="top" colspan="3">{{$sHeadPassword}}</td>
									</tr>
								</table>
							</td>	
						</tr>
						
					</table>
				</td>
				<td class="segPanel" align="center">
					{{$sRefNo}}
					{{$sResetRefNo}}
				</td>
				<td class="segPanel" align="center" valign="middle">
					{{$sOrderDate}}
					{{$sCalendarIcon}}
					<strong style="font-size:10px">mm/dd/yyyy</strong>
				</td>
			</tr>
			<tr>
				<td class="segPanelHeader">Discounts</td>
				<td class="segPanelHeader">Request Options</td>
			</tr>
			
			<tr>
				
				<td class="segPanel" align="center" valign="top">
						<table>
							<tr>
								{{if $ssView}}
								{{else}}
								<td valign="middle">
									{{$sAdjustedAmount}}
									<div style=""><strong>Classification: </strong><span id="sw-class" style="font:bold 14px Arial; color:#0000FF;">{{$sClassification}}</span></div>
									<div style="margin-top:5px; vertical-align:middle; ">{{$sDiscountShow}}</div>
								</td>
								<td>{{$sDiscountInfo}}</td>
								{{/if}}
							</tr>
							
						</table>
						{{$sBtnDiscounts}}
					</td>
				<!-- -->
				<td class="segPanel" align="center" valign="top">
					<div style="padding-left:5px">
						<strong>Priority</strong>
						{{$sNormalPriority}}
						{{$sUrgentPriority}}
					</div>
					<div style="padding-left:5px; margin-bottom:4px; vertical-align:middle">
						<strong style="float:left; margin-top:10px">Comments </strong>
						{{$sComments}}
					</div>
				</td>
			</tr>
		</tbody>
	</table>

<br>
	<div align="left" style="width:95%">
		<table width="100%">
			<tr>
				<!--
				<td width="50%" align="left">
					{{$sBtnAddItem}}
					{{$sBtnEmptyList}}
					{{$sBtnPDF}}
				</td>
				-->
				<td>&nbsp;</td>
				<td align="right">
					{{$sContinueButton}}
					{{$sBreakButton}}
				</td>
			</tr>
		</table>
		<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr id="order-list-header">
					<th width="4%" nowrap></th>
					<th width="0.5%"></th>
					<th width="15%" nowrap align="left">&nbsp;&nbsp;Code</th>
					<th width="*" nowrap align="left">&nbsp;&nbsp;Service Description</th>
					<th width="15%" align="center">Original Price</th>
					<th width="17%" align="center">Net Price</th>
				</tr>
			</thead>
			<tbody>
{{$sOrderItems}}
			
			<tbody id="socialServiceNotes" style="display:none">
				<tr>
					<td colspan="6">{{$sSocialServiceNotes}}</td>
				</tr>
			</tbody>
		</table>
		
		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
			<tr>
			<tr>
					<td width="*" align="right" style="background-color:#ffffff; padding:4px" height=""><strong>Sub-Total</strong>
					<td id="show-sub-total" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold">
			</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Discount</strong>
					<td id="show-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold">
				</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Net Total</strong>
					<td id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold">
				</tr>

			
		</table>
	</div>


{{$sHiddenInputs}}
{{$jsCalendarSetup}}
{{$sIntialRequestList}}
<br/>
<img src="" vspace="2" width="1" height="1"><br/>
{{$sDiscountControls}}
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>

<div align="center">
		{{$sClaimStub}}
</div>

<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}} 	
<hr/>
<!--
<input type="button" name="btnRefreshDiscount" id="btnRefreshDiscount" onclick="refreshDiscount()" value="Refresh Discount">
<input type="button" name="btnRefreshTotal" id="btnRefreshTotal" onclick="refreshTotal()" value="Refresh Totals">
-->
{{$sRefreshDiscountButton}}
{{$sRefreshTotalButton}}