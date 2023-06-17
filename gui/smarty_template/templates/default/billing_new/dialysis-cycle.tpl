<style>
.jedButton{
    cursor:pointer;
}

</style>
<div id="loadingBox" style="display:none;" align="center">
    <strong>Please wait ...</strong><br>
    <img id="imgLoading" src="../../images/ajax_bar.gif" />
</div>
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />
<div id="mainTablediv" align="center">
        <table width="50%" cellpadding="2" cellspacing="2" id="mainTable" style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
            <thead>
                <tr>
                    <th id="billcol_01" colspan="2" rowspan="2" align="left" class="jedPanelHeader" style="border-right:none">PATIENT INFORMATION&nbsp;&nbsp;</th>
                    <th id="billcol_02" colspan="2" rowspan="2" align="center" class="jedPanelHeader" style="border-right:none;border-left:none"><span id="remaindays" style="display:none"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="coverdays" style="display:none"></span><span id="savethis" style="display:none"></span></th>
                   <th class="jedPanelHeader" style="border-left:none" align="right"><div id="categ_col" style="display:none">{{$sMembershipCategory}}&nbsp;MEMBERSHIP CATEGORY:&nbsp;&nbsp;&nbsp;<span id="mcategdesc" name="mcategdesc"></span></div></th>
                </tr> 
            </thead>
        </table>
        <table width="50%" cellpadding="2" cellspacing="2" id="mainTable2" style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
            {{$sFormStart}}
            <tbody>
                <!-- Basic information -->
                <tr>
                    <td colspan="2" rowspan="5" align="left" valign="top" class="jedPanel">
                        <table width="100%" border="0" cellpadding="2" cellspacing="0" style="font-size:11px">
                            <tr class="jedPanel">
                                    <td width="5%" align="right"><strong>HRN:</strong></td>
                                    <td width="50px" align="left">{{$sPid}}</td> 
                                    <td width="10px" align="left" valign="middle">{{$sSelectEnc}}</td>
                                    <td colspan="2" width="14%" align="right"><strong>Ref No:</strong></td>
                                    <td width="25%" align="left" valign="middle">{{$sRefNo}}</td>
                                    <td valign="middle" align="left">  
                                    </td>
                            </tr>
                            <tr class="jedPanel">
                                    <td align="right" valign="middle"><strong>Name:</strong></td>
                                    <td colspan="2" width="50px" valign="middle">
                                        {{$sPatientName}}
                                        <span style="vertical-align:bottom">{{$sSelectPatient}}</span>
                                    </td>
                                    <td width="10px" align="left" valign="middle"></td>
                                    <td width="10%" align="right" valign="middle"><strong>Age:</strong></td>
                                    <td colspan="3" width="20%" valign="middle" align="left">{{$sAge}}</td>
                            </tr>
                            <tr class="jedPanel" rowspan = "2">
                                    <td width="*" align="right" valign="top"><strong>Address:</strong></td>
                                    <td rowspan="2" width="50px">{{$sPatientAddress}}</td>
                                    <td width="50px" align="left">&nbsp;</td>
                                    <td width="10px" align="left" valign="middle"></td>
                                    <td width="20%" align="right" valign="middle"><strong>Status:</strong></td>
                                    <td width="15%" valign="middle" align="left">{{$sStatus}}</td> 
                                    <td valign="middle" align="left">  
                                       {{$doneBtn}}
                                    </td>
                            </tr>
                         </table>
                    </td>
                </tr>
            </tbody>
            {{$sFormEnd}}
    </table>
    </br>
    <div class="segContentPane" style="width: 50%;">
        <div style="float:left;">
            {{$addBtn}}
        </div>
        <div style="float:right;">
             {{$saveBtn}}
        </div>
       
        <br>
        <table id="session-list" class="jedList" width="100%" border="0" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                         <th width="10%">Case #</th>
                         <th width="16%">Case Date</th>
                         <th width="5%">Case Type</th>
                         <th width="*">Diagnosis</th>
                         <th width="*">Procedures</th>
                         <th width="1%">Action</th>
                </tr>
            </thead>
            <tbody id="session-body">
            </tbody>
        </table>
    </div>
</div>

