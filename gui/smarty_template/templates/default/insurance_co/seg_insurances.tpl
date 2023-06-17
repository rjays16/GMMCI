{{* prices.tpl  Form template for manage transactions module (pharmacy & meddepot) Segworks Technologies, Inc *}}

<table border=0 cellspacing=0 cellpadding=3 width="80%" bordercolor="#ffffdd">
	<tr bgcolor=#ffffdd>
		<td colspan=2>
			<FONT color="#800000"></font><br>
		</td>
	</tr>
</table>
<table border=0 cellspacing=0 cellpadding=3 width="80%" bordercolor="#ffffdd">	
	<tr bgcolor=#ffffdd>
		<td width="30">&nbsp;</td>
		<td align=left>Type in first few characters of name of health insurance:</td>
	</tr>
	<tr bgcolor=#ffffdd>
		<td width="30">&nbsp;</td>
		<td>
			<input type="text" id="keyword" size="130%" value="">
		</td>
	</tr>
</table>
<table border="0" cellspacing="2" cellpadding="3" width="80%">
	<tr>
		<td>
			<input type="reset" value="Reset" onClick="$('keyword').value='';$('keyword').focus();">
 		</td>
		<td align=right>
			<input type="button" value="Refresh" onclick="if ($('keyword').value) xajax_getHealthInsurances($('keyword').value);">
		</td>
	</tr>
</table>

<table border="1" width="80%" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" style="padding:1px">
			<div style="width:100%;height:304px;overflow:hidden;">
			<div style="width:100%;height:320px;overflow:scroll;">
			<table  id="hplans_table" class="segList" width="100%" border="0" cellpadding="0" cellspacing="1">		
				<thead>
					<tr class="wardlistrow1" id="hplans_header">
						<th align="center" width="10%">Select</th>
						<th align="center" width="25%">Name</th>
						<th align="center" width="40%">Company</th>
						<th align="center" width="15%">Contact Details</th>
						<th width="10%">&nbsp;</th>
					</tr>					
				</thead>
				<tbody>
				</tbody>
			</table>
			</div></div>
		</td>
	</tr>
	
</table>

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

<!--
<input type="button" value="Add" onClick="retail_addProductPrice('1000', 'Family', '0','0','0')"> 
<input type="button" value="Clear" onClick="retail_rmvProductPrice(1)"> 
<input type="button" value="Color" onClick="ppricecolorrow(1)">  -->
</div>
{{$sFormStart}}
{{$sHiddenFields}}
{{$sFormEnd}}