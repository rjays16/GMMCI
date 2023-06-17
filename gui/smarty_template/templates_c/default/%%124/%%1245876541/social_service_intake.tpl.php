<?php /* Smarty version 2.6.0, created on 2018-03-20 21:54:29
         compiled from social_service/social_service_intake.tpl */ ?>

<form id="intake_form" name="intake_form" action="Javascript:void(null);" ENCTYPE="multipart/form-data" method="POST"> 
<div align="left" style="width:100%" class="form-header rounded-borders-top">
    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width="99%" nowrap="nowrap"><h1>MSWD ASSESSMENT TOOL</h1></td>
        </tr>
    </table>
</div>
<div id="tab_form" align="center" style="width:100%;">
    <ul id="mswd-tabs" class="tabs-nav">
        <li><a href="#mswd_part1" onClick="" segTab="tab0" segSetMode="demographic"><span>Demographic and Medical Data</span></a></li>
        <li><a id="tab1" href="#mswd_part2" onClick="setInterval('saveAssessment(1)',60000);" segTab="tab1" segSetMode="assessment"><span>Assessment</span></a></li>
        <li><a href="#mswd_part3" onClick="" segTab="tab2" segSetMode="case_management"><span>Case Management Services</span></a></li>
       <!--  <li><a href="#mswd_part4" onClick="" segTab="tab3" segSetMode="case_management"><span>PDPU</span></a></li> -->
    </ul>
    <div id="mswd_part1" align="center" style="margin-top:10px;width:98%">
        <table width="100%" border="0" cellspacing="5" cellpadding="0">
            <tr>
                <td valign="top">
                    <div id="intake" class="dashlet" align="left" style="width:100%">
                        <?php echo $this->_tpl_vars['sHiddenInputs']; ?>

                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="99%" nowrap="nowrap"><h1>DEMOGRAPHIC DATA</h1></td>
                            </tr>
                        </table>
                    </div>
                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td  width="20%" nowrap="nowrap" class="reg_item"><strong>HRN</strong></td>
                            <td  width="30%" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sHRN']; ?>
</td>
                            <td  width="20%" nowrap="nowrap" class="reg_item"><strong>Case Number</strong></td>
                            <td  width="30%" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sCasenr']; ?>
</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Patient Name</strong></td>
                            <td colspan="3" width="*" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sPatient_name']; ?>
</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Address</strong></td>
                            <td colspan="3" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sAddress']; ?>
</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Gender</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sSexType']; ?>
</td>
                            <td nowrap="nowrap" class="reg_item"><strong>Age</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sAge']; ?>
</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Date of Birth</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sBdayDate']; ?>
</td>
                            <td nowrap="nowrap" class="reg_item"><strong>Place of Birth</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sBirthPlace']; ?>
</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Patient Type</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sPType']; ?>
</td>
                            <td nowrap="nowrap" class="reg_item"><strong>Location</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sLocation']; ?>
</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong><?php echo $this->_tpl_vars['slabel']; ?>
</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sAdmissionDate']; ?>
</td>
                            <td nowrap="nowrap" class="reg_item"><strong>MSS NO</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sMss_no']; ?>
</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Patient Category</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sCategory']; ?>
</td>
                            <td nowrap="nowrap" class="reg_item"><strong>MSWD Category</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sSSCategory']; ?>
</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Admitting Diagnosis</strong></td>
                            <td colspan="3" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sDiagnosis']; ?>
</td>
                        </tr>
                    </table>
                    <div id="demographic" class="dashlet" align="left" style="width:100%">
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="99%" nowrap="nowrap"><h1>PERSONAL DETAILS</h1></td>
                            </tr>
                            <tr>
                                <td width="99%" nowrap="nowrap"><h1>All fields with  <font color="#ff0000">*</font> are required.</h1></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="segPanel">
                                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Date of Interview</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['date_interview']; ?>
</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Civil Status </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['civil_status']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="20%"></td>
                                            <td width="30%"></td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Religion </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['religion']; ?>
</td>
                                        </tr>
                                        <?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Temporary Address 
                                            <font color="#ff0000">*</font></strong></td>
                                            <td width="*"  colspan="3" nowrap="nowrap" class="segInput"><?php echo $this->_tpl_vars['temp_address']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Companion Upon Admission </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['companion']; ?>
