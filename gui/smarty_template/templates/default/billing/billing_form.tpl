{{* form.tpl  Form template for billing module : Bong S. Trazo *}}
<!--added by jasper 07/08/2013-->
<script type="text/javascript" src="jquery/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.8.2.custom.min.js"></script>
<!--added by jasper 07/08/2013-->
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />
<div id="mainTablediv" align="center">
        <table width="96%" cellpadding="0" cellspacing="2">
                <tbody id="buttons_bar"><tr>
                    {{$sBillingButton5}}
                    <!-- added by pol 10/05/2013 -->
                    {{$sPreviousPackage}}
                    <!-- end by pol 10/05/2013 -->
                    {{$sBillingButton0}}
                    {{$sBillingButton6}}
                    {{$sMGHCheckBox}}
<!--            <td width="*"><span style="font-size:18px; font-family:Verdana; font-style:bold">Classification: </span></td>
                        <td width={{$row_width}}><span style="font-size:18px; font-family:Verdana; font: bold" id="sclassification"></span></td>  -->
                        <td width="*">&nbsp;</td>
                        <td width="8" valign="bottom" align="center">{{$sBillingButton1}}</td>
                        <td width="8" valign="bottom" align="center">{{$sBillingButton2}}</td>
                        <td width="8" valign="bottom" align="center">{{$sBillingButton3}}</td>
                        <td width="8" valign="bottom" align="center">{{$sBillingButton4}}</td>
                        <td width="14%" align="center" valign="middle"><input type="checkbox" name="IsDetailed" id="IsDetailed" style="vertical-align:middle">Detailed?</td>
                </tr></tbody>
        </table>
        <table width="96%" cellpadding="2" cellspacing="2" id="mainTable" style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
                <thead>
                        <tr><th id="billcol_01" colspan="2" rowspan="2" align="left" class="jedPanelHeader" style="border-right:none">BILLING STATEMENT&nbsp;&nbsp;{{$sBillStatus}}</th>
                                <th class="jedPanelHeader" style="border-left:none" align="right"><div id="categ_col" style="display:none">{{$sMembershipCategory}}&nbsp;MEMBERSHIP CATEGORY:&nbsp;&nbsp;&nbsp;<span id="mcategdesc" name="mcategdesc"></span></div></th>
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
                                                        <td width="25%" align="left" valign="middle">{{$sPatientEnc}}</td>
                                                        <td colspan="2" width="13%" align="right"><strong>CLASSIFICATION:</strong></td>
                                                        <td width="10%" align="left"><span style="color:#0000FF" id="sclassification">NONE</span></td>
                                                </tr>
                                                <tr class="jedPanel">
                                                        <td align="right" valign="middle"><strong>Name:</strong></td>
                                                        <td colspan="2" width="50px" valign="middle">
                                                                {{$sPatientName}}<span style="vertical-align:bottom">{{$sSelectPatient}}</span></td>
                                                        <td width="8%" align="right" valign="middle"><strong>Date:</strong></td>
                                                        <td colspan="2" width="20%" valign="middle" align="left">{{$sDate}}<span style="vertical-align:top">{{$sCalendarIcon}}</span></td>
                                                        <td width="10%" align="right" valign="middle"><span><strong>Case Type:</strong></span></td>
                                                        <td width="15%" align="left" valign="middle">{{$sConfineType}}</td>

<!--                            <td align="left">{{$sDate}}<strong style="font-size:10px">mm/dd/yyyy</strong></td>  -->
                                                </tr>
                                                <tr class="jedPanel">
