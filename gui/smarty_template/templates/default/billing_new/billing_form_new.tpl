{{* form.tpl  Form template for new billing module : Jarel Q. Mamac *}}
<div id="loadingBox" style="display:none;" align="center">
    <strong>Please wait ...</strong><br>
    <img id="imgLoading" src="../../images/ajax_bar.gif" />
</div>
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />
<div id="mainTablediv" align="center">
        <table width="96%" cellpadding="0" cellspacing="3">
            <tbody id="buttons_bar">
                <tr>
                    <td width="8" valign="bottom" align="left">{{$sBtnInsurance}}</td>
                    <td width="148" valign="bottom" align="left">{{$sBtnPrevPackage}}</td>
                    <td width="200" valign="bottom" align="left">{{$sBtnDiagnosis}}</td>
                    <td width="*">&nbsp;</td>
                    {{$sBtnCSFp2}}
                    {{$sBtnSave}}
                    {{$sBtnNew}}
                    {{$sBtnDelete}}
                    {{$sBtnPrint}}
                    {{$sBtnCF2Part3}}
                    {{$sChckDetail}}
                    {{$sChckFinal}}
               </tr>
            </tbody>
        </table>
        <table width="96%" cellpadding="2" cellspacing="2" id="mainTable" style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
            <thead>
                <tr>
                    <th id="billcol_01" colspan="2" rowspan="2" align="left" class="jedPanelHeader" style="border-right:none">BILLING STATEMENT&nbsp;&nbsp;{{$sBillStatus}}</th>
                    <th id="billcol_02" colspan="2" rowspan="2" align="center" class="jedPanelHeader" style="border-right:none;border-left:none"><span id="remaindays" style="display:none"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="coverdays" style="display:none"></span><span id="savethis" style="display:none"></span></th>
                   <th class="jedPanelHeader" style="border-left:none" align="right"><div id="categ_col" style="display:none">{{$sMembershipCategory}}&nbsp;MEMBERSHIP CATEGORY:&nbsp;&nbsp;&nbsp;<span id="mcategdesc" name="mcategdesc"></span></div></th>
                    <th class="jedPanelHeader" style="border-left:none" align="right">
                        <div id="categ_col" style="display:none">
                            {{$sLblMembershipCategory}}
                        </div>
                    </th>
                </tr>
            </thead>
        </table>
        <table width="96%" cellpadding="2" cellspacing="2" id="mainTable2" style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
            <tbody>
                <!-- Basic information -->
                <tr>
                    <td colspan="2" rowspan="5" align="left" valign="top" class="jedPanel">
                        <table width="100%" border="0" cellpadding="2" cellspacing="0" style="font-size:11px">
                            <tr class="jedPanel">
                                    <td width="5%" align="right"><strong>HRN:</strong></td>
                                    <td width="50px" align="left">{{$sPid}}</td>
                                    <td colspan="2" width="14%" align="right"><strong>Case No:</strong></td>
                                    <td width="25%" align="left" valign="middle">{{$sEncounter}}</td>
                                    <td colspan="2" width="13%" align="right"><strong>CLASSIFICATION:</strong></td>
                                    <td width="10%" align="left"><span style="color:#0000FF" id="classification">NONE</span></td>

                            </tr>
                            <tr class="jedPanel">
                                    <td align="right" valign="middle"><strong>Name:</strong></td>
                                    <td colspan="2" width="50px" valign="middle">
                                        {{$sPatientName}}
                                        <span style="vertical-align:bottom">{{$sSelectPatient}}</span>
                                    </td>
                                    <td width="8%" align="right" valign="middle"><strong>Billing Date:</strong></td>
                                    <td colspan="2" width="20%" valign="middle" align="left">{{$sDate}}</td>
                                    <td id="confine_label" width="10%" align="right" valign="top" style="display:none"><strong>Confinement:</strong></td>
                                    <td id="confine_cbobox" width="15%" align="left" valign="top" style="display:none">{{$sDrpConfinement}}</td>
                            </tr>
                            <tr class="jedPanel">
                                    <td width="*" align="right" valign="top"><strong>Address:</strong></td>
                                    <td rowspan="2" width="50px">{{$sPatientAddress}}</td>
                                    <td align="right" colspan="2" valign="top"><strong>Case Date:</strong></td>
                                    <td valign="top" colspan="2">{{$sAdmissionDate}}</td>
                                    <td width="10%" align="right" valign="middle"><span><strong>Case Type:</strong></span></td>
                                    <td width="15%" align="left" valign="middle">{{$sDrpCaseType}}</td>
                                    <!-- <td id="lastbill_label" style="display:none" colspan="2" align="right" valign="middle" width="15%"><strong>Last Bill:</strong></td>
                                    <td id="lastbill_actualdate" style="display:none" valign="top" width="10%">{{$sLastBillDate}}</td> -->
                            </tr>

                            <tr class="jedPanel">
                                <td width="*" align="right">&nbsp;</td>
                                <td width="50px" align="left">&nbsp;</td>
                                <td width="8%" align="right" valign="middle"><strong>Days Admitted:</strong></td>
                                <td colspan="2" width="20%" valign="middle" align="left">{{$sAdmDays}}</td>
                                <!-- Added by EJ 10/03/2014 -->
                                <td width="8%" align="right" valign="middle">&nbsp;</td>
                                <td colspan="2" width="20%" valign="middle" align="left">{{$sPatientNote}}</td>
                                <!-- Ended by EJ 10/03/2014 -->
                            </tr>

                            <tr class="jedPanel">
                                <td width="*" align="right"><strong>Age :</strong></td>
                                <td width="50px" align="left">{{$sAge}}</td>
                                <td align="right" colspan="2" valign="top"><strong>Other Insurance:</strong></td>
                                <td valign="top" colspan="2">{{$sOtherInsurances}}</td>
                            </tr>

                            <tr class="jedPanel">
                                <td width="*" align="right"><strong>Insurance No. :</strong></td>
                                <td width="50px" align="left">{{$sPhic}}</td>
                                <td colspan="2" width="*" align="right" valign="top">&nbsp;</td>
                                <td id="admit_label" style="display:none" align="right" colspan="2" valign="middle"><strong>Adm.:</strong></td>
                                <td id="admit_date" style="display:none" valign="top">{{$sAdmitDate}}</td>
                                    <div id="ShowMedicoLegal" style="display:none">
                                        <input class="segInput" id="ShowMedicoCases" name="ShowMedicoCases" type="text" size="16" value="" style="font:bold 12px Arial; float;left;" readOnly >');
                                    </div>
                                <td id="medicolegal" style="display:none; color:red"  onmouseover="return overlib($('ShowMedicoCases').value, LEFT);"
                                onmouseout="return nd();" colspan="2" align="left" valign="middle" width="10%"><strong>Medico Legal</strong>
                                </td>
                            </tr>
                            <tr class="jedPanel">
                                <td colspan="2" width="20%" valign="middle" align="left">
                                    <input type="checkbox" name="isdied" id="isdied" style="vertical-align:middle" onclick="toggleDeathDate(1)">
                                    <strong>Check if Patient is already Dead</strong></td>
                                <td width="20px" valign="middle" align="left"></td>
                                <td id="label_deathdate" width="20px" align="right" valign="middle" style="display:none"><strong>Death Date:</strong></td>
                                <td id="input_deathdate" colspan="2" width="20%" valign="middle" align="left" style="display:none">
                                    {{$sDeathDate}}
                                </td>

                                <!-- For Patient that is a regular dialysis Added by genz-->
                                <!-- <td id="dialysisCheckBox" colspan="2" width="20%" valign="middle" align="right" style="display:none">
                                    <input type="checkbox" name="isdialysis" id="isdialysis" style="vertical-align:middle" onclick="toggleDialysis(1)">
                                    <strong>Check if Patient is a Regular Dialysis</strong>
                                </td>
                                <td width="20px" valign="middle" align="right"></td>
                                <td id="label_dialysis"align="right" valign="middle" style="display:none">
                                    <strong>Current Count of Dialysis:</strong>
                                </td>
                                <td id="input_dialysis" valign="middle" align="left" style="display:none">
                                    {{$sDialysis}}
                                    <span id="done_dialysis" style="display:''">
                                        <button id="Btndialysis" onclick="saveDoneRegularDialysis(1)" value="Done">Done</button>
                                    </span>
                                    <span id="undone_dialysis" style="display:none">
                                        <button id="Btndialysis1" onclick="saveDoneRegularDialysis(2)" value="Undone">Undone</button>
                                    </span>
                                </td> -->
                                <!-- For Patient that is a regular dialysis Added by genz-->

                            </tr>
                         </table>
                    </td>
                </tr>
            </tbody>
    </table>
    <!-- Billing Details -->
    <div id="bBody" style="width:100%" align="center">
            <!-- billing Header Details -->
        <div id="accommodation_div" align="center">
            <table width="96%" class="segPanelHeader" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #cccccc; margin-top:5px; color:black">
                <tbody>
                    <!-- Accommodation -->
                    <tr>
                        <td>
                            <style type="text/css">
                                    #body_accListDetails tr td, #body_mdListDetails tr td, #body_supListDetails tr td, #body_docRoleArea tr td, #body_hsListDetails tr td, #body_opsListDetails tr td, #body_mscListDetails tr td {
                                            font:normal 12px Arial, Helvetica, sans-serif;
                                    }
                            </style>
                            <table id="accListDetails" width="100%" cellpadding="0" cellspacing="0" border="1" class="segList">
                                    <thead class="togglehdr">
                                        <tr>
                                            <th class="toggleth" width="3%"><div class="arrow"></div></th>
                                            <th align="left" width="*" style="font-weight:bold; font-size:15px;">Accommodation&nbsp;{{$sBtnAddAccommodation}}</th>
                                            <th width="15%"><span style="font-size:12px;">No. of Days</span></th>
                                            <th width="15%"><span style="font-size:12px;">Rate</span></th>
                                            <th width="15%"><span style="font-size:12px;">Total</span></th>
                                        </tr>
                                    </thead>
                                    <tbody class="toggle" id="body_accListDetails">
                                    </tbody>
                                    <tbody class="billfooter" id="footer_accListDetails">
                                        <tr>
                                            <td class="billftr1" align="left" colspan="2">
                                                <span id="accProgStatus" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif">Please wait ... computing accommodation charges.</span><br />
                                                <span id="accProgBar" style="display:none; float:left">{{$sProgBar}}</span><br />
                                                <span>&nbsp;</span>
                                            </td>
                                            <td class="billftr2" align="right" colspan="2">
                                                <span>Sub-Total</span><br />
                                            </td>
                                            <td align="right">
                                                <span id="accAP">0.00</span><br />
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody id="hdAccommodationRef" name="hdAccommodationRef" style="display:none;">

                                    </tbody>
                            </table>
                        </td>
                    </tr><!-- end of Accommodation -->
                    <tr><td></td></tr>
                 </tbody>
            </table>
        </div>