</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Contact Number </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['contact_number']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Educational Attainment </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['attainment']; ?>
</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Occupation</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['occupation']; ?>
</td>
                                        </tr>
                                            <?php echo $this->_tpl_vars['ot_occupation']; ?>

                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Informant <font color="#ff0000">*</font></strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['informant']; ?>
</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Relation to Patient <font color="#ff0000">*</font></strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['pat_relation']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="*" nowrap="nowrap" class="reg_item"><strong>Address of Informant <font color="#ff0000">*</font></strong></td>
                                            <td width="*"  colspan="3" nowrap="nowrap" class="segInput"><?php echo $this->_tpl_vars['informant_address']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Employer </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['employer']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Employer Address </strong></td>
                                            <td width="*"  colspan="3" nowrap="nowrap" class="segInput"><?php echo $this->_tpl_vars['employer_address']; ?>
</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div id="family" class="dashlet"  align="left" style="width:100%">
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="99%" nowrap="nowrap"><h1>FAMILY COMPOSITION</h1></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="segPanel">
                                   <div class="active-area drop-shadow pre-space rounded-borders-all">
                                       <div class="form-header rounded-borders-top">
                                           <h1>Relations</h1>
                                            <?php echo $this->_tpl_vars['addbtn']; ?>
         
                                       </div>
                                       
                                       <div id="dependents_form">
                                            <table class="data-grid" id="dependents-list">
                                                <thead>
                                                <tr>
                                                    <th width="10%">Name</th>
                                                    <th width="3%">Age</th>        
                                                    <th width="10%" nowrap="nowrap">Civil Status</th>
                                                    <th width="10%" nowrap="nowrap">Relation</th>
                                                    <th width="10%" nowrap="nowrap">Education</th>
                                                    <th width="10%" nowrap="nowrap">Occupation</th>
                                                    <th width="10%" nowrap="nowrap">Monthly Income</th>
                                                    <th width="3%"></th>
                                                    <th width="3%"></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                  
                                                </tbody>
                                            </table> 
                                       </div> 
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="income" class="dashlet"  align="left" style="width:100%">
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="99%" nowrap="nowrap"><h1>MONTHLY INCOME</h1></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="segPanel">
                                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Household Size</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['household_no']; ?>
</td>

                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Other Source of Income</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['other_source_income']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Patient Income </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['income']; ?>
</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Other Income</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['other_income']; ?>
</td>
                                        </tr>
                                        <tr>
                                             <td width="20%" nowrap="nowrap" class="reg_item"><strong>Total Monthly Income </strong></td>
                                             <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['total_income']; ?>
</td>
                                             <td width="20%" nowrap="nowrap" class="reg_item"><strong>Per Capita Income</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['capita_income']; ?>
</td>
                                        </tr>

                                           <tr>
                                             <td width="20%" nowrap="nowrap" class="reg_item"><strong>Remarks<font color="#ff0000">*</font> </strong></td>
                                             <td width="*" colspan="3" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['monthly_income_remarks']; ?>
</td>
                                                  </tr>
                                                  
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="expenses" class="dashlet"  align="left" style="width:100%">
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="99%" nowrap="nowrap"><h1>MONTHLY EXPENSES</h1></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="segPanel">
                                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Living Arrangement</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['living']; ?>
</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Light Source </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['light_source']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Living Expenses </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['living_amount']; ?>
</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Light Expenses </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['light_amount']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Water Source</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['water_source']; ?>
</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Fuel Source</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['fuel_source']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Water Expenses </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['water_amount']; ?>
</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Fuel Expenses</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['fuel_amount']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Food </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['food_amount']; ?>
</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Househelp </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['househelp_amount']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Education</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['educ_amount']; ?>
</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Medical Expenditure </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['medical_amount']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Clothing</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['clothing_amount']; ?>
</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Insurance Plan </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['plan_amount']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Transportation</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['trans_amount']; ?>
</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Others </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['others_amount']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Total Monthly Expenditure</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput"> &nbsp;<?php echo $this->_tpl_vars['total_expenses']; ?>
</td>

                                        </tr>
                                            <tr>
                                                <td width="20%" nowrap="nowrap" class="reg_item"><strong>Remarks <font color="#ff0000">*</font> </strong></td>
                                                
                                                 <td width="*" colspan="3" nowrap="nowrap" class="segInput"> &nbsp;<?php echo $this->_tpl_vars['monthly_expenses_remarks']; ?>