<!--                            <td width="*" align="right" valign="top"><strong>Address:</strong></td>
                                                        <td width="39%">{{$sPatientAddress}}</td>
                                                        <td align="right" colspan="2" valign="top"><strong>Case Date:</strong></td>
                                                        <td valign="top" colspan="4">{{$sAdmissionDate}} </td>  -->
                                                        <td width="*" align="right" valign="top"><strong>Address:</strong></td>
                                                        <td rowspan="2" width="50px">{{$sPatientAddress}}</td>
                                                        <td align="right" colspan="2" valign="top"><strong>Case Date:</strong></td>
                                                        <td valign="top" colspan="2">{{$sAdmissionDate}}</td>
                                                        <td id="confine_label" width="10%" align="right" valign="top" style="display:none"><strong>Confinement:</strong></td>
                                                        <td id="confine_cbobox" width="15%" align="left" valign="top" style="display:none">{{$sCaseType}}</td>
                                                </tr>
                                                <tr class="jedPanel">
                                                        <td colspan="2" width="*" align="right" valign="top">&nbsp;</td>
                                                        <td id="admit_label" style="display:none" align="right" colspan="2" valign="middle"><strong>Adm.:</strong></td>
                                                        <td id="admit_date" style="display:none" valign="top">{{$sAdmitDate}}</td>
                                                        <td id="lastbill_label" style="display:none" colspan="2" align="right" valign="middle" width="15%"><strong>Last Bill:</strong></td>
                                                        <td id="lastbill_actualdate" style="display:none" valign="top" width="10%">{{$sLastBillDate}}</td>
                                                        <!--added by jasper 04/25/2013 -->
                                                        <!--eddited by pol 05/21/2013 -->


                                                </tr>
                                    <!--added by pol-->
                                                <tr class="jedPanel">
                                                    <td width="5%" align="right"><strong>Insurance No:</strong></td>
                                                    <td width="50px" align="left">{{$sPhic}}</td>
                                                    <td></td>
                                                    <td></td>
                                                        <td colspan="2" width="50px" valign="middle">

                                                        <div id="ShowMedicoLegal" style="display:none">
                             <!--added by pol-->        <input class="segInput" id="ShowMedicoCases" name="ShowMedicoCases" type="text" size="16" value="" style="font:bold 12px Arial; float;left;" readOnly >');
                                                        </div>
                             <!--added by pol-->        <td id="medicolegal" style="display:none; color:red"  onmouseover="return overlib($('ShowMedicoCases').value, LEFT);"
                                                        onmouseout="return nd();" colspan="2" align="left" valign="middle" width="10%"><strong>Medico Legal</strong>
                                                        </td>
                                                </tr>
                            <!--end by pol-->
                                                <!-- Added by Jarel 05/16/2013 -->
                                                <tr class="jedPanel">
                                                        <td colspan="2" width="20%" valign="middle" align="left"><input type="checkbox" name="isdied" id="isdied" style="vertical-align:middle" onclick="toggleDeathDate()"><strong>Check if Patient is already Dead</strong></td>
                                                        <td width="20px" valign="middle" align="left"></td>
                                                        <td id="label_deathdate" width="20px" align="right" valign="middle" style="display:none"><strong>Death Date:</strong></td>
                                                        <td id="input_deathdate" colspan="2" width="20%" valign="middle" align="left" style="display:none">{{$sDDate}}<span style="vertical-align:top">{{$sDCalendarIcon}}</span></td>
                                                </tr>
                                                <!--  -->
                                    </table>
                                </td>
                        </tr>
                </tbody>
    </table>
    <!-- Billing Details -->
    <div id="bBody" style="width:100%; display:none" align="center">
            <!-- billing Header Details -->
        <div id="accommodation_div" style="display:none" align="center">
                <table width="96%" class="segPanelHeader" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #cccccc; margin-top:5px; color:black">
                        <!--<thead>
                                <tr id="id-billing-details" align="left">
                                        <th nowrap="nowrap" style="font-weight:bold; font-size:14px">Details</th>
                                </tr>
                        </thead>-->
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
                                                                        <th align="left" width="*" style="font-weight:bold; font-size:15px;">Accommodation&nbsp;{{$sAddAccommodation}}</th>
                                                                        <th width="15%"><span style="font-size:12px;">No. of Days</span></th>
                                                                        <th width="15%"><span style="font-size:12px;">Rate</span></th>
                                                                        <th width="15%"><span style="font-size:12px;">Total</span>
                                                                </th></tr>
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
                                                                                <span>Discount</span><br />
                                                                                <span>[Health Insurance] Total Coverage</span><br />
                                                                                <span>Excess</span>
                                                                        </td>
                                                                        <td align="right">
                                                                                <span id="accAP">0.00</span><br />
                                                                                <span id="accDiscount">0.00</span><br />
                                                                                <span id="accHC">0.00</span><br />
                                                                                <span id="accEX">0.00</span>
                                                                        </td>
                                                                </tr>
                                                        </tbody>
                                                </table>
                                        </td>
                                </tr><!-- end of Accommodation -->
                                <tr><td></td>
                                </tr>
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
                                                                <th align="left" width="*" style="font-weight:bold; font-size:15px;">X-Ray, Lab, & Others&nbsp;<span>{{$sAddMiscService}}</span>&nbsp;</th>
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
                                                                        <span>Discount</span><br />
                                                                        <span><div id="hstooltip" style="display:none">Edit coverage distribution in x-ray, lab and others.</div><a id="xlocvrg" style="cursor:pointer" onclick="openCoverages('O');" onmouseover="return overlib($('hstooltip').innerHTML, LEFT);" onmouseout="return nd();">[Health Insurance] Total Coverage</a></span><br />
                                                                        <span>Excess</span>
                                                                </td>
                                                                <td align="right" width="15%">
                                                                        <span id="hsAP">0.00</span><br />
                                                                        <span id="hsDiscount">0.00</span><br />
                                                                        <span id="hsHC">0.00</span><br />
                                                                        <span id="hsEX">0.00</span>
                                                                </td>
                                                        </tr>
                                                </tbody>
                                    </table>
                                </td>
                        </tr>
                </tbody>