<!-- Hospital Services -->
<table width="96%" class="segPanelHeader" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #cccccc; margin-top:5px; color:black">
    <tbody>
        <tr>
            <td>
                <table id="hsListDetails" width="100%" border="1" cellpadding="0" cellspacing="0" class="segList">
                    <thead class="togglehdr">
                        <tr>
                            <th class="toggleth" width="3%"><div class="arrow"></div></th>
                            <th align="left" width="*" style="font-weight:bold; font-size:15px;">
                                X-Ray, Lab, & Others&nbsp;<span>{{$sBtnAddMiscService}}</span>&nbsp;
                            </th>
                            <th width="17%">Department - Area</th>
                            <th width="15%">Qty</th>
                            <th width="15%">Price</th>
                            <th width="15%">Total</th>
                        </tr>
                    </thead>
                    <tbody class="toggle" id="body_hsListDetails">
                    </tbody>
                    <tbody class="billfooter" id="footer_hsListDetails">
                        <tr>
                            <td class="billftr1" align="left" colspan="2">
                                <span id="hsProgStatus" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif">Please wait ... computing XLO charges.</span><br />
                                <span id="hsProgBar" style="display:none; float:left">{{$sProgBar}}</span><br />
                                <span>&nbsp;</span>
                            </td>
                            <td class="billftr2" align="right" colspan="3">
                                <span>X-Ray, Lab, & Others Sub-Total</span><br />
                            </td>
                            <td align="right" width="15%">
                                <span id="hsAP">0.00</span><br />
                            </td>
                        </tr>
                    </tbody>
                    <tbody id="hdXLORef" name="hdXLORef" style="display:none;">
                                        
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<div class="segPanel" id="dialogMiscServicesDelConfirm" style="display:none">
    <p>
        <span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
        Do you really want to remove <span id="delMiscServName" name="delMiscServName"></span>?<br>
        NOTE: Deletion will remove only 1 instance of the miscellaneous service.
    </p>
    <input type="hidden" name="delMiscServCode" id="delMiscServCode" value=""/>
    <input type="hidden" name="delSource" id="delSource" value=""/>
