{{* billing_transmittal_form.tpl  Form template for billing transmittal module (billing) Bong S. Trazo *}}
<style>
.ui-dialog-titlebar-close {
  visibility: hidden;
}
</style>
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br>
{{$sFormStart}}
<div id="loadingBox" style="display:none;" align="center">
	<strong>Generating XML ...</strong><br>
	<img id="xmlLoading" src="../../images/ajax_bar.gif" />
</div>
<div id="xmlParams" style="display:none;" align="center">
    <table>
        <tr>
            <td align="right">Total bills count:</td>
            <td id="billsCount">{{$billCount}}</td>
        </tr>
        <tr>
            <td align="right">Member Category:</td>
            <td>
                <select id="memcat">
                    <option value="all">All</option>
                    <option value="none">None</option>
                    {{$memcats}}
                </select>
            </td>
        </tr>
    </table>
</div>
<div id="mainTablediv" align="center">
		<table width="90%" cellpadding="2" cellspacing="2" id="mainTable" style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
		<tbody>
			<tr>
				<td width="88%" class="jedPanelHeader">
				TRANSMITTAL
				</td>
			</tr>
			<!-- Basic information -->
			<tr>
				<td rowspan="5" align="left" valign="top" class="segPanel">
					<table width="100%" border="0" cellpadding="2" cellspacing="0" style="font-size:11px">
						<tr class="segPanel">
							<td align="right" valign="middle"><strong>Insurance:</strong></td>
							<td width="38%" valign="middle">
								{{$sHCareDesc}}</td>
							<td width="10%" align="left" valign="middle">
								{{$sSelectHCare}}</td>
														<td align="right" valign="middle"><strong>Date:</strong></td>
														<td valign="middle" align="left">{{$sDate}}{{$sCalendarIcon}}</td>
<!--                            <td align="left">{{$sDate}}<strong style="font-size:10px">mm/dd/yyyy</strong></td>  -->
						</tr>
						<tr class="segPanel">
							<td  width="67" align="right" valign="top"><strong>Address:</strong></td>
							<td>{{$sHCareAddress}}</td>
						</tr>
												<tr class="segPanel">
														<td align="right" valign="middle"><strong>Control No.:</strong></td>
                            							<td valign="middle" align="left">{{$sTransmitNo}}&nbsp;{{$sResetTransmitNo}}</td>
												</tr>
												<tr class="segPanel">
														<td align="right" valign="top"><strong>Remarks:</strong></td>
														<td valign="middle" align="left">{{$sRemarks}}</td>
												</tr>
												<tr id="tbl_report1" {{$sNoShowButtons}}>
												<td align="left" colspan="7">&nbsp;</td>
												</tr>
												<tr id="tbl_report2" {{$sShowButtons}}>
													<td align="middle" valign="middle"><strong>PhilHealth Returned Claims:</strong></td>
													<td align="left" width="">From:
												<input type="text" segoption="claim_returned_from" paramname="date" value="" size="12" id="claim_returned_from" name="param" class="segInput"><img align="absmiddle" style="cursor:pointer;" id="tg_claim_returned_from" src="../../gui/img/common/default/show-calendar.gif"><script type="text/javascript">Calendar.setup ({inputField : "claim_returned_from",ifFormat : "%m/%d/%y", showsTime : false, button : "tg_claim_returned_from", singleClick : true, step : 1});</script>
									
									
												To:<input type="text" segoption="claim_returned_to" paramname="date" value="" size="12" id="claim_returned_to" name="param" class="segInput"><img align="absmiddle" style="cursor:pointer;" id="tg_claim_returned_to" src="../../gui/img/common/default/show-calendar.gif"><script type="text/javascript">Calendar.setup ({inputField : "claim_returned_to",ifFormat : "%m/%d/%y", showsTime : false, button : "tg_claim_returned_to", singleClick : true, step : 1});</script>
												{{$sBtnReport}}
									</td>
													<!-- <td align="left" width=""></td> -->
												</tr>
						</table>
					</td>
			</tr>
		</tbody>
	</table>
	<table width="90%">
		<tbody id="tbl_transmit_details_header">
						<tr id="tbl_transmit_details_hdr_row1" {{$sNoShowButtons}}>
								<td align="left" colspan="7">&nbsp;</td>
						</tr>
			<tr id="tbl_transmit_details_hdr_row2" {{$sShowButtons}}>
				<td align="left" colspan="6">{{$sBtnAddItem}}{{$sBtnDelete}}</td>
								<td align="right">{{$sBtnPrintDia}}{{$btnXml}}{{$sBtnPrint}}{{$sBtnSave}}</td>
			</tr>
		</tbody>
	</table>
	<table id="transmit_details" class="segList" border="0" cellpadding="0" cellspacing="0" width="90%">
		<thead id="tbl_transmit_details_hdr">
			<tr>
								<th width="2%">&nbsp;</th>
								<th width="9%">Policy No.</th>
								<th width="9%">Classification</th>
								<th width="30%">Confinement</th>
								<th width="8%">Case No.</th>
								<th width="20%">Patient</th>
								<th width="10%">Claim</th>
								<th width="10%">Meds/XLO<br>Outside</th>
								<th width="2%">&nbsp;</th>
			</tr>
		</thead>
		<tbody id="tbl_transmit_details_body">
{{$sTransmittalClaims}}
		</tbody>
	</table>
	<br />
	<br />