</td>
                                                    

                                                    </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id="mss" class="dashlet"  align="left" style="width:100%">
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="99%" nowrap="nowrap"><h1>PHILHEALTH and CLASSIFICATION</h1></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="segPanel">
                                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                             <td width="20%" nowrap="nowrap" class="reg_item"><strong>Classification <font color="#ff0000">*</font></strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['classification']; ?>
</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>PhilHealth Member?</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['phic_member']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Other Sectoral</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['sectoral']; ?>
</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Category</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['phic_category']; ?>
</td>
                                        </tr>
                                             <?php echo $this->_tpl_vars['other_row']; ?>

                                              <?php echo $this->_tpl_vars['id_row']; ?>

                                            <?php echo $this->_tpl_vars['pwd_row']; ?>

                                        <tr> 
                                             <td width="20%" nowrap="nowrap" class="reg_item"><strong>Modifier</strong></td>
                                            <td width="*"  nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['modifier']; ?>
</td>
                                             <td width="20%" nowrap="nowrap" class="reg_item"><strong>Additional Support</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['additional_support']; ?>
</td>
                                            
                                        </tr>
                                        <tr>
                                        <td width="20%" nowrap="nowrap" class="reg_item"><strong>Sub Modifier</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<?php echo $this->_tpl_vars['sub_modifier']; ?>
</td>
                                            <?php echo $this->_tpl_vars['other_support']; ?>

                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Point of Care</strong></td>
                                            <td width="*" colspan="3" nowrap="nowrap" class="segInput">
                                                &nbsp;<?php echo $this->_tpl_vars['is_poc']; ?>

                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div id="medical" class="dashlet"  align="left" style="width:100%">
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="99%" nowrap="nowrap"><h1>MEDICAL DATA</h1></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="segPanel">
                                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Final Diagnosis</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput"><?php echo $this->_tpl_vars['final_diagnosis']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Duration of Problems / Symptoms</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput"><?php echo $this->_tpl_vars['duration_prob']; ?>
</td>
                                        </tr>
                                        <tr>   
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Previous Treatment / Duration</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput"><?php echo $this->_tpl_vars['prev_treatment']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Present Treatment Plan</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput"><?php echo $this->_tpl_vars['present_treatment']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Health Accessibility Problems</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput"><?php echo $this->_tpl_vars['health_access']; ?>
</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="medical" class="dashlet"  align="left" style="width:100%">
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="99%" nowrap="nowrap"><h1>REFERRAL</h1></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="segPanel">
                                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Source of Referral/Agency</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['source_referral']; ?>
</td>
                                        </tr>
                                        <tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Name of Referral</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['name_referral']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Address</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput"><?php echo $this->_tpl_vars['name_address']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Contact Number</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['referral_number']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Remarks</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput"><?php echo $this->_tpl_vars['remarks']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Social Worker</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">&nbsp;&nbsp;<?php echo $this->_tpl_vars['social_worker']; ?>
