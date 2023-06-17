{{* Template for medocs (medical diagnosis/therapy record) *}}
{{* Note: the input tags are left here in raw form to give the GUI designer freedom to change  the input dimensions *}}
{{* Note: be very careful not to rename nor change the type of the input  *}}

{{*if $bSetAsForm*}}
{{*$sDocShotcuts*}}
{{*$sDocsJavaScript*}}
<div style="width:100%">
	<table border="0" cellpadding="2" cellspacing="2" width="100%">
		<tbody>
			<tr>
				<td class="segPanelHeader" colspan="2">Patient Information</td>
			</tr>
			<tr>
				<td class="segPanel" id="hpid" width="30%"><strong>Health Record Number</strong></td>
				<td class="segPanel" id="spid">{{$sPid}}</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table border="0" cellpadding="2" cellspacing="2" width="100%">
		<tbody>
			<tr>
				<td class="segPanelHeader">Admitting Diagnosis</td>
			</tr>
			<tr>
				<td class="segPanel" id="admitting_diagnosis"></td>
			</tr>
		</tbody>
	</table>
		</tbody>
	</table>
</div>
<br />

<br>
<table width="100%">
<div id="rqlistdiv" align="left" style="display:''">
<span style="font-weight:bold" class="segPanel">List of Current Request</span>
	<table id="rqlisttable" width="88%" class="segList" border="0">
		<thead>
			<tr>
				<th width="25%">Batch No.</th>
				<th width="25%">Date Requested</th>
				<th width="25%">Department</th>
				<th width="20%">Total Charge</th>
				<th width="5%">&nbsp;</th>
				<th width="20">Discount</th>
			</tr>
		</thead>
		<tbody id="rqlisttbody">
		</tbody>
	</table>
</div>
</table>


{{$sTailScripts}}
{{$sTailScripts2}}
<!--</form>-->
{{*/if*}}