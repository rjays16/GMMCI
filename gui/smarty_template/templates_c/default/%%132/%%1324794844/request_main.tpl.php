<?php /* Smarty version 2.6.0, created on 2018-03-26 15:51:27
         compiled from dialysis/request_main.tpl */ ?>
<style type="text/css">
    .tabFrame {
        /*padding:5px;*/
        min-height:140px;
    }
</style>
<script language="javascript" type="text/javascript">
    function tabClick(obj) {
    if (obj.className=='segActiveTab') return false;
    var dList = obj.parentNode;
    var tab;
    if (dList) {
    var listItems = dList.getElementsByTagName("LI");
    if (obj) {
    for (var i=0;i<listItems.length;i++) {
    if (obj!=listItems[i]) {
    listItems[i].className = "";
    tab = listItems[i].getAttribute('segTab');
    if ($(tab))
        $(tab).style.display = "none";
}
}
tab = obj.getAttribute('segTab');
if ($(tab))	$(tab).style.display = "block";
obj.className = "segActiveTab";
}
}
}

function toggleTBody(list) {
var dTable = $(list);
if (dTable) {
var dBody = dTable.getElementsByTagName("TBODY")[0];
if (dBody) dBody.style.display = (dBody.style.display=="none") ? "" : "none";
}
}

function enableInputChildren(id, enable) {
var el=$(id);
if (el) {
var children = el.getElementsByTagName("INPUT");
if (children) {
for (i=0;i<children.length;i++) {
children[i].disabled = !enable;
}
return true;
}
}
return false;
}
    <?php echo $this->_tpl_vars['print']; ?>

</script>
<?php echo $this->_tpl_vars['sFormStart']; ?>

<div style="width:750px; margin-top:10px">
    <table border="0" cellspacing="1" cellpadding="0" width="100%" align="center" style="">
        <tbody>
            <tr height="5"><td class="segPanelHeader" colspan="4">Patient Information</td></tr>
            <tr>
                <td width="53%" valign="top" class="segPanel">
                    <table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
                        <tr valign="top">
                            <td align="right" nowrap="nowrap"><label>PID</label></td>
                            <td align="left" valign="middle"><?php echo $this->_tpl_vars['sPatientID']; ?>
</td>
                            <td rowspan="5" class="photo_id" align="center" id="photo_row" style="background-color:transparent">
                                <img width="180px" height="150px" src="<?php echo $this->_tpl_vars['img_source']; ?>
" name="headpic" id="headpic" border="0">
                                <input type="hidden" id="photo_src" name="photo_src" value=""/>
                            </td>
                        </tr>
                        <tr valign="top">
                            <td align="right" nowrap="nowrap"><label>Fullname</label></td>
                            <td align="left" nowrap="nowrap" valign="middle">
                                <?php echo $this->_tpl_vars['sPatientEncNr']; ?>

                                <?php echo $this->_tpl_vars['sPatientName']; ?>

                                <?php echo $this->_tpl_vars['sSelectEnc']; ?>

                                <?php echo $this->_tpl_vars['sClearEnc']; ?>

                            </td>
                        </tr>
                        <tr valign="top">
                            <td align="right" nowrap="nowrap"><label>Address</label></td>
                            <td align="left" nowrap="nowrap" valign="middle"><?php echo $this->_tpl_vars['sAddress']; ?>
</td>
                        </tr>
                        <tr valign="top">
                            <td align="right" nowrap="nowrap"><label>Age</label></td>
                            <td align="left" nowrap="nowrap" valign="middle">
                                <?php echo $this->_tpl_vars['sPatientAge']; ?>

                                <label>Birthday</label>
                                <?php echo $this->_tpl_vars['sPatientBirthday']; ?>

                            </td>
                        </tr>
                        <tr valign="top">
                            <td align="right" nowrap="nowrap"><label>Gender</label></td>
                            <td align="left" nowrap="nowrap" valign="middle">
                                <?php echo $this->_tpl_vars['sPatientGender']; ?>

                                <label>Civil Status</label>
                                <?php echo $this->_tpl_vars['sPatientStatus']; ?>

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="53%" valign="top" class="segPanel">
                    <table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
                        <tr valign="top">
                            <td nowrap="nowrap" align="right"><label>Admitting Diagnosis</label></td>
                            <td valign="middle"><?php echo $this->_tpl_vars['sPatientDiagnosis']; ?>
