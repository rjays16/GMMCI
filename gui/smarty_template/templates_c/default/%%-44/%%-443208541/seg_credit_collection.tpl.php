<?php /* Smarty version 2.6.0, created on 2017-10-10 22:20:50
         compiled from billing/seg_credit_collection.tpl */ ?>
<table width="96%" cellpadding="2" cellspacing="2" id="mainTable"
       style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
    <thead>
    <tr>
        <th id="billcol_01" colspan="2" rowspan="2" align="left" class="jedPanelHeader" style="border-right:none">
            CREDIT AND COLLECTION&nbsp;&nbsp;</th>
        <th id="billcol_02" colspan="2" rowspan="2" align="center" class="jedPanelHeader"
            style="border-right:none;border-left:none"><span id="remaindays" style="display:none"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span
                    id="coverdays" style="display:none"></span><span id="savethis" style="display:none"></span></th>
        <th class="jedPanelHeader" style="border-left:none" align="right">
            <div id="categ_col" style="display:none">&nbsp;MEMBERSHIP CATEGORY:<img
                        id="btnEditMemCat" src="../../images/cashier_edit.gif" style="cursor: pointer;"
                        align="absmiddle"/>&nbsp;<span id="mcategdesc" name="mcategdesc"></span></div>
        </th>
        <th class="jedPanelHeader" style="border-left:none" align="right">
            <div id="categ_col" style="display:none"></div>
        </th>
    </tr>
    </thead>
</table>

<form id="collectionForm">
    <table width="96%" cellpadding="2" cellspacing="2" id="collectionMainTable2"
           style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
        <tbody>
        <!-- Basic information -->
        <tr>
            <td colspan="2" rowspan="2" align="left" valign="top" class="jedPanel" style="border-color: #F5F5F5">
                <table width="100%" border="0" cellpadding="2" cellspacing="0" style="font-size:11px">
                    <tr class="jedPanel">
                        <td width="5%" align="right"><strong>HRN:</strong></td>
                        <td width="5%" align="left"><?php echo $this->_tpl_vars['sHRNInput']; ?>
</td>
                        <td width="50px" align="left"><?php echo $this->_tpl_vars['sPid']; ?>
</td>
                        <td colspan="2" width="14%" align="right"><strong>Case No:</strong></td>
                        <td width="25%" align="left" valign="middle"><?php echo $this->_tpl_vars['sEncounter']; ?>
</td>
                        <td colspan="2" width="13%" align="right"></td>
                        <td width="10%" align="left"><span style="color:#0000FF" id="classification"></span></td>

                    </tr>
                    <tr class="jedPanel">
                        <td align="right" valign="middle"><strong>Name:</strong></td>
                        <td colspan="2" width="50px" valign="middle">
                            <?php echo $this->_tpl_vars['sPatientInput']; ?>

                            <span style="vertical-align:bottom"><?php echo $this->_tpl_vars['sSelectPatient']; ?>
</span>
                        </td>
                        <td width="14%" align="right" valign="middle"><strong>Bill No:</strong></td>
                        <td colspan="2" width="20%" valign="middle" align="left"><?php echo $this->_tpl_vars['sBillNo']; ?>
 <span name="eclaims_dte"
                                                                                                    id="eclaims_dte"
                                                                                                    style="color:#07d216; font: bold 14px Arial;"> </span>
                        </td>
                    </tr>
                    <tr class="jedPanel">
                        <td width="*" align="right" valign="top"><strong>Address:</strong></td>
                        <td rowspan="2" width="50px"><?php echo $this->_tpl_vars['sPatienAddress']; ?>
</td>
                        <td align="right" colspan="2" valign="top"></td>
                        <td valign="top" colspan="2"></td> <!--Case Date -->
                        <td width="10%" align="right" valign="middle"><span></span></td>
                        <td width="15%" align="left" valign="middle"></td>
                    </tr>
                    <tr></tr>
                    <tr class="jedPanel">
                        <td width="*" align="right"><strong>Insurance No. :</strong></td>
                        <td width="50px" align="left"><?php echo $this->_tpl_vars['sInsurance']; ?>
</td>
                        <td colspan="2" width="*" align="right" valign="top">&nbsp;</td>
                        <td id="admit_label" style="display:none" align="right" colspan="2" valign="middle">
                            <strong>Adm.:</strong></td>
                        <td id="admit_date" style="display:none" valign="top"><?php echo $this->_tpl_vars['sAdmitDate']; ?>
</td>
                        <td id="showOpdType" style="display:''" width="10%" align="right" valign="middle" colspan="3"></td>
                        <td width="15%" align="left" valign="middle"> <?php echo $this->_tpl_vars['sOpdType']; ?>
 </td>
                        <td>
                            <!-- hidden elements -->
                            <?php echo $this->_tpl_vars['sBillDte']; ?>

                            <?php echo $this->_tpl_vars['sBillFrmDte']; ?>

                            <?php echo $this->_tpl_vars['sIsFinal']; ?>

                        </td>
                    </tr>

                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <table width="96%" cellpadding="2" cellspacing="2" id="mainTable2"
           style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
        <tbody>
        <tr>
            <td colspan="2" rowspan="2" align="left" valign="top" class="jedPanel" style="border-color: #F5F5F5">
                <table width="100%" border="0" cellpadding="2" cellspacing="0" style="font-size:11px">

                    <tr class="jedPanel">
                        <td></td>
                        <td></td>
                        <td width="*" align="left"><strong>Total Gross Amount :</strong></td>
                        <td width="75%" align="left"><?php echo $this->_tpl_vars['sGrossAmount']; ?>
</td>

                    </tr>
                    <tr class="jedPanel">
                        <td></td>
                        <td></td>
                        <td width="*" align="left"><strong>Health Insurance Total Coverage :</strong></td>
                        <td width="75%" align="left"><?php echo $this->_tpl_vars['sCoverage']; ?>
</td>
                    </tr>
                    <tr class="jedPanel">
                        <td></td>
                        <td></td>
                        <td width="*" align="left"><strong>Total Discount :</strong></td>
                        <td width="75%" align="left">
                            <?php echo $this->_tpl_vars['sDiscount']; ?>

                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <label style="font-size: 20px">BALANCE: </label>  <input id="balance" type="text" style="font-size: 20px; width: 20%; text-align: right" readonly />
                        </td>

                    </tr>
                    <tr class="jedPanel">
                        <td></td>
                        <td></td>
                        <td width="*" align="left"><strong>Deposit :</strong></td>
                        <td width="75%" align="left"><?php echo $this->_tpl_vars['sDeposit']; ?>
</td>
                    </tr>
                    <tr class="jedPanel">
                        <td></td>
                        <td></td>
                        <td width="*" align="left"><strong>Net Amount :</strong></td>
                        <td width="75%" align="left"><?php echo $this->_tpl_vars['sNetAmount']; ?>
</td>
                    </tr>
                    <tr class="jedPanel">
                        <td></td>
                        <td></td>
                        <td width="*" align="left"><strong>Less :</strong></td>
                        <td width="75%" align="left"><?php echo $this->_tpl_vars['sTotalGrants']; ?>
</td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <br/>
    <div style="float: right; margin-right:52px; padding: 10px">
        <button id="addB">ADD</button>
        <button id="saveB">SAVE</button>
        <button id="resetB">REFRESH</button>
    </div>

    <table id="collectionGrid" class="CSSTableGenerator" width="90%" cellpadding="2" cellspacing="2"
           style="border-collapse:collapse; border:1px solid #a6b4c9; color:black;">
        <tbody>

        </tbody>
    </table>
</form>

<div id="dialog" title="Select Registered Person"></div>