</div>

{{$jsCalendarSetup}}
{{$sHiddenItems}}

<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}

<div id="formpromptdbox" style="display:none">
<div class="hd" align="left">Select PHIC Form</div>
<div class="bd">
		<form id="formdbox" method="post" action="document.location.href">
				<table width="100%" class="segPanel">
						<tbody>
								<tr>
										<td align="right" width="*">Form:</td>
										<td align="left">
												<select id="forms_list" name="forms_list">
														<option value="">-Select Form-</option>
												</select>
									</td>
								</tr>
						</tbody>
				</table>
				{{$sFormsHiddenInputs}}
		</form>
</div>
</div>

<div id="memcategdialogbox" style="display:none">
<div class="hd" align="left">Membership Category to Print</div>
<div class="bd">
		<form id="mcategdbox" method="post" action="document.location.href">
				<table width="100%" class="segPanel">
						<tbody>
								<tr>
									<td align="center" width="45%">
											<select id="category_list" name="category_list">
													<option value="">-Select Category-</option>
											</select>
									</td>
									<td align="center" width="*"><input type="checkbox" id="is_detailed" name="is_detailed" value="">&nbsp;Detailed</td>
								</tr>
								<tr>
									<td align="left" width="*" colspan="2">
										<input type="checkbox" id="surgicalCase" name="surgicalCase" value="surgical" onclick="caseSurgical()" />&nbsp;Surgical
										<input type="checkbox" id="medicalCase" name="medicalCase" value="medical" onclick="caseMedical()" />&nbsp;Medical
										<input type="checkbox" id="print_doctor" name="print_doctor" value="">&nbsp;W/ Doctor's Name
									</td>
								</tr>
						</tbody>
				</table>
		</form>
</div>
</div>
<div id="viewreportbox" style="display:none">
<div class="hd" align="left">View Report for Returned Claims</div>
<div class="bd">
		<form id="vreportbox" method="post" action="document.location.href">
				<table width="100%" class="segPanel">
						<tbody>
								
								<tr>
									<td align="center" width="45%">
											<select id="newcategory_list" name="newcategory_list">
													<option value="">-Select Category-</option>
											</select>
									</td>
									<td align="center" width="*"><input type="checkbox" id="is_detailednew" name="is_detailednew" value="">&nbsp;Detailed</td>
								</tr>
								<!-- <tr>
									
									
								</tr> -->
								 
								<tr>
									<td align="left" width="*" colspan="2">
										<input type="checkbox" id="surgicalCase" name="surgicalCase" value="surgical" onclick="caseSurgical()" />&nbsp;Surgical
										<input type="checkbox" id="medicalCase" name="medicalCase" value="medical" onclick="caseMedical()" />&nbsp;Medical
										<input type="checkbox" id="print_doctor" name="print_doctor" value="">&nbsp;W/ Doctor's Name
									</td>
								</tr>
						</tbody>
				</table>
		</form>