</table>
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
                                                                        <span>Drugs & Medicines Sub-Total</span><br />
                                                                        <span>Discount</span><br />
                                                                        <span><div id="medtooltip" style="display:none">Edit coverage distribution in drugs and medicines.</div><a id="medcvrg" style="cursor:pointer" onclick="openCoverages('M');" onmouseover="return overlib($('medtooltip').innerHTML, LEFT);" onmouseout="return nd();">[Health Insurance] Total Coverage</a></span><br />
                                                                        <span>Excess</span>
                                                                </td>
                                                                <td align="right">
                                                                        <span id="medAP">0.00</span><br />
                                                                        <span id="medDiscount">0.00</span><br />
                                                                        <span id="medHC">0.00</span><br />
                                                                        <span id="medEX">0.00</span>
                                                                </td>
                                                        </tr>
                                                </tbody>
                                    </table>
                                </td>
                        </tr>
        </tbody>
</table>
<!-- Supplies -->
<!-- <table width="96%" class="segPanelHeader" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #cccccc; margin-top:5px; color:black">
        <tbody>
                        <tr>
                                <td>
                                        <table id="supListDetails" width="100%" border="1" cellpadding="0" cellspacing="0" class="segList">
                                                <thead>
                                                        <tr>
                                                                <th align="left" style="font-weight:bold; font-size:15px;">Supplies</th>
                                                                <th width="15%">Quantity</th>
                                                                <th width="15%">Item Price</th>
                                                                <th width="15%">Total</th>
                                                        </tr>
                                                </thead>
                                                <tbody id="body_supListDetails">
                                                </tbody>
                                                <tbody id="footer_supListDetails">
                                                        <tr>
                                                                <td align="right" colspan="3">
                                                                        <span>Supplies Sub-Total</span><br />
                                                                        <span>Discount</span><br />
                                                                        <span>[Health Insurance] Total Coverage</span><br />
                                                                        <span>Excess</span>
                                                                </td>
                                                                <td align="right" width="15%">
                                                                        <span id="supAP">0.00</span><br />
                                                                        <span id="supDiscount">0.00</span><br />
                                                                        <span id="supHC">0.00</span><br />
                                                                        <span id="supEX">0.00</span>
                                                                </td>
                                                        </tr>
                                                </tbody>
                                    </table>
                                </td>
                        </tr>
        </tbody>