</div>
<!--Medicines -->
<table width="96%" class="segPanelHeader" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #cccccc; margin-top:5px; color:black">
    <tbody>
        <tr>
            <td>
                <table id="mdListDetails" width="100%" border="1" cellpadding="0" cellspacing="0" class="segList">
                    <thead class="togglehdr">
                        <tr>
                            <th class="toggleth" width="3%"><div class="arrow"></div></th>
                            <th width="*" align="left" style="font-weight:bold; font-size:15px;">Drugs & Medicines&nbsp;&nbsp;&nbsp;
                                            <span>{{$sAddMedsandSupplies}}</span>
                                            &nbsp;</th>
                            <th width="15%">Quantity</th>
                            <th width="15%">Item Price</th>
                            <th width="15%">Total</th>
                        </tr>
                    </thead>
                    <tbody class="toggle" id="body_mdListDetails">
                    </tbody>
                    <tbody class="billfooter" id="footer_mdListDetails">
                        <tr>
                            <td class="billftr1" align="left" colspan="2">
                                <span id="mdProgStatus" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif">Please wait ... computing drugs & meds charges.</span><br />
                                <span id="mdProgBar" style="display:none; float:left">{{$sProgBar}}</span><br />
                                <span>&nbsp;</span>
                            </td>
                            <td class="billftr2" align="right" colspan="2">
                                <span>Drugs & Medicines Sub-Total</span>
                            </td>
                            <td align="right">
                                <span id="medAP">0.00</span>
                                <input type="hidden" name="hdTotalMeds" id="hdTotalMeds" value=""/>
                            </td>
                        </tr>
                    </tbody>
                    <tbody id="hdMedRef" name="hdMedRef" style="display:none;">
                                        
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<div class="segPanel" id="dialogMedicineDelConfirm" style="display:none">
    <p>
        <span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
        Do you really want to remove <span id="delMedName" name="delMedName"></span>?<br>
        NOTE: Deletion will remove only 1 instance of the miscellaneous service.
    </p>
    <input type="hidden" name="delMedCode" id="delMedCode" value=""/>