</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        </div>
                        <!-- art 08/28/2014 added id to div -->
                        <div id ='submit_tab1'>
                            <?php echo $this->_tpl_vars['DeMe_submit']; ?>

                            <?php echo $this->_tpl_vars['DeMe_print']; ?>
 
                            <!-- <?php echo $this->_tpl_vars['DeMe_cancel']; ?>
 # remove : syboy 10/22/2015 : meow -->
                        </div>
                </td>
            </tr>
        </table>
    </div>
    <div id="mswd_part2" align="center" style="margin-top:10px;width:99%">
        <table width="100%" border="0" cellspacing="5" cellpadding="0">
            <tr>
                <td valign="top">
                    <div id="intake" class="dashlet"  class="dashlet" align="left" style="width:100%">
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="99%" nowrap="nowrap"><h1>SOCIAL FUNCTIONING</h1></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <tr>
                                    <td class="segPanel">
                                       <div class="active-area drop-shadow pre-space rounded-borders-all">
                                           <div id="social_form">
                                                <table class="data-grid" border="0">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="2" width="*">Social Roles</th>
                                                            <th width="18%">Social Interaction</th>
                                                            <th width="11%">Severity Index</th>
                                                            <th width="11%">Duration Index</th>
                                                            <th width="11%">Coping Index</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php echo $this->_tpl_vars['sfTemp']; ?>

                                                        <tr>
                                                           <?php echo $this->_tpl_vars['no_social_problem']; ?>

                                                        </tr>
                                                    </tbody>
                                                </table>
                                           </div>
                                        </div>
                                    </td>
                                </tr>  
                            </tr>        
                        </table>
                        <?php echo $this->_tpl_vars['social_submit']; ?>

                    </div>
                    <div id="demographic" class="dashlet"  align="left" style="width:100%">
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="99%" nowrap="nowrap"><h1>PROBLEMS IN THE ENVIRONMENT</h1></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <tr>
                                    <td class="segPanel">
                                        <div class="active-area drop-shadow pre-space rounded-borders-all">
                                            <div id="environment-form">
                                                <table class="data-grid">
                                                    <thead>
                                                    <tr>
                                                        <th width="1%"></th>
                                                        <th colspan="2" width="*">Economic/Basic Needs Systems Problems</th>
                                                        <th width="18%">Severity Index</th>
                                                        <th width="18%">Duration Index</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php echo $this->_tpl_vars['peTemp']; ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tr>
                        </table>
                        <?php echo $this->_tpl_vars['problem_submit']; ?>

                    </div>
                    <div id="social-findings" class="dashlet"  align="left" style="width:100%">
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="99%" nowrap="nowrap"><h1>FINDINGS</h1></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="segPanel">
                                    <table>
                                        <tr>
                                            <td width="22%" class="reg_item"><strong>Problem Presented at Intake</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">
                                                <table width="100%">
                                                    <?php echo $this->_tpl_vars['problemtemp']; ?>

                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td class="segPanel">
                                    <table>
                                        <tr>
                                            <td width="22%" class="reg_item"><strong>Counseling Done</strong></td>
                                            <?php echo $this->_tpl_vars['counseling']; ?>

                                        </tr>
                                        <tr>
                                            <td width="22%" class="reg_item"><strong>Topic Concerns</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">
                                                <table width="100%">
                                                    <?php echo $this->_tpl_vars['topictemp']; ?>

                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td class="segPanel">
                                    <table>
                                        <tr>
                                             <td width="22%" class="reg_item"><strong>No Reason</strong></td>
                                             <td width="*" nowrap="nowrap" class="segInput">
                                                <table width="100%">
                                                    <tr>
                                                        <?php echo $this->_tpl_vars['no_reason']; ?>

                                                    </tr>
                                                </table>
                                             </td>           
                                        </tr>
                                    </table>
                                 </td>
                            </tr>
                            <tr>
                                <td class="segPanel">
                                    <table>
                                        <tr>
                                             <td width="19%" class="reg_item"><strong>Assessment Findings / Social Diagnosis</strong></td>
                                             <td width="*" nowrap="nowrap" class="segInput">
                                                <table width="100%">
                                                    <tr>
                                                        <?php echo $this->_tpl_vars['social_diagnosis']; ?>

                                                    </tr>
                                                </table>
                                             </td>           
                                        </tr>
                                    </table>
                                 </td>
                            </tr>
                            <tr>
                                <td class="segPanel">
                                    <table>
                                        <tr>
                                             <td width="19%" class="reg_item"><strong>Recommended Interventions</strong></td>
                                             <td width="*" nowrap="nowrap" class="segInput">
                                                <table width="100%">
                                                    <tr>
                                                        <?php echo $this->_tpl_vars['intervention']; ?>

                                                    </tr>
                                                </table>
                                             </td>           
                                        </tr>
                                    </table>
                                 </td>
                            </tr>
                            <tr>
                                <td class="segPanel">
                                    <table>
                                        <tr>
                                             <td width="22%" class="reg_item"><strong>Action Taken</strong></td>
                                             <td width="*" nowrap="nowrap" class="segInput">
                                                <table width="100%">
                                                    <tr>
                                                        <?php echo $this->_tpl_vars['action_taken']; ?>

                                                    </tr>
                                                </table>
                                             </td>           
                                        </tr>
                                    </table>
                                 </td>
                            </tr>
                            <tr>
                                <td class="segPanel">
                                    <table>
                                        <tr>
                                             <td width="22%" class="reg_item"><strong>Remarks</strong></td>
                                             <td width="*" nowrap="nowrap" class="segInput">
                                                <table width="100%">
                                                    <tr>
                                                        <?php echo $this->_tpl_vars['fremarks']; ?>

                                                    </tr>
                                                </table>
                                             </td>           
                                        </tr>
                                    </table>
                                 </td>
                            </tr>            
                        </table> 
                    </div>
                    <!-- art 08/28/2014 added id to div -->
                    <div id='submit_tab2'>
                    <?php echo $this->_tpl_vars['findings_submit']; ?>

                    <?php echo $this->_tpl_vars['Assess_print']; ?>

                    </div>
                </td>
            </tr>
        </table> 
    </div>
    <div id="mswd_part3" align="center" style="margin-top:10px;width:98%">
        <table width="100%" border="0" cellspacing="5" cellpadding="0">
            <tr>
                <td valign="top">
                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="segPanel">
                                <table>
                                    <tr>
                                        <td width="150px" class="reg_item"><strong>PLANNING SCREENING & ELIGIBILITY STUDY</strong></td>
                                        <td width="*" nowrap="nowrap" class="segInput">
                                            <table width="100%">
                                                <?php echo $this->_tpl_vars['planningTemp']; ?>

                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <br/>
                            </td>
                        </tr>
                    </table>
                    <div id="referral-services" class="dashlet" align="left" style="width:100%">
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="99%" nowrap="nowrap"><h1>Concrete & Referral Services</h1></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>  
                                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td class="segPanel">
                                                <?php echo $this->_tpl_vars['referralTemp']; ?>

                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="psycho-social" class="dashlet" align="left" style="width:100%">
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="99%" nowrap="nowrap"><h1>Psycho-Social Counselling</h1></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>   
                                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                           <td class="segPanel">
                                                <?php echo $this->_tpl_vars['psychoTemp']; ?>

                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="other-case" class="dashlet" align="left" style="width:100%">
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>     
                                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td class="segPanel">
                                                <table>
                                                    <tr>
                                                        <td width="150px" class="reg_item"><strong>CASE CON.</strong></td>
                                                        <td width="*" nowrap="nowrap" class="segInput">
                                                            <table width="100%">
                                                                <?php echo $this->_tpl_vars['caseTemp']; ?>

                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <br/>
                                                <table>
                                                    <tr>
                                                        <td width="150px" class="reg_item"><strong>FOLLOW-UP SERVICES</strong></td>
                                                        <td width="*" nowrap="nowrap" class="segInput">
                                                            <table width="100%">
                                                                <?php echo $this->_tpl_vars['followupTemp']; ?>

                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <br/>
                                                <table>
                                                    <tr>
                                                        <td width="150px" class="reg_item"><strong>COORDINATION</strong></td>
                                                        <td width="*" nowrap="nowrap" class="segInput">
                                                            <table width="100%">
                                                                <?php echo $this->_tpl_vars['coordinationTemp']; ?>

                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <br/>
                                                <table>
                                                    <tr>
                                                        <td width="150px" class="reg_item"><strong>DOCUMENTATION</strong></td>
                                                        <td width="*" nowrap="nowrap" class="segInput">
                                                            <table width="100%">
                                                                <?php echo $this->_tpl_vars['documentationTemp']; ?>

                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>    
                                </td>
                            </tr>
                            <tr>
                                <td>     
                                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td class="segPanel">
                                                <table>
                                                    <tr>
                                                        <td width="150px" class="reg_item"><strong>Remarks</strong></td>
                                                        <td width="*" nowrap="nowrap" class="segInput">
                                                            <table width="100%">
                                                                <?php echo $this->_tpl_vars['cremarks']; ?>

                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>    
                                </td>
                            </tr>
                        </table>
                    </div>
                        <!-- art 08/28/2014 added id to div -->
                        <div id='submit_tab3'> 
                            <?php echo $this->_tpl_vars['case_submit']; ?>

                        </div>
                </td>
            </tr>
        </table>
    </div>
    <!-- added by art 08/28/2014 -->
    <!-- <div id="mswd_part4" align="center" style="margin-top:10px;width:98%">
        <table width="100%" border="0" cellspacing="5" cellpadding="0">
            <tr>
                <td valign="top">
                    <div id="intake" class="dashlet" align="left" style="width:100%">
                        
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="99%" nowrap="nowrap"><h1>PDPU ASSESSMENT AND REFERRAL FORM</h1></td>
                            </tr>
                        </table>
                    </div>
                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td  width="20%" nowrap="nowrap" class="reg_item"><strong>Patient Name</strong></td>
                            <td  width="30%" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sPatient_name']; ?>
