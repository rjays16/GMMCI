{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}
	<table border="0" cellspacing="2" cellpadding="2" width="95%" align="center">
		<tbody>
			<tr>
				<td class="segPanelHeader" width="*">
					Personal Information
				</td>
			</tr>
			<tr>
				<td rowspan="3" class="segPanel" align="center" valign="top">
				  <table width="100%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
				  	<tr>
					<td width="60%">	
					<table width="100%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
						<tr>
							<td valign="top" width="20%"><strong>Name</strong></td>
							<td width="1" valign="middle">
								{{$sOrderName}}
							</td>
						</tr>
						<tr>
							<td valign="top"><strong>Hospital No.</strong></td>
							<td width="1" valign="middle">
								{{$sOrderPID}}
							</td>
						</tr>
						<tr>
							<td valign="top"><strong>Member ID.</strong></td>
							<td width="1" valign="middle">
								{{$sOrderMemberID}}
							</td>
						</tr>
						<tr>
							<td valign="top"><strong>Address</strong></td>
							<td>{{$sOrderAddress}}</td>
						</tr>
					</table>
					</td>
					<td width="40%">
						<table width="100%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
						<tr>
							<td valign="top" width="50%" align="left"><strong>Age</strong></td>
							<td width="1" valign="middle">
								{{$sAge}}
							</td>
						</tr>
						<tr>
							<td valign="top"><strong>Sex</strong></td>
							<td width="1" valign="middle">
								{{$sSex}}
							</td>
						</tr>
						<tr>
							<td valign="top"><strong>Civil Status</strong></td>
							<td width="1" valign="middle">
								{{$sCivilStatus}}
							</td>
						</tr>
						<tr>
							<td valign="top"><strong>Membership Date</strong></td>
							<td>{{$sMemberDate}}</td>
						</tr>
						<tr>
							<td valign="top"><strong>Covered Date</strong></td>
							<td>{{$sCoveredDate}}</td>
						</tr>
					</table>
					</td>
					</tr>
					</table>
				</td>
				
			</tr>
			
		</tbody>
	</table>

<br>
	<div align="left" style="width:95%">
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
		<table id="dep-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr id="dep-list-header">
					<th width="4%" nowrap align="left">Cnt : <span id="counter"></span></th>
					<th width="0.5%"></th>
					<th width="10%" nowrap align="left">&nbsp;&nbsp;HRN</th>
					<th width="*" nowrap align="left">&nbsp;&nbsp;Dependents</th>
					<th width="15%" nowrap align="left">&nbsp;&nbsp;Relationship</th>
					<th width="10%" nowrap align="left">&nbsp;&nbsp;Bdate</th>
					<th width="10%" nowrap align="left">&nbsp;&nbsp;Age</th>
					<th width="5%" nowrap align="left">&nbsp;&nbsp;Sex</th>
					<th width="10%" nowrap align="left">&nbsp;&nbsp;Civil Status</th>
					<!--<th width="5%" nowrap align="left">&nbsp;&nbsp;Delete</th>-->
				</tr>
			</thead>
			<tbody>
{{$sOrderItems}}
			
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



<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}} 	
<hr/>
<!--
<input type="button" name="btnRefreshDiscount" id="btnRefreshDiscount" onclick="refreshDiscount()" value="Refresh Discount">
<input type="button" name="btnRefreshTotal" id="btnRefreshTotal" onclick="refreshTotal()" value="Refresh Totals">
-->