</div>
<!-- Operation/Procedures -->
<div id="op_div" align="center">
<table width="96%" class="segPanelHeader" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #cccccc; margin-top:5px; color:black">
    <tbody>
        <tr>
            <td>
                <table id="opsListDetails" width="100%" border="1" cellpadding="0" cellspacing="0" class="segList">
                    <thead class="togglehdr">
                        <tr>
                            <th class="toggleth" width="3%"><div class="arrow"></div></th>
                            <th width="*" align="left" style="font-weight:bold; font-size:15px;">
                                Operating / Del. Room&nbsp;&nbsp;&nbsp;{{$sAddOPAccommodation}}&nbsp;
                            </th>
                            <th width="15%">RVU</th>
                            <th width="15%">Multiplier</th>
                            <th width="15%">Total</th>
                        </tr>
                    </thead>
                    <tbody class="toggle" id="body_opsListDetails">
                    </tbody>
                    <tbody class="billfooter" id="footer_opsListDetails">
                        <tr>
                            <td class="billftr1" align="left" colspan="2">
                                    <span id="opsProgStatus" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif">Please wait ... computing OP charges.</span><br />
                                    <span id="opsProgBar" style="display:none; float:left">{{$sProgBar}}</span><br />
                                    <span>&nbsp;</span>
                            </td>
                            <td class="billftr2" align="right" colspan="2">
                                    <span>Operating / Delivery Room Sub-Total</span>
                            </td>
                            <td align="right" width="15%">
                                    <span id="opsAP">0.00</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
</div>

<!-- Miscellaneous Charges -->
<table width="96%" class="segPanelHeader" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #cccccc; margin-top:5px; color:black">
    <tbody>
        <tr>
            <td>
                <table id="mscListDetails" width="100%" border="1" cellpadding="0" cellspacing="0" class="segList">
                    <thead class="togglehdr">
                        <tr>
                            <th class="toggleth" width="3%"><div class="arrow"></div></th>
                            <th width="*" align="left" style="font-weight:bold; font-size:15px;">
                                Miscellaneous Charges&nbsp;&nbsp;&nbsp;
                                <span>{{$sAddMiscChrg}}</span>&nbsp;
                            </th>
                            <th width="15%">Quantity</th>
                            <th width="15%">Unit Price</th>
                            <th width="15%">Total Charge</th>
                        </tr>
                    </thead>
                    <tbody class="toggle" id="body_mscListDetails">
                    </tbody>
                    <tbody class="billfooter" id="footer_mscListDetails">
                        <tr>
                            <td class="billftr1" align="left" colspan="2">
                                <span id="mscProgStatus" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif">Please wait ... computing miscellaneous charges.</span><br />
                                <span id="mscProgBar" style="display:none; float:left">{{$sProgBar}}</span><br />
                                <span>&nbsp;</span>
                            </td>
                            <td class="billftr2" align="right" colspan="2">
                                <span>Miscellaneous Sub-Total</span><br/>
                            <!-- added by Nick 1/12/2014 -->
                              <!--   <span>Discount</span><br/>
                                <span>Excess</span> -->
                            </td>
                            <td align="right">
                                <span id="mscAP">0.00</span><br/>
                                <!-- <span id="msDiscount">0.00</span><br/>
                                <span id="msEX">0.00</span> -->
                            </td>
                            <!-- end nick -->
                        </tr>
                    </tbody>
                    <tbody id="hdMiscChargesRef" name="hdMiscChargesRef" style="display:none;">
                                        
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<div class="segPanel" id="dialogMiscChargesDelConfirm" style="display:none">
    <p>
        <span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
        Do you really want to remove <span id="delMiscChargeName" name="delMiscChargeName"></span>?<br>
        NOTE: Deletion will remove only 1 instance of the miscellaneous service.
    </p>
    <input type="hidden" name="delMiscChargeCode" id="delMiscChargeCode" value=""/>
</div>
<!-- Sub Totals -->
<table width="96%" class="segPanelHeader" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #cccccc; margin-top:5px; color:black">
    <tbody>
        <tr>
            <td>
                <table id="subTotalDetails" width="100%" border="1" cellpadding="0" cellspacing="0" class="segList">
                    <thead class="togglehdr">
                        
                        <tr>
                            <th> </th>
                            <th> </th>
                            <th width="50%">HOSPITAL CHARGES</th>
                            <th> </th>
                            <th > </th>
                            <!-- <th width="15%">Coverage</th> -->
                        </tr>

                    </thead>
                    <tbody class="billfooter" id="footer_subTotalDetails">
                        <tr>
                            <td class="billftr1" align="left" colspan="2">
                                <span id="subTotalDetailsStatus" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif">Please wait ... computing miscellaneous charges.</span><br />
                                <span id="subTotalDetailsProgBar" style="display:none; float:left">{{$sProgBar}}</span><br />
                                <span>&nbsp;</span>
                            </td>
                            <td class="billftr2" align="right" colspan="2">
                                <span>Hospital Income Total</span><br />
                                <span>Discount</span><br />
                                <span>
                                <div id="hcitooltip" style="display:none">Edit coverage distribution for HCI.</div>
                                <a id="hcicvrg" onmouseout="return nd();" onmouseover="return overlib($('hcitooltip').innerHTML, LEFT);"  style="cursor:pointer">[Health Insurance] Total Coverage</a>
                                </span><br />
                                <span>Excess</span>
                            </td>
                            <td align="right" width="15%">
                                <span id="hiTotal">0.00</span><br />
                                <span id="hiDiscount">0.00</span><br />
                                <span id="hiHIC">0.00</span><br />
                                <span id="hiEX">0.00</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<!-- Doctors' professional Fees -->
