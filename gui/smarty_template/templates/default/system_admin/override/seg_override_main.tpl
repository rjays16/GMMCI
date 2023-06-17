{{* Frame template of medocs page *}}
{{* Note: this template uses a template from the /registration_admission/ *}}
<!--<div align="center" style="width:95%"> -->
<div align="left" style="width:100%">
	<table width="100%" border="0" cellspacing="5" cellpadding="0">
		<tr>
			<td width="50%" valign="top">
				 <table border="0" cellpadding="2" cellspacing="2" width="100%">
					<tbody>
						<tr>
							<td class="segPanelHeader" colspan="3">Patient Information</td>
						</tr>
						<tr>
							<td class="segPanel" id="hpid" width="1%" nowrap="nowrap"><strong>Health Record Number</strong></td>
							<td class="jedPanel3" id="spid" width="50%">{{$sPid}}</td>
							<td rowspan="8" class="photo_id">{{$img_source}}</td>
						</tr>
						<tr>
							<td class="segPanel" id="hcase_no"><strong>Personnel No.</strong></td>
							<td class="jedPanel3" id="scase_no">{{$sPersonnelNo}}</td>
						</tr>
						<tr>
							<td class="segPanel" id="hcase_no"><strong>Job Function</strong></td>
							<td class="jedPanel3" id="scase_no">{{$sJobFunction}}</td>
						</tr>
						<tr>
							<td class="segPanel" id="hcase_no"><strong>Case Number</strong></td>
							<td class="jedPanel3" id="scase_no">{{$sEncNrPID}}</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Title</strong></td>
							<td class="jedPanel3">{{$title}}</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Family Name</strong></td>
							<td class="jedPanel3">{{$name_last}}</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Given Name</strong></td>
							<td class="jedPanel3">{{$name_first}}</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Gender</strong></td>
							<td class="jedPanel3"  colspan="2">{{$sSexType}}</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Date of Birth</strong></td>
							<td class="jedPanel3"  colspan="2">{{$sBdayDate}} &nbsp; {{$sCrossImg}} &nbsp; <font color="black">{{$sDeathDate}}</font></td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Place of Birth</strong></td>
							<td class="jedPanel3"  colspan="2">{{$sBirthPlace}}</font></td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Age</strong></td>
							<td class="jedPanel3"  colspan="2">{{$sAge}}</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Civil Status</strong></td>
							<td class="jedPanel3"  colspan="2">{{$sCivilStat}}</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Religion</strong></td>
							<td class="jedPanel3"  colspan="2">{{$sReligion}}</font></td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Occupation</strong></td>
							<td class="jedPanel3"  colspan="2">{{$sOccupation}}</font></td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Address</strong></td>
							<td class="jedPanel3" colspan="2">{{$sAddress}}</font></td>
						</tr>
						<tr id="senior_row" style="display:none">
							<td class="segPanel" id="tdsenior1"><strong>{{$sSeniorLabel}}</strong></td>
							<td class="jedPanel3" id="tdsenior2">{{$sSeniorID}}</td>
						</tr>
						<tr>
							<td class="segPanelHeader" colspan="3">Admitting Diagnosis</td>
						</tr>
						<tr>
							<td id="admitting_diagnosis" class="jedPanel3" colspan="3"></td>
						</tr>
					</tbody>
				</table>
			</td>
			<td width="50%" valign="top">
				 <table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top">
							<div id="requests" class="dashlet" align="left" style="width:100%">
								<table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td width="99%" nowrap="nowrap"><h1>List of current requests</h1></td>
										</tr>
								</table>
							</div>
							{{$sRequestList}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>

{{$sTailScripts}}
{{$sTailScripts2}}
<!--</form>-->