</table> -->
<!-- Operation/Procedures -->
<div id="op_div" align="center" style="display:none">
<table width="96%" class="segPanelHeader" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #cccccc; margin-top:5px; color:black">
        <tbody>
                        <tr>
                                <td>
                                        <table id="opsListDetails" width="100%" border="1" cellpadding="0" cellspacing="0" class="segList">
                                                <thead class="togglehdr">
                                                        <tr>
                                                                <th class="toggleth" width="3%"><div class="arrow"></div></th>
                                                                <th width="*" align="left" style="font-weight:bold; font-size:15px;">Operating / Del. Room&nbsp;&nbsp;&nbsp;
                                                                                <span>{{$sAddMiscOps}}</span>&nbsp;{{$sAddOPAccommodation}}
                                                                                &nbsp;
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
                                                                        <span>Operating / Delivery Room Sub-Total</span><br />
                                                                        <span>Discount</span><br />
                                                                        <span>[Health Insurance] Total Coverage</span><br />
                                                                        <span>Excess</span>
                                                                </td>
                                                                <td align="right" width="15%">
                                                                        <span id="opsAP">0.00</span><br />
                                                                        <span id="opsDiscount">0.00</span><br />
                                                                        <span id="opsHC">0.00</span><br />
                                                                        <span id="opsEX">0.00</span>
                                                                </td>
                                                        </tr>
                                                </tbody>
                                    </table>
                                </td>
                        </tr>
        </tbody>
</table>
</div>
<!-- Doctors' professional Fees -->
<div id="pf_div" align="center" style="display:none">
<table width="96%" class="segPanelHeader" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #cccccc; margin-top:5px; color:black">
        <tbody>
                        <tr>
                                <td>
                                        <table id="docRoleArea" width="100%" border="1" cellpadding="0" cellspacing="0" class="segList">
                                                <thead class="togglehdr">
                                                        <tr>
                                                                <th colspan="4" align="left" style="font-weight:bold; font-size:15px;">Doctors' Fees
                                                                            {{$sAddDoctorsButton}}
                                                                            &nbsp;
                                                                </th>
                                                                <!--<th width="15%">Item Price</th>
                                                                <th width="15%">Total</th>-->
                                                        </tr>
                                                        <tr>
                                                                <th class="toggleth" width="3%"><div class="arrow"></div></th>
                                                                <th width="*">Description</th>
<!--                                <th width="15%">&nbsp;</th>  -->
                                                                <th width="15%">Total Charge</th>
                                                                <th width="15%">Coverage</th>
                                                        </tr>
                                                </thead>
                                                <tbody class="toggle" id="body_docRoleArea">
                                                        <!-- prof area & doctors list -->
                                                </tbody>
                                                <tbody class="billfooter" id="footer_docRoleArea">
                                                        <tr>
                                                                <td class="billftr1" align="left">
                                                                        <span id="pfProgStatus" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif">Please wait ... computing Prof. Fees.</span><br />
                                                                        <span id="pfProgBar" style="display:none; float:left">{{$sProgBar}}</span><br />
                                                                        <span>&nbsp;</span>
                                                                </td>
                                                                <td class="billftr2" align="right" colspan="2">
                                                                        <span>Doctors' Fees Sub-Total</span><br />
                                                                        <span>Discount</span><br />
                                                                        <span>[Health Insurance] Total Coverage</span><br />
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
                        <!--<tr class="segPanelHeader">
                                <td>&nbsp;</td>
                        </tr>                -->
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
                                                                <th width="*" align="left" style="font-weight:bold; font-size:15px;">Miscellaneous Charges&nbsp;&nbsp;&nbsp;
                                                                                <span>{{$sAddMiscChrg}}</span>
                                                                                &nbsp;
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
                                                                        <span>Miscellaneous Sub-Total</span><br />
                                                                        <span>Discount</span><br />
                                                                        <span>[Health Insurance] Total Coverage</span><br />
                                                                        <span>Excess</span>
                                                                </td>
                                                                <td align="right">
                                                                        <span id="mscAP">0.00</span><br />
                                                                        <span id="mscDiscount">0.00</span><br />
                                                                        <span id="mscHC">0.00</span><br />
                                                                        <span id="mscEX">0.00</span>
                                                                </td>
                                                        </tr>
                                                </tbody>
                                    </table>
                                </td>
                        </tr>
        </tbody>