<!--edited daryl 01/02/14-->
<div id="pf_div" align="center">
<table width="96%" class="segPanelHeader" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #cccccc; margin-top:5px; color:black">
    <tbody>
        <tr>
            <td>
                <table id="docRoleArea" width="100%" border="1" cellpadding="0" cellspacing="0" class="segList">
                    <thead class="togglehdr">
                        <tr>
                            <th colspan="4" align="left" style="font-weight:bold; font-size:15px;">
                                Doctors' Fees {{$sBtnAddDoctorsButton}}&nbsp;
                            </th>
                        </tr>
                        <tr>
                            <th class="toggleth" width="3%"><div class="arrow"></div></th>
                            <th width="*">Description</th>
                            <th width="15%">Total Charge</th>
                            <!-- <th width="15%">Coverage</th> -->
                        </tr>
                    </thead>
                    <tbody class="toggle" id="body_docRoleArea">
                    </tbody>
                    <tbody class="billfooter" id="footer_docRoleArea">
                        <tr>
                            <!-- <td class="billftr1" align="left">
                                <span id="pfProgStatus" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif">Please wait ... computing Prof. Fees.</span><br />
                                <span id="pfProgBar" style="display:none; float:left">{{$sProgBar}}</span><br />
                                <span>&nbsp;</span>
                            </td> -->
                            <td class="billftr2" align="right" colspan="2">
                                <span>Doctors' Fees Sub-Total</span><br />
                                <span>Discount</span><br />
                                <span>
                                <div id="doctooltip" style="display:none">Edit coverage distribution of Doctor's PF.</div>
                                <a id="doccvrg" onmouseout="return nd();" onmouseover="return overlib($('doctooltip').innerHTML, LEFT);"  style="cursor:pointer">[Health Insurance] Total Coverage</a>
                                </span><br />
                                <span>Excess</span>
                            </td>
                            <td align="right" width="15%">
                                <span id="pfAP">0.00</span><br />
                                <span id="pfDiscount">0.00</span><br />
                                <span id="pfHC">0.00</span><br />
                                <span id="pfEX">0.00</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
</div>

<!-- Previous payment -->
<table width="96%" class="segPanelHeader" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #cccccc; margin-top:5px; color:black">
    <tbody>
        <tr>
            <td width="100%">
                <table width="100%" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="25%" colspan="3" align="left" style="font-weight:bold; font-size:15px;">
                                &nbsp;<span style="vertical-align:top">Package</span>&nbsp;{{$sBtnOutMedsXLO}}
                            </th>
                            <th colspan="2" align="left" style="font-weight:bold; font-size:15px;">
                                    <span>DUE & PAYABLE</span>{{$sBtnDiscountDetails}}
                            </th>
                        </tr>
                    </thead>
                    <tr>