</td>
                            <td nowrap="nowrap" align="right">
                                <table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
                                    <tr valign="top">
                                        <td nowrap="nowrap" align="left">
                                            <label>Admission Date</label>&nbsp;&nbsp;
                                            <?php echo $this->_tpl_vars['sPatientAdmissionDate']; ?>

                                        </td>
                                    </tr>
                                    <tr valign="top">
                                        <td nowrap="nowrap" align="left">
                                            <label>Discharged Date</label>
                                            <?php echo $this->_tpl_vars['sPatientDischargeDate']; ?>

                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr valign="top">
                            <td nowrap="nowrap" align="right"><label>Location/Clinic</label></td>
                            <td align="left" valign="middle"><?php echo $this->_tpl_vars['sPatientLocation']; ?>
</td>
                            <td nowrap="nowrap" align="right">
                                <table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
                                    <tr valign="top">
                                        <td nowrap="nowrap" align="left">
                                            <label>Patient Type</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sPatientType']; ?>

                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <br/>
</div>
<div style="width:800px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td valign="top">
                <ul id="request-tabs" class="segTab" style="padding-left:10px;">
                    <li id="tab_request" <?php if ($this->_tpl_vars['bTabRequest']): ?>class="segActiveTab"<?php endif; ?> onclick="tabClick(this)" segTab="new_transact">
                        <h2 class="segTabText">New Transaction</h2>
                    </li>
                    <li id="tab_history" <?php if ($this->_tpl_vars['bTabHistory']): ?>class="segActiveTab"<?php endif; ?> onclick="tabClick(this)" segTab="history">
                        <h2 class="segTabText">Billing</h2>
                    </li>
                </ul>
                <div class="" style="width:100%;height:300px; border-top:2px solid #4e8ccf; margin-left:10px" align="center">
                    <div id="new_transact" style="padding-top: 10px; padding-bottom: 10px;<?php if (! $this->_tpl_vars['bTabRequest']): ?>display:none<?php endif; ?>">
                        <table border="0" cellspacing="0" cellpadding="0" width="110%" align="center" style="">
                            <tbody>
                                <tr height="5">
                                    <td class="segPanelHeader" colspan="2">Transaction Details</td>
                                </tr>
                                <tr>
                                    <td width="53%" valign="top"  class="segPanel">
                                        <table border="0" cellspacing="1" cellpadding="5" width="100%" style="margin-top: 5%;font-family:Arial, Helvetica, sans-serif">
                                            <!--
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Reference</label></td>
                                                <td align="left" valign="middle"><?php echo $this->_tpl_vars['requestReferenceNo']; ?>
</td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>No. of Visits</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle"><?php echo $this->_tpl_vars['requestVisitNo']; ?>
</td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Attending Nurse</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle"><?php echo $this->_tpl_vars['requestNurses']; ?>
</td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Request Type</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle"><?php echo $this->_tpl_vars['requestDialysisType']; ?>
</td>
                                            </tr>

                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Remarks</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle"><?php echo $this->_tpl_vars['requestRemarks']; ?>
</td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Procedure</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle"><?php echo $this->_tpl_vars['requestProcedure']; ?>
</td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Status</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle"><?php echo $this->_tpl_vars['requestStatus']; ?>
</td>
                                            </tr>
                                            -->
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap" valign="bottom"><label>Requesting Doctor</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle"><?php echo $this->_tpl_vars['requestDoctors']; ?>
</td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap" valign="bottom"><label>Diagnosis</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle"><?php echo $this->_tpl_vars['requestDiagnosis']; ?>
</td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap" valign="middle"><label>Encoded by</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle"><?php echo $this->_tpl_vars['requestEncoder']; ?>
</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="*" valign="top"  class="segPanel">
                                        <table border="0" cellspacing="2" cellpadding="0" width="100%" style="font-family:Arial, Helvetica, sans-serif">
                                            <tr valign="top">

                                                <td width="1%" nowrap="nowrap" align="right"><label>Transaction Date</label></td>
                                                <td width="5%" align="left" valign="middle"><?php echo $this->_tpl_vars['requestDate'];  echo $this->_tpl_vars['sCalendarIcon'];  echo $this->_tpl_vars['jsCalendarSetup']; ?>