</table>
<!-- Previous payment -->
<table width="96%" class="segPanelHeader" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; border:1px solid #cccccc; margin-top:5px; color:black">
        <tbody>
                <tr>
            <TD width="100%">
                <table width="100%" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <thead>
                                        <tr>
                            <th width="25%" colspan="3" align="left" style="font-weight:bold; font-size:15px;">
                                <input type="checkbox" name="is_coveredbypkg" id="is_coveredbypkg" value="0" onClick="togglePkgControls('1');">&nbsp;<span style="vertical-align:top">Covered by Package?</span>&nbsp;/
                                <input type="checkbox" name="is_cvrgadjusted" id="is_cvrgadjusted" value="0" onClick="toggleCvrgAdjust('1');">&nbsp;<span id="cvrgadjusted_label" style="vertical-align:top">Adjusted Coverage?</span>
                            </th>
                                                        <th colspan="2" align="left" style="font-weight:bold; font-size:15px;">Previous Payment (Deposit)</th>
                                                </tr>
                                        </thead>
                                        <tr>
                        <td id="td01" width="25%" rowspan="5" style="border-right:none;">
                            <span id="pkg_label">Select Package:</span><br>
                            <span>&nbsp;</span><br>
                            <span id="cvg_label"><div id="pkgtooltip" style="display:none">Edit distribution of package coverage.</div><a style="cursor:pointer" onclick="openPkgCoverage();" onmouseover="return overlib($('pkgtooltip').innerHTML, LEFT);" onmouseout="return nd();">Coverage of Package:</a></span><br>
                            <span>&nbsp;</span>
                        </td>
                        <td id="td02" width="*" rowspan="5" align="right" style="border-right:none; border-left:none;">
                            <span id="pkgcbo">{{$sPkgCbo}}</span><br>
                            <span>&nbsp;</span><br>
                            <span id="pkgamnt">0.00</span><br>
                            <span>&nbsp;</span>
                        </td>
                        <td id="td03" width="1%" rowspan="5" style="border-left:none; border-right-style:solid; border-right-width:thin; border-right-color:#436499;">
                            <span>&nbsp;</span><br>
                            <span>&nbsp;</span><br>
                            <span>&nbsp;</span><br>
                            <span>&nbsp;</span>
                        </td>
<!--                        <td width="25%">Deposit</td>-->
                        <td width="25%" id="deposit_label"></td>
                        <td width="25%" align="right" id="bdeposit">0.00</td>
                                        </tr>
                    <tr id="classification_discount_row1">
                        <td class="tdcell" colspan="2" align="left" style="font-weight:bold; font-size:15px;">Classification Discount</td>
                </tr>
                    <tr id="classification_discount_row2">
                        <td width="25%">Total Discount</td>
                                                <td align="right" id="bdiscount">0.00</td>
                                        </tr>
                <tr>
                        <td class="tdcell" colspan="2" align="left" style="font-weight:bold; font-size:15px;"><span>DUE & PAYABLE</span>{{$sDiscountDetails}}</td>
                                                </tr>
                                        <!--ADDED BY JASPER 04/01/2013 -->
                                        <tr id="prevbill" style="display:none">
                                                <td width="25%"><span id="lastProgBar2" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif"></span><span id="prevbill_label">PREVIOUS BILL AMOUNT</span></td>
                                                <td align="right" id="prevbillamt">0.00</td>
                                        </tr>
                                        <tr id="nobalance" style="display:none">
                                                <td width="25%"><span id="lastProgBar1" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif"></span><span id="sponsored_label">SPONSORED - No Balance Billing</span></td>
                                                <td align="right" id="sponsored_amount">0.00</td>
                                        </tr>
                                        <tr id="poc" style="display:none">
                                                <td width="25%"><span id="lastProgBar1" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif"></span><span id="poc_label">HOSPITAL SPONSORED MEMBER</span></td>
                                                <td align="right" id="poc_amount">0.00</td>
                                        </tr>
                                        <tr id="infirmary" style="display:none">
                                                <td width="25%"><span id="lastProgBar3" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif"></span><span id="infirmary_label">Infirmary Discount</span></td>
                                                <td align="right" id="infirmary_amount">0.00</td>
                                        </tr>
                                        <!--ADDED BY JASPER 04/01/2013 -->
                                        <tr>
                                                <td width="25%"><span id="lastProgBar" style="display:none; font:bold 12px Arial, Helvetica, Sans-Serif">{{$sProgBar}}&nbsp;Please wait ... computing amount due.</span><span id="amntlabel">AMOUNT</span></td>
                                                <td align="right" id="netbill">00.00</td>
                                        </tr>
                                </table>
            </td>
                </tr>
        </tbody>