<!--                         <td id="td01" width="25%" rowspan="5" style="border-right:none;">
                            <span id="pkg_label">Select Package:</span><br>
                            <span>&nbsp;</span><br>
                            <span id="cvg_label">
                                <div id="pkgtooltip" style="display:none">Edit distribution of package coverage.</div>
                                <a style="cursor:pointer" onclick="openPkgCoverage();" onmouseover="return overlib($('pkgtooltip').innerHTML, LEFT);" onmouseout="return nd();">Coverage of Package:</a>
                            </span><br>
                            <span>&nbsp;</span>
                        </td> -->
                        <td id="td02" width="50%" rowspan="7" align="left" style="border-right:none; border-left:none;">
                            <span id="" style="font-weight: bold;">First Case Rate<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>:</span>
                            <span id="">
                                <select id="first_rate" name="first_rate">
                                    <option value="0" id="0">-Select Code-</option>
                                </select>
                            </span>
                            <span>&nbsp;&nbsp;&nbsp;P&nbsp;</span>
                            <span id="first_rate_amount">00.00</span><br/>
                            <span>&nbsp;&nbsp;</span>
                            <span id="first_case_desc">No case rate selected.</span><br/>
                            <span id="" style="font-weight: bold;">Second Case Rate :</span>
                            <span id="">
                                <select id="second_rate" name="second_rate">
                                    <option value="0" id="0">-Select Code-</option>
                                </select>
                            </span>
                            <span>&nbsp;&nbsp;&nbsp;P&nbsp;</span>
                            <span id="second_rate_amount">00.00</span><br/>
                            <span>&nbsp;&nbsp;</span>
                            <span id="second_case_desc">No case rate selected.</span><br/>
                            <span>&nbsp;</span>
                            <span id="rate_total_amount">{{$sRateTotalAmount}}</span><br>
                        </td>
                        <td id="td03" width="1%" rowspan="10" style="border-left:none; border-right-style:solid; border-right-width:thin; border-right-color:#436499;">
                            <span>&nbsp;</span><br>
                            <span>&nbsp;</span><br>
                            <span>&nbsp;</span><br>
                            <span>&nbsp;</span>
                        </td>
                    </tr>
                    <!-- <tr>
                        <td class="tdcell" colspan="2" align="left" style="font-weight:bold; font-size:15px;">
                            <span>DUE & PAYABLE</span>{{$sBtnDiscountDetails}}
                        </td>
                    </tr>  -->
                    <tr id="prevbill" style="display:none">
                        <td width="25%">
                            <span id="lastProgBar2" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif"></span>
                            <span id="prevbill_label">PREVIOUS BILL AMOUNT</span>
                        </td>
                        <td align="right" id="prevbillamt">0.00</td>
                    </tr>
                    <tr id="nobalance" style="display:none">
                        <td width="25%">
                            <span id="lastProgBar1" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif"></span>
                            <span id="sponsored_label">SPONSORED - No Balance Billing</span>
                        </td>
                        <td align="right" id="sponsored_amount">0.00</td>
                    </tr>
                    <tr id="infirmary" style="display:none">
                        <td width="25%">
                            <span id="lastProgBar3" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif"></span>
                            <span id="infirmary_label">Infirmary Discount</span>
                        </td>
                        <td align="right" id="infirmary_amount">0.00</td>
                    </tr>
                    <tr>
                        <td width="25%">
                            <span id="lastProgBar" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif">
                                {{$sProgBar}}&nbsp;Please wait ... computing amount due.
                            </span>
                            <span id="amntlabel">Total Gross Amount :</span>
                        </td>
                        <td align="right" id="netbill">00.00</td>
                    </tr>
                    <tr>
                        <td width="25%">
                            <span id="lastProgBar" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif">
                                {{$sProgBar}}&nbsp;Please wait ... computing amount due.
                            </span>
                            <span id="amntlabel">Health Insurance Total Coverage :</span>
                        </td>
                        <td align="right" id="HealthInsuranceTotal">00.00</td>
                    </tr>
                    <tr>
                        <td width="25%">
                            <span id="lastProgBar" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif">
                                {{$sProgBar}}&nbsp;Please wait ... computing amount due.
                            </span>
                            <span id="amntlabel_discount">Total Discount :</span>
                        </td>
                        <td align="right" id="DiscountTotal">00.00</td>
                    </tr>
                    <tr>
                        <td width="25%">
                            <span id="lastProgBar" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif">
                                {{$sProgBar}}&nbsp;Please wait ... computing amount due.
                            </span>
                            <span id="amntlabel">Deposit :</span>
                        </td>
                        <td align="right" id="bdeposit">00.00</td>
                    </tr>
                 <!--    <tr>
                        <td width="25%">
                            <span id="lastProgBar" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif">
                                {{$sProgBar}}&nbsp;Please wait ... computing amount due.
                            </span>
                            <span id="amntlabel">Return Meds. :</span>
                        </td>
                        <td align="right" id="netbill">00.00</td>
                    </tr> -->
                    <tr>
                        <td width="25%">
                            <span id="lastProgBar" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif">
                                {{$sProgBar}}&nbsp;Please wait ... computing amount due.
                            </span>
                            <span id="amntlabel">Total Net Amount</span>
                        </td>
                        <td align="right" id="netamnt">00.00</td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>

    </div>
</div>

<!-- {{$jsCalendarSetup}} -->
<!-- {{$jsDCalendarSetup}} -->

<div id="dialogAddDoc" style="display:none">
<div class="bd">
        <!-- <form id="fprof" method="post" action="#"> commented by art 01/28/2014-->
                <table width="100%" class="segPanel">
                        <tbody>
                                <tr>
                                    <td width="25%" align="right"><b>Physician :</b></td>
                                    <td width="75%">
                                            <select id="doclist" name="doclist" style="width: 250px;" onchange="jsOptionChange(this, this.options[this.selectedIndex].value)">
                                                    <option value="">--Select Doctors--</option>
                                            </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"><b>Role :</b></td>
                                    <td>
                                            <select id="rolearea" name="rolearea" onchange="jsOptionChange(this, this.options[this.selectedIndex].value)">
                                                    <option value="0">-Select Role-</option>
                                            </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right"><b>Case Rate Type:</b></td>
                                    <td>
                                        <select id="rate_type" name="rate_type" >
                                            <option value="0">-Select Type-</option>
                                            <option value="1">First Case</option>
                                            <option value="2">Second Case</option>
                                            <option value="3">First And Second Case</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr style="display:none">
                                    <td align="right"><b>Level :</b></td>
                                    <td>
                                            <select id="role_level" name="role_level" onchange="jsOptionChange(this, this.options[this.selectedIndex].value)">
                                                    <option value="0">-Select Level-</option>
                                            </select>
                                    </td>
                                </tr>
                <tr id="days_row">
                    <td align="right"><b>Days Attended:</b></td>
                    <td><input style="text-align:right" onblur="trimString(this); genChkInteger(this); calcDrCharge();" onFocus="this.select();" id="ndays" name="ndays" value="0"/> (if applicable)</td>
                                </tr>
                                <tr>
                                        <td align="right"><b>Charge :</b></td>
                                        <td><input style="text-align:right" onblur="trimString(this); genChkDecimal(this);" onFocus="this.select();" id="charge" name="charge" value="" />&nbsp;
                                            <span style="vertical-align:top">{{$sSelectOpsForPF}}</span>
                                        </td>
                                </tr>
                                <table style="border-top:solid;border-width:thin" width="100%" class="segPanel">
                                        <tr id="hasAnes" name="hasAnes" style="display:none">
                                            <td width="20%" align="right">
                                                <input type="checkbox" id="Anes" name="Anes" onclick="calcDrCharge()" value=""></td>
                                            <td width="80%">Has Anesthesiologist.</td>
                                        </tr>
                                </table>
                                <!-- Added by jasper -->
                                <div id="btns" style="margin-top:5px">
                                <!--<input type="button" id="btnVerify" name="btnVerify" value="Verify Accreditation Number" onclick="jsVerifyDoctor();">-->
                                <input type="submit" style="display:none" id="btnAdd" name="btnAdd" value="Add" onclick="validateDate();">
                                <!-- <input type="button" id="btnCancel" name="btnCancel" value="Cancel" onclick="jsCloseWindow();"> -->
                                </div>
                                {{$sHiddenInputs}}
                        </tbody>
                </table>
        <!-- </form> commented by art 01/28/2014-->