</td>

                                            </tr>
                                            <!-- <tr>
                                                <td colspan="2"><strong>VITAL SIGNS</strong></td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Blood Pressure</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle"><?php echo $this->_tpl_vars['bp_systole']; ?>
&nbsp;/&nbsp;<?php echo $this->_tpl_vars['bp_diastole']; ?>
&nbsp;<span style="font: 11px Arial;">mm Hg</span></td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Temperature</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle"><?php echo $this->_tpl_vars['temperature']; ?>
&nbsp;<span style="font: 11px Arial;">�C</span></td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Weight</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle"><?php echo $this->_tpl_vars['weight']; ?>
&nbsp;<span style="font: 11px Arial;">kg</span></td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Resp. Rate</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle"><?php echo $this->_tpl_vars['resp_rate']; ?>
&nbsp;<span style="font: 11px Arial;">br/m</span></td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Pulse Rate</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle"><?php echo $this->_tpl_vars['pulse_rate']; ?>
&nbsp;<span style="font: 11px Arial;">b/m</span></td>
                                            </tr>
                                            <tr>
                                                <td align="right" nowrap="nowrap"><label></label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">&nbsp;<span style="font: 11px Arial;"></span></td>
                                            </tr>
                                            <tr>
                                                <td align="right" nowrap="nowrap"><label></label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">&nbsp;<span style="font: 11px Arial;"></span></td>
                                            </tr>
                                            <tr>
                                                <td align="right" nowrap="nowrap"><label></label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">&nbsp;<span style="font: 11px Arial;"></span></td>
                                            </tr> -->
                                            <tr valign="top">

                                                <td align="right" nowrap="nowrap" valign="middle"><label>Pre-Billing Details</label></td>
                                                
                                                <!-- added by KENTOOT 09-19-2014 -->
                                                <td align="left" nowrap="nowrap" colspan="4" style="font:bold 11px Arial; color: red;">
                                                <label>
                                                    <strong><?php echo $this->_tpl_vars['alertPrebill']; ?>
</strong>
                                                </label>
                                                </td>

                                            </tr>
                                            <tr valign="top">
                                                <td></td>
                                                <td  nowrap="nowrap" valign="middle">
                                                    <table width="90%">
                                                        <tr>
                                                            <td align="center" >
                                                                <center><label>Amount</label></center>
                                                            </td>
                                                            <td>
                                                                <center><label>Quantity</label></center>
                                                            </td>
                                                            <td>
                                                                <center><label>HDF</label></center>
                                                            </td>
                                                            </tr>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                        <tr valign="top">

                            <td align="middle" nowrap="nowrap" valign="middle"><strong><?php echo $this->_tpl_vars['requestBillTypePH']; ?>
</strong></td>

                            <td  nowrap="nowrap" valign="middle">
                                <table width="100%">
                                    <tr>
                                        <td align="middle">
                                            <?php echo $this->_tpl_vars['requestAmountPH']; ?>

                                        </td>
                                        <td align="middle">
                                            <?php echo $this->_tpl_vars['requestQuantityPH']; ?>

                                        </td>
                                                                                                                        <td align="middle">
                                            <?php echo $this->_tpl_vars['requestAmountHDF']; ?>

                                        </td>
                                        <td align="middle">
                                            <?php echo $this->_tpl_vars['requestSubsidizePH']; ?>

                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="middle" nowrap="nowrap" valign="middle"><strong><?php echo $this->_tpl_vars['requestBillTypeNPH']; ?>