</td>
                            <td  width="20%" nowrap="nowrap" class="reg_item"><strong>HRN</strong></td>
                            <td  width="30%" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sHRN']; ?>
</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Address</strong></td>
                            <td colspan="3" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sAddress']; ?>
</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Gender</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sSexType']; ?>
</td>
                            <td nowrap="nowrap" class="reg_item"><strong>Age</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['sAge']; ?>
</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Civil Status</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['pdpucivilstatus']; ?>
</td>
                            
                            <td nowrap="nowrap" class="reg_item"><strong>Ward/OPD Clinic</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['ward']; ?>
</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Dx</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['dx']; ?>
</td>
                            <td nowrap="nowrap" class="reg_item"><strong>Classification</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['pdpuclassification'];  echo $this->_tpl_vars['pdpuclass']; ?>
</td>
                            
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Attending Physician</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['physician']; ?>
</td>
                        </tr>
                    </table>
                    

                    <div id="medical" class="dashlet"  align="left" style="width:100%">
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="segPanel">
                                    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Recommended Intervention</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput"><?php echo $this->_tpl_vars['pdpuintervention']; ?>
</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Remarks</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput"><?php echo $this->_tpl_vars['pdpuremarks']; ?>
</td>
                                        </tr>
                                        <td nowrap="nowrap" class="reg_item"><strong>PDPU staff</strong></td>
                                        <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['pdpustaff']; ?>