</div>
</div>

<div id="memcategdialogbox" style="display:none">
<div class="hd" align="left">Specify Membership Category</div>
<div class="bd">
        <form id="mcategdbox" method="post" action="#">
                <table width="100%" class="segPanel">
                        <tbody>
                                <tr>
                                        <td align="center" width="75%">
                                                <select id="category_list" name="category_list" onchange="jsCategoryOptionChange(this, this.options[this.selectedIndex].value, this.options[this.selectedIndex].text)">
                                                        <option value="">-Select Category-</option>
                                                </select>
                                    </td>
                                </tr>
                        </tbody>
                </table>
                {{$sMemCategHiddenInputs}}
        </form>
</div>
</div>

<div class="segPanel" id="dialogAcc" style="display:none">
    <form id="faccbox" method="post" action="#">
        <table width="100%" class="data-grid rounded-borders-bottom">
                <tr>
                    <td>
                        <p id="validationAccomMsgBox" class="validateTips ui-state-error" style="display:none"></p>
                    </td>
                <tr>
                <tr><td>
                        <table width="100%" border="0">
                                <tbody>
                                        <tr>
                                                <td width="12%" align="right"><b>Ward :</b></td>
                                                <td width="45%" align="left" colspan="3">
                                                        <select style="width:258px" id="wardlist" name="wardlist" onchange="jsAccOptionsChange(this, this.options[this.selectedIndex].value)">
                                                                <option value="0">- Select Ward -</option>
                                                        </select>
                                                </td>
                                                <td width="10%" align="right"><b>Room :</b></td>
                                                <td colspan="3" align="left">
                                                        <select style="width:142px" id="roomlist" name="roomlist" onchange="jsAccOptionsChange(this, this.options[this.selectedIndex].value)">
                                                                <option value="0">- Select Room -</option>
                                                        </select>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                        <table width="100%" border="0" class="data-grid rounded-borders-bottom">
                                <tbody>
                                        <tr>
                                                <td width="18%" align="right"><b>Rate/Chrg. :</b></td>
                                                <td width="*">
                                                        <input style="text-align:right" onblur="trimString(this); genChkDecimal(this);" onFocus="this.select();" id="rate" name="rate" size="10" value="" />
                                                </td>
                                                <td width="18%" align="right"><b>Occupied From:</b></td>
                                                <td width="*">
                                                        <input style="text-align:left" id="occupydatefrom" name="occupydatefrom" size="10" value="" />
                                                    <b>To:</b>
                                                        <input style="text-align:left" id="occupydateto" name="occupydateto" size="10" value="" />
                                                </td>
                                        </tr>
                                </tbody>
                        </table>
                </td></tr>
        </table>
        {{$sAccAddHiddenInputs}}
    </form>
</div>

<div class="segPanel" id="dialogAccDelConfirm" style="display:none">
    <p>
        <span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
        Do you really want to remove this accommodation?<br>
        <i>NOTE: Deletion will remove the most recent posted accommodation.</i>
    </p>
    <input type="hidden" id="delAccomType" name="delAccomType" value=""/>
    <input type="hidden" id="room" name="delAccomType" value=""/>
    <input type="hidden" id="ward" name="delAccomType" value=""/>