</strong></td>
                            <td  nowrap="nowrap" valign="middle">
                                <table width="100%">
                                    <tr>
                                        <td align="middle" >
                                            <?php echo $this->_tpl_vars['requestAmountNPH']; ?>

                                        </td>
                                        <td align="middle">
                                            <?php echo $this->_tpl_vars['requestQuantityNPH']; ?>

                                        </td>
                                                                                                                        <td align="middle">
                                            <?php echo $this->_tpl_vars['requestAmountHDFNPH']; ?>

                                        </td>
                                        <td align="middle">
                                            <?php echo $this->_tpl_vars['requestSubsidizeNPH']; ?>

                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                           <!-- <td align="middle" nowrap="nowrap" valign="middle"><strong>HDF Request</strong></td> -->
                           <td  nowrap="nowrap" valign="middle">
                                <table width="100%">
                                    <tr>
                                        <td align="middle" >
                                            <!-- <?php echo $this->_tpl_vars['requestAmountHDF']; ?>
 -->
                                        </td>
                                        <td align="middle">
                                            <!-- <?php echo $this->_tpl_vars['requestQuantityHDF']; ?>
 -->
                                        </td>
                                        <td align="middle">
                                            <!-- <?php echo $this->_tpl_vars['requestSubsidizeHDF']; ?>
 -->
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="middle" nowrap="nowrap" valign="middle"><strong>Include Lab Request</strong></td>
                            <td align="left" nowrap="nowrap" colspan="4">
                                <input type='hidden' value='0' name="labRequest">
                                <input type="checkbox" name="labRequest" style="margin-left:15px" value='1'>
                            </td>
                        </tr>
                        <tr valign="bottom">
                            <td align="right" nowrap="nowrap" colspan="4">
                                <?php echo $this->_tpl_vars['submitBtn']; ?>
<!--<?php echo $this->_tpl_vars['cancelBtn']; ?>
-->
                            </td>
                        </tr>
                        </table>
                        </td>
                        </tr>
                        </tbody>
                        </table>
                    </div>

                    <div id="history" style="padding:2px;padding-top:3px;<?php if (! $this->_tpl_vars['bTabHistory']): ?>display:none<?php endif; ?>">
                        <table border="0" cellspacing="1" cellpadding="0" width="100%" align="center" style="font-family:Arial, Helvetica, sans-serif">
                            <tbody>
                                <tr height="5">
                                    <td class="segPanelHeader" colspan="4">Billing Details</td>
                                </tr>
                                <tr>
                                    <td class="segPanel">
                                        <table style="font-family:Arial, Helvetica, sans-serif" width="100%" align="center">
                                            <tr>
                                                <td align="left">Encounter No:<?php echo $this->_tpl_vars['patientEncounter']; ?>
 &nbsp;
                                                    <?php echo $this->_tpl_vars['toLaboratory'];  echo $this->_tpl_vars['viewBilling']; ?>
 <?php echo $this->_tpl_vars['toDischarge']; ?>
</td>

                                                <td align="right"><!--<?php echo $this->_tpl_vars['requestBtn']; ?>
-->
                                                   <?php echo $this->_tpl_vars['buttonScheduleHistory'];  echo $this->_tpl_vars['toRefresh'];  echo $this->_tpl_vars['historyBtn'];  echo $this->_tpl_vars['continuousReportBtn'];  echo $this->_tpl_vars['prntsoa']; ?>
</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div id="billing_list" style="margin-top:5px;"></div>
                    </div>
            </td>
    </table>
</div>
<div id="frame-dialog" style="display: none;">
    <iframe id="dialog-frame" src="" style="height: 100%; width: 100%; border: none;">

    </iframe>
</div>
                                <table id="subsidizeModal" style="display:none;">
                                    <tr>
                                        <td align="left" colspan="2">
                                            <h1 id="subsidizeText"></h1> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left">
                                            Subsidy Amount: 
                                        </td>
                                        <td align="left">
                                            <?php echo $this->_tpl_vars['subsidy_amount_input']; ?>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left">
                                            Classification: 
                                        </td>
                                        <td align="left">
                                            <?php echo $this->_tpl_vars['subsidy_classification_options']; ?>

                                        </td>
                                    </tr>
                                </table>
<?php echo $this->_tpl_vars['submitted']; ?>

<!--<?php echo $this->_tpl_vars['dialysis_type']; ?>
 -->
<?php echo $this->_tpl_vars['encounter_nr']; ?>

<?php echo $this->_tpl_vars['encounter_type']; ?>

<?php echo $this->_tpl_vars['pid']; ?>

<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>

<?php echo $this->_tpl_vars['DialogModals']; ?>