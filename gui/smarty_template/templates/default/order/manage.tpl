{{* manage.tpl  Form template for manage transactions module (pharmacy & meddepot) Segworks Technologies, Inc *}}

<div align="center">
	<div id="referenceSearchTab" style="border:1px solid black;padding:6px;background-color:#CCCCCC;width:95%;position:relative;display:block" align="center">
	<div style="width:95%;height:20px;font-weight:bold" align="left">
		Search by reference no. | <u><a href="#" onclick="$('referenceSearchTab').style.display='none';$('personSearchTab').style.display='block'" onmouseover="window.status='Search by patient name'" onmouseout="window.status=''">Search by patient name</a></u>
	</div>
<table border="1" width="100%" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"	>
	<tr>
		<td width="50%" align="left" style="padding:5px">
			<table width="95%">
				<tr>
					<td width="60%">
						Search reference no.
						<input type="text" id="searchPerson" style="width:200px" onKeyUp="fetchRefList(this.value, 300)">
						<input type="button" id="clearSearch" style="width:50px" value="Clear">
					</td>
					<td width="40%">
						<strong>From</strong>
						<input type="text" id="dayStart" value="0" maxlength="4" size="4" onChange="if (isNaN(this.value)) this.value=0;">
						<strong>to</strong>
						<input type="text" id="dayEnd" value="9999" maxlength="4" size="4" onChange="if (isNaN(this.value)) this.value=9999;"> <strong>days ago</strong>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="padding:1px">
			<div style="width:100%;height:324px;overflow:hidden;border:1px solid black;">
			<div style="width:100%;height:340px;overflow:scroll;border:0px;">
			<table width="100%" border="0" cellpadding="0" cellspacing="1" id="refTable">
				<thead>
					<tr class="reg_list_titlebar" style="font-weight:bold;height:18px" id="refRowHeader">
						<th width="34%" nowrap>&nbsp;Reference no.</th>
						<th width="34%" nowrap>Purchase date</th>
						<th width="12%">&nbsp;Type</th>
						<th width="10%">&nbsp;Edit</th>
						<th width="10%">&nbsp;Rmv</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			</div></div>
		</td>
	</tr>
</table>
	
	</div>
	<div id="personSearchTab" style="border:1px solid black;padding:6px;background-color:#CCCCCC;width:95%;position:relative;display:none" align="center">

	<div style="width:95%;height:20px;font-weight:bold" align="left">
		<u><a href="#" onclick="$('referenceSearchTab').style.display='block';$('personSearchTab').style.display='none'" onmouseover="window.status='Search by reference no.'" onmouseout="window.status=''">Search by reference no.</a></u> | Search by patient name
	</div>
<table border="1" width="100%" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"	>
	<tr>
		<td width="50%" align="left" style="padding:5px">
			Search Person
			<input type="text" id="searchPerson" style="width:53%" onKeyUp="fetchPersonList(this.value, 300)">
			<input type="button" id="clearSearch" style="width:18%" value="Clear">
			<br/>
		</td>
		<td width="*" style="padding:5px" align="center">
			<table width="95%">
				<tr>
					<td width="10%"><strong>From</strong>
						<input type="text" id="dayStart" value="0" maxlength="4" size="4" onChange="if (isNaN(this.value)) this.value=0;">
						<strong>to</strong>
						<input type="text" id="dayEnd" value="9999" maxlength="4" size="4" onChange="if (isNaN(this.value)) this.value=9999;"> <strong>days ago</strong>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center" style="padding:1px">
		<div style="width:100%;height:324px;overflow:hidden;border:1px solid black;">
			<div style="width:100%;height:340px;overflow:scroll;border:0px">
			<table width="100%" border="0" cellpadding="0" cellspacing="1" id="personsTable">
				<thead>
					<tr class="reg_list_titlebar" style="font-weight:bold;height:32px" id="personsRowHeader">
						<th width="20%" nowrap>&nbsp;PID Nr</th>	
						<th width="24%" nowrap>Family name</th>
						<th width="24%">&nbsp;Given name</th>
						<th width="23%">&nbsp;Date of Birth</th>
						<th width="10%" align="center">View</th>
					</tr>					
				</thead>
				<tbody>
				</tbody>
			</table>
			</div></div>
		</td>		
		<td style="padding:1px">
			<div style="width:100%;height:324px;overflow:hidden;border:1px solid black;">			
			<div style="width:100%;height:340px;overflow:scroll;border:0px">
			<table width="100%" border="0" cellpadding="0" cellspacing="1" id="detailsTable">
				<thead>
					<tr class="reg_list_titlebar" style="font-weight:bold;height:32px" id="detailsRowHeader">
						<th width="34%" nowrap>&nbsp;Reference no.</th>
						<th width="34%" nowrap>Purchase date</th>
						<th width="12%">&nbsp;Type</th>
						<th width="10%">&nbsp;Edit</th>
						<th width="10%">&nbsp;Rmv</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			</div></div>
		</td>
	</tr>
</table>
</div>

{{$jsCalendarSetup}}
<br/>
<span id="tdShowWarnings" style=" font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal"></span>
<br/><img src="" vspace="1" width="1" height="1"><br/>
<div style="float:left;">
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1%">{{$sContinueButton}}</td>
		<td width="2">&nbsp;</td>
		<td>{{$sBreakButton}}</td>
	</tr>
</table>

	</div>
</div>

<!-- <input type="button" value="Add" onClick="retail_addTransaction('1000', 'Family', '0')"> 
<input type="button" value="Clear" onClick="retail_rmvTransaction(1)"> -->	
</div>
{{$sFormStart}}
{{$sHiddenFields}}
<input type="hidden" name="refno" id="sRefNo" value="">
<input type="hidden" name="pencnum" id="sPayerID" value="">
<input type="hidden" name="pname" id="sPayerName" value="">
<input type="hidden" name="purchasedt" id="sPurchaseDate" value="">
<input type="hidden" name="is_cash" id="sIsCash" value="">
<input type="hidden" name="saveok" value="1">
{{$sFormEnd}}