</div>
<div class="segPanel" id="dialogOR" style="display:none">
    <form id="fopaccbox" method="post" action="#">
            <table width="100%" class="segPanel" class="data-grid rounded-borders-bottom">
                    <tr><td>
                            <table width="100%" border="0" class="data-grid rounded-borders-bottom">
                                    <tbody>
                                            <tr>
                                                    <td width="20%" align="right"><b>O.R. Ward :</b></td>
                                                    <td width="65%" align="left">
                                                            <select style="width:350px" id="opwardlist" name="opwardlist">
                                                                    <option value="0">- Select O.R. Ward -</option>
                                                            </select>
                                                    </td>
                                            </tr>
                                            <tr>
                                                    <td width="20%" align="right"><b>Room :</b></td>
                                                    <td width="65%" align="left">
                                                            <select style="width:350px" id="orlist" name="orlist">
                                                                    <option value="0">- Select Operating Room -</option>
                                                            </select>
                                                    </td>
                                            </tr>
                                            <tr>
                                                    <td width="20%" align="right"><b>Total RVU :</b></td>
                                                    <td width="65%" align="left">
                                                            <input style="text-align:right;" disabled="disabled" id="total_rvu" name="total_rvu" size="30" value="" />&nbsp;
                                                            <span style="vertical-align:top">{{$sSelectOps}}</span>
                                                    </td>
                                            </tr>
                                            <tr>
                                                    <td width="20%" align="right"><b>Multiplier :</b></td>
                                                    <td width="65%" align="left">
                                                            <input style="text-align:right" disabled="disabled" id="multiplier" name="multiplier" size="30" value="" />
                                                    </td>
                                            </tr>
                                            <tr>
                                                    <td width="20%" align="right"><b>Charge :</b></td>
                                                    <td width="65%" align="left">
                                                            <input style="text-align:right" onblur="trimString(this); genChkDecimal(this);" onFocus="this.select();" id="oprm_chrg" name="oprm_chrg" size="30" value="" />
                                                    </td>
                                            </tr>
                                    </tbody>
                            </table>
                    </td></tr>
            </table>
            {{$sOpAccChrgHiddenInputs}}
    </form>
</div>

<div class="segPanel" id="dialogProcedureList" style="display:none;overflow:auto;">
    <table id="procedure-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
        <thead>
            <tr>
                <th width="*" align="left">&nbsp;&nbsp;Name/Description</th>
                <th width="10%" align="left">&nbsp;&nbsp;Code </th>
                <!-- <th width="3%" align="center">Group</span></th> -->
                <th width="10%" align="center">Date</th>
                <th width="8%" align="center">RVU</th>
                <th width="10%" align="center">Multiplier</th>
                <th width="12%" align="center">Charge</th>
                <th width="5%" align="center">Selected?</th>
            </tr>
        </thead>
        <tbody id="procedure-list-body">
        </tbody>
    </table>
    <img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
    <div id='nrvu'>
    </div>
</div>


<!-- Added by Jarel ____ Dialog for OUTSIDE MEDS AND XLO -->
<div id="dialogOutMedsXLO" style="display:none">
    <div class="bd">
        <table width="100%" class="segPanel">
            <tbody>
                <tr>
                    <td width="25%" align="left"><b>Total Outside MEDICINE :</b></td>
                   
                </tr>
                <tr>
                    <td width="75%">
                        <input style="text-align:left"  id="meds_total" name="meds_total" value="0"/>
                    </td>
                </tr>
                <tr>
                    <td align="left"><b>Total Outside XLO :</b></td>
                </tr>
                <tr>
                    <td>
                        <input style="text-align:left"  id="xlo_total" name="xlo_total" value="0"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="coverage-dialog" style="display:none">
    <table class="segList" width="100%" border="1" cellspacing="0" cellpadding="0">
        <thead id="coverage-header">
        </thead>
        <tbody id="doc-coverage">
        </tbody>
        <tfoot id="coverage-footer">
        </tfoot>
    </table>
    <br/>
    <table width="100%" class="segList">
        <thead>
                <tr>
                <th width="30%" >Role</th>
                <th width="20%" nowrap="nowrap">First Case</th>
                <th width="20%" nowrap="nowrap">Second Case</th>
                <th width="30%" nowrap="nowrap">Total</th>
                </tr>
        </thead>
        <tbody>
                <tr>
                <td align="left"><b>General Practitioner / Specialist</b></td>
                <td align="left" id="D1_first"></td>
                <td align="left"  id="D1_second"></td>
                <td width="30%"  align="left" nowrap="nowrap" id="D1_total"></td>
                </tr>
                <tr>
                <td align="left"><b>Surgeon</b></td>
                <td align="left" id="D3_first"></td>
                <td align="left" id="D3_second"></td>
                <td width="30%" align="left" nowrap="nowrap" id="D3_total"></td>
                </tr>
                <tr>
                <td align="left"><b>Anesthesiologist</b></td>
                <td align="left" id="D4_first"></td>
                <td align="left" id="D4_second"></td>
                <td width="30%" align="left" nowrap="nowrap" id="D4_total"></td>
                </tr>
            <tfoot>
                <tr>
                    <th width="40%">MAX PHIC PF </th>
                    <th width="30%" align="left" nowrap="nowrap" id="first_total"></th>
                    <th width="30%" align="left" nowrap="nowrap" id="second_total"></th>
                    <th align="left"><span style="text-align:left;font:bold 15px Arial"  id="phic-max-PF" name="phic-max-PF" ></span></th>
                </tr>
                    
            </tfoot>
            </tbody>
        </table>
    </div>

<div id="coverage-dialog-hci" style="display:none">
    <table class="segList" width="100%" border="1" cellspacing="0" cellpadding="0">
        <thead id="coverage-header-hci">
        </thead>
        <tbody id="hci-coverage">
        </tbody>
        <tfoot id="coverage-footer-hci">
        </tfoot>
    </table>
    <table> 
        <tr>
            <td width="50%"><b>MAX PHIC HCI : </b></td>
            <td>
                <span style="text-align:left;font:bold 15px Arial"  id="phic-max-HCI" name="phic-max-HCI" >0.00</span>
            </td>
        </tr>
    </table>
</div>

<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}
{{$sSaveinputs}}