</table>

<!--<table width="90%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
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
-->

    </div>
</div>

{{$jsCalendarSetup}}
{{$jsDCalendarSetup}}

<div id="profDialogbox" style="display:none">
<div class="hd" align="left">Add Doctor</div>
<div class="bd">
        <form id="fprof" method="post" action="#">
                <table width="100%" class="segPanel">
                        <tbody>
                                <tr>
                                        <td width="25%" align="right"><b>Physician :</b></td>
                                        <td width="75%">
                                                <select id="doclist" name="doclist" style="width: 250px;" onchange="jsOptionChange(this, this.options[this.selectedIndex].value)">
                                                        <option value="">-Select Doctors-</option>
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
                                        <td align="right"><b>Level :</b></td>
                                        <td>
                                                <select id="role_level" name="role_level" onchange="jsOptionChange(this, this.options[this.selectedIndex].value)">
                                                        <option value="0">-Select Level-</option>
                                                </select>
                                        </td>
                                </tr>
                                <!--Added by jasper 06/17/2013
                                <tr>
                                    <td width="18%" align="right"><b>From Date:</b></td>
                                    <td width="*">
                                            <input style="text-align:left" id="fromdate" name="fromdate" size="10" value="" onchange="assignDaysAttended();" />
                                            <script>
                                                jQuery(function() {
                                                    jQuery( "#fromdate" ).datepicker({ dateFormat: 'mm/dd/yy',
                                                                                         changeMonth: true,
                                                                                         changeYear: true
                                                                                       });
                                                });
                                            </script>
                                            &nbsp;&nbsp;&nbsp;&nbsp;<b>To Date:</b>
                                            <input style="text-align:left" id="todate" name="todate" size="10" value="" onchange="assignDaysAttended();" />
                                            <script>
                                                jQuery(function() {
                                                    jQuery( "#todate" ).datepicker({ dateFormat: 'mm/dd/yy',
                                                                                         changeMonth: true,
                                                                                         changeYear: true
                                                                                       });
                                                });
                                            </script>
                                    </td>
                                </tr>-->
                                <!--Added by jasper 06/17/2013-->

                <tr id="days_row">
                    <td align="right"><b>Days Attended:</b></td>
                    <td><input style="text-align:right" onblur="trimString(this); genChkInteger(this); assignDrCharge();" onFocus="this.select();" id="ndays" name="ndays" value=""/> (if applicable)</td>
                                </tr>
                                <tr>
                                        <td align="right"><b>Charge :</b></td>
                                        <td><input style="text-align:right" onblur="trimString(this); genChkDecimal(this);" onFocus="this.select();" id="charge" name="charge" value="" />&nbsp;
                                            <span style="vertical-align:top">{{$sSelectOpsForPF}}</span>
                                        </td>
                                </tr>
                                <table style="border-top:solid;border-width:thin" width="100%" class="segPanel">
                                        <tr>
                                                <td width="20%" align="right">
                                                    <input onclick="setExcludedFlag();" type="checkbox" id="is_excluded" name="is_excluded" value=""></td>
                                                <td width="80%">Check the box if this PF will not be charged<br>to the availed health insurances.</td>
                                        </tr>
                                </table>
                                <!-- Added by jasper -->
                                <div id="btns" style="margin-top:5px">
                                <!--<input type="button" id="btnVerify" name="btnVerify" value="Verify Accreditation Number" onclick="jsVerifyDoctor();">-->
                                <input type="submit" id="btnAdd" name="btnAdd" value="Add" onclick="validateDate();">
                                <input type="button" id="btnCancel" name="btnCancel" value="Cancel" onclick="jsCloseWindow();">
                                </div>
                                {{$sHiddenInputs}}
                        </tbody>
                </table>
        </form>
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

