{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}

	<table border="0" cellspacing="2" cellpadding="2" width="90%" align="center">
		<tbody>
			<tr>
				<td class="segPanelHeader" width="*">
					Request Details
				</td>
				<td class="segPanelHeader" width="170">
					Reference No.
				</td>
				<td class="segPanelHeader" width="220">
					Request Date
				</td>
			</tr>
			<tr>
				<td rowspan="3" class="segPanel" align="center" valign="top">
					<table width="95%" border="0" cellpadding="1" cellspacing="0" style="font-size:11px" >
						<tr>
							<td><strong>Transaction type</strong></td>
							<td align="left">
								{{$sIsCash}}
								{{$sIsCharge}}
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
							<td valign="top"><strong>Address</strong></td>
							<td colspan="3">{{$sOrderAddress}}</td>
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
				<td class="segPanelHeader">Request options</td>
			</tr>
			<tr>
				<td class="segPanel" align="center" valign="top">
					<table>
						<tr>
							<td><input id="show-discount" type="text" style="color:#006600; font-family:Arial; font-size:15px; font-weight:bold; text-align:right" readonly="1" size="5" value="0.00" disabled></td>
							<td>{{$sDiscountInfo}}</td>
						</tr>
					</table>
					{{$sBtnDiscounts}}
				</td>
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
	<table border="0" cellspacing="2" cellpadding="2" width="90%" align="center">
		<tbody>
			<tr>
				<td class="segPanelHeader" width="20%" align="left" colspan="5"> {{$segDepartment}} </td>
			</tr>
			<tr>
				<td class="segPanelHeader" width="20%" align="center"> Surgeon(s) </td>
				<td class="segPanelHeader" width="20%" align="center"> Assistant(s) </td>
				<td class="segPanelHeader" width="20%" align="center"> Anesthetist(s) </td>
				<td class="segPanelHeader" width="20%" align="center"> Scrub Nurse(s) </td>
				<td class="segPanelHeader" width="20%" align="center"> Rotating Nurse(s) </td>
			</tr>
			<tr>
				<td class="segPanel" align="left" valign="top"> 
					{{$segSurgeons}} 
				</td>
				<td class="segPanel" align="left" valign="top">
					{{$segAssistants}} 
				</td>
				<td class="segPanel" align="left" valign="top">
					{{$segAnDoctors}}
				</td>
				<td class="segPanel" align="left" valign="top">
					{{$segScrubNurses}}
				</td>
				<td class="segPanel" align="left" valign="top"> 
					{{$segRotatingNurses}} 
				</td>
			</tr>
		</tbody>
	</table>
<br>
	<table border="0" cellspacing="2" cellpadding="2" width="90%" align="center">
		<tbody>
			<tr>
				<td class="segPanelHeader" width="20%" align="left"> Therapy/Operation </td>
			</tr>
			<tr>
				<td class="segPanel" width="20%" align="justify" style="color:#000000"> 
					{{$segTherapy}}
				</td>
			</tr>
		</tbody>
	</table>
<br>
	<div align="left" style="width:90%">
		<table width="100%">
			<tr>
				<td width="50%" align="left">
					{{$sBtnAddItem}}
					{{$sBtnEmptyList}}
					{{$sBtnPDF}}
				</td>
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
					<th width="1%"></th>
					<th width="10%" nowrap align="center">&nbsp;&nbsp;Code</th>
					<th width="*" nowrap align="center">&nbsp;&nbsp;Description</th>
					<th width="10%" align="left">RVU</th>
					<th width="10%" align="left">Multiplier</th>
					<th width="10%" align="left">Charge</th>
				</tr>
			</thead>
			<tbody>
{{$sOrderItems}}
			</tbody>
		</table>
<!--
		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
			<tr>
			<tr>
					<td width="*" align="right" style="background-color:#ffffff; padding:4px" height=""><strong>Sub-Total</strong></th>
					<td id="show-sub-total" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold"></th>
			</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Discount</strong></th>
					<td id="show-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold"></th>
				</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Net Total</strong></th>
					<td id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold"></th>
				</tr>
			</tr>
		</table>
-->
	</div>


{{$sHiddenInputs}}
{{$jsCalendarSetup}}
{{$sIntialRequestList}}
<br/>
<img src="" vspace="2" width="1" height="1"><br/>
{{$sDiscountControls}}
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>


</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}} 	
<hr/>
<!--
<input type="button" onclick="refreshDiscount()" value="Refresh Discount" />
<input type="button" onclick="refreshTotal()" value="Refresh Totals" />
-->