</div>
</div>
<div id="dataeditbox" style="display:none">
<div class="hd" align="left">Update Encounter Information</div>
<div class="bd">
		<form id="dtaeditbox" method="post" action="document.location.href">
				<table width="100%" class="segPanel">
						<thead id="tbl_transmit_details_hdr">
								<tr>
										<th width="10%" align="left">Patient:</th>
										<th width="*" align="left" colspan="3"><input class="segInput" id="patientname" name="patientname" type="text" size="50" value="" disabled="disabled" readOnly></th>
								</tr>
						</thead>
						<tbody>
								<tr>
										<td align="left" width="10%" valign="middle">Member:</td>
										<td align="left" width="*" valign="middle" colspan="3">
												<input class="segInput" type="text" id="membernmlast" name="membernmlast" size="20" disabled="disabled"/><span>, </span>
												<input class="segInput" type="text" id="membernmfirst" name="membernmfirst" size="20" disabled="disabled"/>
												<input class="segInput" type="text" id="membernmmid" name="membernmmid" size="10" disabled="disabled"/>
												<span style="vertical-align:bottom; cursor:pointer"><img id="btn_editmem" src="{{$sRootPath}}/images/cashier_edit_3.gif" border="0" align="absmiddle" onclick="allowMbrEdit();"/></span>
										</td>
								</tr>
								<tr>
										<td align="left" width="10%">&nbsp;&nbsp;Address:</td>
										<td align="left" width="*" colspan="3"><input class="segInput" type="text" id="street_addr" name="street_addr" size="50" disabled="disabled"/></td>
								</tr>
								<tr>
										<td align="left" width="10%">&nbsp;&nbsp;&nbsp;</td>
										<td align="left" width="*" colspan="3">
											 <div id="barangay_autocomplete">
														<input type="text" size="25" value="" id="barangay" name="barangay" onblur="trimString(this);" disabled="disabled"/>
														<div id="barangay_container" style="width:30em"></div>
											 </div>
										</td>
								</tr>
								<tr>
										<td align="left" width="10%">&nbsp;&nbsp;&nbsp;</td>
										<td align="left" width="*" colspan="3">
											 <div id="municipality_autocomplete">
														<input type="text" size="25" value="" id="municipality" name="municipality" onblur="trimString(this);" disabled="disabled"/>
														<div id="municipality_container" style="width:45em"></div>
											 </div>
										</td>
								</tr>
								<tr>
										<td align="left"><label for="date">Discharge Date: </label></td>
										<td align="left" colspan="3">{{$sDischargeDate}}&nbsp;&nbsp;<label for="date">Time (24-hour format): </label><input type="text" id="dischrgtme" name="dischrgtme" size="6" onblur="checkTimeInput(this.value);" /></td>
								</tr>
								<tr>