<div id="accAddDialogBox" style="display:none">
<div class="hd" align="left">More Accommodation Charges</div>
<div class="bd">
        <form id="faccbox" method="post" action="#">
                <table width="100%" class="segPanel">
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
                                <table width="100%" border="0">
                                        <tbody>
                                                <tr>
                                                        <td width="12%" align="right"><b>Day(s) :</b></td>
                                                        <td width="*">
                                                                <input style="text-align:right" onblur="trimString(this); genChkDecimal(this);" onFocus="this.select();" id="days_stay" name="days_stay" size="8" value="" />
                                                        </td>
                                                        <td width="18%" align="right"><b>Excess(hrs) :</b></td>
                                                        <td width="*">
                                                                <input style="text-align:right" onblur="trimString(this); genChkDecimal(this);" onFocus="this.select();" id="hrs_stay" name="hrs_stay" size="8" value="" />
                                                        </td>
                                                        <td width="18%" align="right"><b>Rate/Chrg. :</b></td>
                                                        <td width="*">
                                                                <input style="text-align:right" onblur="trimString(this); genChkDecimal(this);" onFocus="this.select();" id="rate" name="rate" size="10" value="" />
                                                        </td>
                                                        <td width="18%" align="right"><b>Occupied:</b></td>
                                                        <td width="*">
                                                                <input style="text-align:left" id="occupydate" name="occupydate" size="8" value="" />
                                                                <script>
                                                                    jQuery(function() {
                                                                        jQuery( "#occupydate" ).datepicker({ dateFormat: 'mm/dd/yy',
                                                                                                             changeMonth: true,
                                                                                                             changeYear: true
                                                                                                           });
                                                                    });
                                                                </script>
                                                        </td>
                                                </tr>
                                        </tbody>
                                </table>
                        </td></tr>
                </table>
                {{$sAccAddHiddenInputs}}
        </form>
</div>
</div>

<div id="opAccChrgBox" style="display:none">
<div class="hd" align="left">Operating Room Accommodation Charges</div>
<div class="bd">
        <form id="fopaccbox" method="post" action="#">
                <table width="100%" class="segPanel">
                        <tr><td>
                                <table width="100%" border="0">
                                        <tbody>
                                                <tr>
                                                        <td width="20%" align="right"><b>O.R. Ward :</b></td>
                                                        <td width="65%" align="left">
                                                                <select style="width:350px" id="opwardlist" name="opwardlist" onchange="jsOpAccChrgOptionsChange(this, this.options[this.selectedIndex].value)">
                                                                        <option value="0">- Select O.R. Ward -</option>
                                                                </select>
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td width="20%" align="right"><b>Room :</b></td>
                                                        <td width="65%" align="left">
                                                                <select style="width:350px" id="orlist" name="orlist" onchange="jsOpAccChrgOptionsChange(this, this.options[this.selectedIndex].value)">
                                                                        <option value="0">- Select Operating Room -</option>
                                                                </select>
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td width="20%" align="right"><b>Total RVU :</b></td>
                                                        <td width="65%" align="left">
                                                                <input style="text-align:right;" disabled="disabled" id="total_rvu" name="total_rvu" size="30" value="" />&nbsp;<span style="vertical-align:top">{{$sSelectOps}}</span></td>
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
</div>

<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}