</td>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                        
                        <div id ='submit_tab4'>
                            <?php echo $this->_tpl_vars['pdpusave']; ?>

                            <?php echo $this->_tpl_vars['pdpuprint']; ?>

                        </div>
                </td>
            </tr>
        </table>
    </div> -->

    <!-- --------------------------------------end pdpu-------------------------------------------- -->
    <!-- edn art -->

</div>

<div class="segPanel" id="dependent" style="display:none" align="left">
    <div align="center" style="overflow:hidden">
        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td  width="20%" nowrap="nowrap" ><strong>Name</strong></td>
                <td  width="40%" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['name_dep']; ?>
</td>
                <br/>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>     
            <tr>
                <td  width="20%" nowrap="nowrap" ><strong>Age</strong></td>
                <td  width="20%" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['age_dep']; ?>
</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>  
            <tr>
                <td nowrap="nowrap" ><strong>Civil Status</strong></td>
                <td class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['cstatus_dep']; ?>
</td>            
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr> 
            <tr>
                <td nowrap="nowrap" ><strong>Relation</strong></td>
                <td class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['relation_dep']; ?>
</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>  
            <tr>
                <td nowrap="nowrap" ><strong>Occupation</strong></td>
                <td class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['occupation_dep']; ?>
</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr> 
                <?php echo $this->_tpl_vars['ot_occupation_dep']; ?>

            <tr>
                <td>&nbsp;</td>
            </tr> 
            <tr>
                <td nowrap="nowrap" ><strong>Education</strong></td>
                <td class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['education_dep']; ?>
</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr> 
            <tr>
                <td nowrap="nowrap" ><strong>Monthly Income</strong></td>
                <td width="40%" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;<?php echo $this->_tpl_vars['monthly_income_dep']; ?>
</td>
            </tr>
        </table>
     </div>
</div> 
<!--<table border="1">
<tr><td colspan="3">Enter Your Information
<input id="addbtn" type="button" class="addRow" value="Add Row"/></td></tr>
<tr><td>Email</td><td><input type="text" size="24"/></td>
<td><input id="minbtn" type="button" class="delRow" value="Delete Row"/></td></tr>
</table>-->

</form>
<br />
<br />

<?php echo $this->_tpl_vars['sTailScripts']; ?>

<?php echo $this->_tpl_vars['sTailScripts2']; ?>
