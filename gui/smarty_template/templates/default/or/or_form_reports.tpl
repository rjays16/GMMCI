{{* form.tpl  Form template for products module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}

{{$sFormStart}}
	<div style="padding:10px;width:95%;border:0px solid black">
	{{* NOTE:::  The following table  block must be inside the $sFormStart and $sFormEnd tags !!! *}}

	<!-- <font class="prompt">{{$sDeleteOK}}{{$sSaveFeedBack}}</font> -->
	<font class="warnprompt">{{$sMascotImg}} {{$sDeleteFailed}} {{$LDOrderNrExists}} <br> <!--{{$sNoSave}}--></font>
	<table border="0" cellspacing="1" cellpadding="3" style="" width="100%">
		<tbody class="submenu">
			<tr>
				<td align="right" width="140"><b>Select report</b></td>
				<td width="80%">{{$sReportSelect}}</td>
			</tr>
			<!--<tr id="section" style="display:none">
				<td align="right" width="140"><b>Social Service Section</b></td>
				<td width="80%">{{$sReportSelectGroup}}</td>
			</tr>
			<tr id="social_worker" style="display:none">
				<td align="right" width="140"><b>Social Worker</b></td>
				<td width="80%">{{$sReportEncoder}}</td>
			</tr>  -->
			<!-- Added by Cherry 08/03/10 -->
			<tr id="from2" style="display:none">
				<td align="right" width="140"><b>Date</b></td>
				<td>{{$sFromDateHidden2}}{{$sFromDateInput2}}{{$sFromDateIcon2}}</td>
			</tr>
			<tr id="prob_observation" style="display:none">
				<td align="right" width="140"><b>Human resource problems:</b></td>
				<td width="80%"><!--{{$sObservation}}--><textarea cols="43" name="observe" id="observe">{{$observe}}</textarea>  </td>
			</tr>
			<tr id="prob_material" style="display:none">
				<td align="right" width="140"><b>Materials/Equipment problems:</b></td>
				<!--<td width="80%">{{$sMaterials}}</td>   -->
				<td width="80%"><textarea name="materials" cols="43" id="materials">{{$materials}}</textarea></td>

			</tr>
			<tr id="prob_environment" style="display:none">
				<td align="right" width="140"><b>Physical Environment problems:</b></td>
				<!--<td width="80%">{{$sEnvironment}}</td> -->
				<td width="80%"><textarea name="environment" cols="43" id="environment">{{$environment}}</textarea></td>
			</tr>
			<tr id="prob_endorsement" style="display:none">
				<td align="right" width="140"><b>Special Endorsement:</b></td>
			<!--	<td width="80%">{{$sEndorsement}}</td>  -->
			<td width="80%"><textarea name="endorsement" cols="43" id="endorsement">{{$endorsement}}</textarea></td>
			</tr>
			<!-- End Cherry -->
			<tr id="from" style="display:none">
				<td align="right" width="140"><b>From</b></td>
				<td>{{$sFromDateHidden}}{{$sFromDateInput}}{{$sFromDateIcon}}</td>
			</tr>
			<tr id="to" style="display:none">
				<td align="right" width="140"><b>To</b></td>
				<td>{{$sToDateHidden}}{{$sToDateInput}}{{$sToDateIcon}}</td>
			</tr>
			<!--<tr>
				<td align="right" width="140"><b>Classification</b></td>
				<td width="80%">{{$sReportSelectClassification}}</td>
			</tr>
			<tr>
				<td align=right width=140>{{$LDReset}}</td>
				<td align=right>{{$sUpdateButton}}</td>
			</tr>-->
		</tbody>
	</table>

	{{$sHiddenInputs}}

{{$jsCalendarSetup}}
{{$sTransactionDetailsControls}}
<br/>
<div style="float:left;">
<table border="0" cellpadding="0" cellspacing="10">
	<tr>
		<td width="1%">{{$sContinueButton}}</td>
		<td width="1%">{{$sSaveButton}}</td>
	</tr>

</table>
</div>


</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$submitted}}
{{$show}}
{{$sFormEnd}}
{{$sTailScripts}}