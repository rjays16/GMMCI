<div id="loadingBox" style="display:none;" align="center">
    <strong>Please wait ...</strong><br>
    <img id="imgLoading" src="../../images/ajax_bar.gif" />
</div>
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />
<div id="mainTablediv" align="center">
        <table width="50%" cellpadding="2" cellspacing="2" id="mainTable" style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
            <thead>
                <tr>
                    <th id="billcol_01" colspan="2" rowspan="2" align="left" class="jedPanelHeader" style="border-right:none">PROMISORY NOTE&nbsp;&nbsp;</th>
                    <th id="billcol_02" colspan="2" rowspan="2" align="center" class="jedPanelHeader" style="border-right:none;border-left:none"><span id="remaindays" style="display:none"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="coverdays" style="display:none"></span><span id="savethis" style="display:none"></span></th>
                   <th class="jedPanelHeader" style="border-left:none" align="right"><div id="categ_col" style="display:none">{{$sMembershipCategory}}&nbsp;MEMBERSHIP CATEGORY:&nbsp;&nbsp;&nbsp;<span id="mcategdesc" name="mcategdesc"></span></div></th>
                </tr>
            </thead>
        </table>
        <table width="50%" cellpadding="2" cellspacing="2" id="mainTable2" style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
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
                            </tr>
                            <tr class="jedPanel">
                                    <td align="right" valign="middle"><strong>Name:</strong></td>
                                    <td colspan="2" width="50px" valign="middle">
                                        {{$sPatientName}}
                                        <span style="vertical-align:bottom">{{$sSelectPatient}}</span>
                                    </td>
                                    <td width="10%" align="right" valign="middle"><strong>Case Date:</strong></td>
                                    <td colspan="2" width="20%" valign="middle" align="left">{{$sAdmissionDate}}</td>
                            </tr>
                            <tr class="jedPanel">
                                    <td width="*" align="right" valign="top"><strong>Address:</strong></td>
                                    <td rowspan="2" width="50px">{{$sPatientAddress}}</td>
                                    <td width="50px" align="left">&nbsp;</td>
                                    <td width="20%" align="right" valign="middle"><strong>Confinement Type:</strong></td>
                                    <td colspan="2" width="20%" valign="middle" align="left">{{$sConfineType}}</td>
                            </tr>

                            <tr class="jedPanel">
                                <td width="*" align="right">&nbsp;</td>
                                <td width="50px" align="left">&nbsp;</td>
                                <td width="20%" align="right" valign="middle"><strong>Billing Date:</strong></td>
                                <td colspan="2" width="20%" valign="middle" align="left">{{$sDate}}</td>
                            </tr>

                            <tr class="jedPanel">
                                <td width="*" align="right"><strong>Age :</strong></td>
                                <td width="50px" align="left">{{$sAge}}</td>
                                <td align="right" width="20%" colspan="2" valign="top"><strong>Total Bill:</strong></td>
                                <td valign="top" colspan="2">{{$sTotalBill}}</td>
                            </tr>
                         </table>
                    </td>
                </tr>
            </tbody>
    </table>
     <table width="50%" cellpadding="2" cellspacing="2" id="mainTable" style="border-collapse:collapse; border:1px solid #a6b4c9; color:black; margin-top: 10px;">
          <tbody>
                <tr>
                   <td colspan="2" rowspan="5" align="left" valign="top" class="jedPanel">
                        <table width="100%" border="0" cellpadding="2" cellspacing="0" style="font-size:11px">
                            <tr class="jedPanel">
                                <td width="200px" align="left">&nbsp;</td>
                                <td width="10%" align="right"><strong>Due Date: </strong></td>
                                <td>{{$sDueDate}}{{$sCalendarIcon}}</td>
                            </tr>
                            <tr class="jedPanel">
                                <td width="200px" align="left">&nbsp;</td>
                                <td width="20%" align="right"><strong>Amount Payable: </strong></td>
                                <td>{{$sAmount}}</td>
                            </tr>
                            <tr class="jedPanel">
                                <td width="200px" align="left">&nbsp;</td>
                                <td width="20%" align="right"><strong>Name of Guarantor: </strong></td>
                                <td>{{$sNameGuarantor}}</td>
                            </tr>
                            <tr class="jedPanel">
                                <td width="200px" align="left">&nbsp;</td>
                                <td width="20%" align="right"><strong>Address: </strong></td>
                                <td>{{$sAddressGuarantor}}</td>
                            </tr>
                             <tr class="jedPanel">
                                <td width="200px" align="left">&nbsp;</td>
                                <td width="20%" align="right"><strong>Relationship to Patient: </strong></td>
                                <td>{{$sReltoPatient}}</td>
                            </tr>
                             <tr class="jedPanel">
                                <td width="200px" align="left">&nbsp;</td>
                                <td width="20%" align="right"><strong>Remarks: </strong></td>
                                <td>{{$sRemarks}}</td>
                            </tr>
                            <tr class="jedPanel">
                               <td width="200px" align="left">&nbsp;</td>
                                <td width="20%" align="right">&nbsp;</td>
                                <td align="left">{{$sBtnSave}}{{$sBtnPrint}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
          </tbody>
        </table>
</div>

<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}
{{$sSaveinputs}}