<!--										<td align="left" width="10%" colspan="2">Final Diagnosis:</td>-->
										<td width="10%" nowrap="nowrap" align="left">
											 <div id="icdAutoComplete">
														<input type="text" size="25" value="" id="icdCode" name="icdCode" onblur="trimString(this);" />
														<div id="icdContainer" style="width:35em"></div>
											 </div>
										</td>
										<td width="*" nowrap="nowrap" align="left">
											 <div style="width:auto;" id="icdDescAutoComplete">
														<input type="text" size="25" value="" id="icdDesc" name="icdDesc" onblur="trimString(this);" />
														<div id="icdDescContainer" style="width:45em"></div>
											 </div>
										</td>
										<td style="vertical-align:middle;" width="13%">
											<div style="vertical-align:middle;"><input type="checkbox" id="is_primary" name="is_primary" value=""><span style="vertical-align:top;">Primary?</span></div>
										</td>
										<td valign="top" align="left" width="8%"><img id="btn_adddiag" style="cursor:pointer" src="{{$sRootPath}}/images/his_addbtn.gif" border=0 onclick="if (checkICDSpecific() && (document.getElementById('icdCode').value!='')) { addICDCode(); }" ></td>
								</tr>
								<tr>
									<td colspan="4">
										<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; overflow-x:hidden; width:100%; background-color:#e5e5e5">
										<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
											<thead>
												<tr class="nav">
												<th colspan="9">
													<div id="d-pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE, dcurpage, dlastpage, dfunc)">
														<img title="First" src="{{$sRootPath}}images/start.gif" border="0" align="absmiddle"/>
														<span title="First">First</span>
													</div>
													<div id="d-pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE, dcurpage, dlastpage, dfunc)">
														<img title="Previous" src="{{$sRootPath}}images/previous.gif" border="0" align="absmiddle"/>
														<span title="Previous">Previous</span>
													</div>
													<div id="d-pageShow" style="float:left; margin-left:10px">
														<span></span>
													</div>
													<div id="d-pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE, dcurpage, dlastpage, dfunc)">
														<span title="Last">Last</span>
														<img title="Last" src="{{$sRootPath}}images/end.gif" border="0" align="absmiddle"/>
													</div>
													<div id="d-pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE, dcurpage, dlastpage, dfunc)">
														<span title="Next">Next</span>
														<img title="Next" src="{{$sRootPath}}images/next.gif" border="0" align="absmiddle"/>
													</div>
													<input id="d-search" name="d-search" type="hidden" />
												</th>
											</tr>
											</thead>
										</table>
										</div>
										<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll;overflow-x:hidden; height:100px; width:100%; background-color:#e5e5e5">
											<table id="diagnosisList" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0" style="overflow:auto">
												<thead>
													<tr>
														<th width="10%" align="left">Code</th>
														<th width="40%" align="left">Diagnosis</th>
														<th width="*" align="left">Clinician</th>
														<th width="8%" align="center">Type</th>
														<th width="2%">&nbsp;</th>
													</tr>
												</thead>
												<tbody id="diagnosisList-body">
												</tbody>
											</table>
											<img id="d-ajax-loading" src="{{$sRootPath}}images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
										</div>
										</td>
								</tr>
								<tr>
										<td align="left" width="10%" colspan="4">Services Performed:</td>
								</tr>
								<tr>
									<td colspan="4">
										<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; overflow-x:hidden; width:100%; background-color:#e5e5e5">
										<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
											<thead>
												<tr class="nav">
												<th colspan="10">
													<div id="p-pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE, pcurpage, plastpage)">
														<img title="First" src="{{$sRootPath}}images/start.gif" border="0" align="absmiddle"/>
														<span title="First">First</span>
													</div>
													<div id="p-pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE)">
														<img title="Previous" src="{{$sRootPath}}images/previous.gif" border="0" align="absmiddle"/>
														<span title="Previous">Previous</span>
													</div>
													<div id="p-pageShow" style="float:left; margin-left:10px">
														<span></span>
													</div>
													<div id="p-pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE)">
														<span title="Last">Last</span>
														<img title="Last" src="{{$sRootPath}}images/end.gif" border="0" align="absmiddle"/>
													</div>
													<div id="p-pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE)">
														<span title="Next">Next</span>
														<img title="Next" src="{{$sRootPath}}images/next.gif" border="0" align="absmiddle"/>
													</div>
													<input id="p-search" name="p-search" type="hidden" />
												</th>
											</tr>
											</thead>
										</table>
										</div>
										<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll;overflow-x:hidden; height:100px; width:100%; background-color:#e5e5e5">
											<table id="proceduresList" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0" style="overflow:auto">
												<thead>
													<tr>
														<th width="20%" align="left">Code</th>
														<th width="60%" align="left">Procedure</th>
														<th width="*" align="left">Operation Date</th>
													</tr>
												</thead>
												<tbody id="proceduresList-body">
												</tbody>
											</table>
											<img id="p-ajax-loading" src="{{$sRootPath}}images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
										</div>
										</td>
								</tr>
								<tr>
									<td colspan="4">
										<div>
											<table>
												<tr>
												<td width="3%" align="left">Classification:</td>
												<td align="left" width="35%">
															<select id="entrycategory_list" name="entrycategory_list" onchange="jsCategoryOptionChange(this, this.options[this.selectedIndex].value, this.options[this.selectedIndex].text)">
																	<option value="">-Select Classification-</option>
															</select>
												</td>
												<td width="20%" align="right"><b>Policy No.:</b></td>
												<td align="left" width="*">
															<input type="text" size="25" value="" id="insurance_nr" name="insurance_nr" onblur="trimString(this);" />
												</td></tr>
											</table>
										</div>
									</td>
								</tr>
						</tbody>
				</table>
				{{$sDataEditHiddenInputs}}
		</form>
</div>
</div>
{{$sTailScripts}}
