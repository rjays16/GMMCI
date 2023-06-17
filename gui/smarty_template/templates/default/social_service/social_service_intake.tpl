{{* Frame template of medocs page *}}
{{* Note: this template uses a template from the /registration_admission/ *}}

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
        <li><a href="#mswd_part2" onClick="setInterval('saveAssessment(1)',60000);" segTab="tab1" segSetMode="assessment"><span>Assessment</span></a></li>
        <li><a href="#mswd_part3" onClick="" segTab="tab2" segSetMode="case_management"><span>Case Management Services</span></a></li>
    </ul>
    <div id="mswd_part1" align="center" style="margin-top:10px;width:98%">
	    <table width="100%" border="0" cellspacing="5" cellpadding="0">
		    <tr>
                <td valign="top">
                    <div id="intake" class="dashlet" align="left" style="width:100%">
					    {{$sHiddenInputs}}
                        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
							    <td width="99%" nowrap="nowrap"><h1>DEMOGRAPHIC DATA</h1></td>
						    </tr>
                        </table>
                    </div>
				    <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td  width="20%" nowrap="nowrap" class="reg_item"><strong>HRN</strong></td>
                            <td  width="30%" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$sHRN}}</td>
                            <td  width="20%" nowrap="nowrap" class="reg_item"><strong>Case Number</strong></td>
                            <td  width="30%" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$sCasenr}}</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Patient Name</strong></td>
                            <td colspan="3" width="*" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$sPatient_name}}</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Address</strong></td>
                            <td colspan="3" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$sAddress}}</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Gender</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$sSexType}}</td>
                            <td nowrap="nowrap" class="reg_item"><strong>Age</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$sAge}}</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Date of Birth</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$sBdayDate}}</td>
                            <td nowrap="nowrap" class="reg_item"><strong>Place of Birth</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$sBirthPlace}}</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Patient Type</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$sPType}}</td>
                            <td nowrap="nowrap" class="reg_item"><strong>Location</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$sLocation}}</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>{{$slabel}}</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$sAdmissionDate}}</td>
                            <td nowrap="nowrap" class="reg_item"><strong>MSS NO</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$sMss_no}}</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Patient Category</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$sCategory}}</td>
                            <td nowrap="nowrap" class="reg_item"><strong>MSWD Category</strong></td>
                            <td nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$sSSCategory}}</td>
                        </tr>
                        <tr>
                            <td nowrap="nowrap" class="reg_item"><strong>Admitting Diagnosis</strong></td>
                            <td colspan="3" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$sDiagnosis}}</td>
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
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$date_interview}}</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Civil Status </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$civil_status}}</td>
                                        </tr>
                                        <tr>
                                            <td width="20%"></td>
                                            <td width="30%"></td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Religion </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$religion}}</td>
                                        </tr>
                                        {{$jsCalendarSetup}}
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Temporary Address 
                                            <font color="#ff0000">*</font></strong></td>
                                            <td width="*"  colspan="3" nowrap="nowrap" class="segInput">{{$temp_address}}</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Companion Upon Admission </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$companion}}</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Contact Number </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$contact_number}}</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Educational Attainment </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$attainment}}</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Occupation</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$occupation}}</td>
                                        </tr>
                                            {{$ot_occupation}}
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Informant <font color="#ff0000">*</font></strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$informant}}</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Relation to Patient <font color="#ff0000">*</font></strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$pat_relation}}</td>
                                        </tr>
                                        <tr>
                                            <td width="*" nowrap="nowrap" class="reg_item"><strong>Address of Informant <font color="#ff0000">*</font></strong></td>
                                            <td width="*"  colspan="3" nowrap="nowrap" class="segInput">{{$informant_address}}</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Employer </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$employer}}</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Employer Address </strong></td>
                                            <td width="*"  colspan="3" nowrap="nowrap" class="segInput">{{$employer_address}}</td>
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
                                            {{$addbtn}}         
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
                                                    <th width="5%"></th>
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
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$household_no}}</td>

                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Other Source of Income</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$other_source_income}}</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Patient Income </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$income}}</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Other Income</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$other_income}}</td>
                                        </tr>
                                        <tr>
                                             <td width="20%" nowrap="nowrap" class="reg_item"><strong>Total Monthly Income </strong></td>
                                             <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$total_income}}</td>
                                             <td width="20%" nowrap="nowrap" class="reg_item"><strong>Per Capita Income</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$capita_income}}</td>
                                        </tr>

                                           <tr>
                                             <td width="20%" nowrap="nowrap" class="reg_item"><strong>Remarks<font color="#ff0000">*</font> </strong></td>
                                             <td width="*" colspan="3" nowrap="nowrap" class="segInput">&nbsp;{{$monthly_income_remarks}}</td>
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
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$living}}</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Light Source </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$light_source}}</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Living Expenses </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$living_amount}}</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Light Expenses </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$light_amount}}</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Water Source</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$water_source}}</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Fuel Source</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$fuel_source}}</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Water Expenses </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$water_amount}}</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Fuel Expenses</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$fuel_amount}}</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Food </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$food_amount}}</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Househelp </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$househelp_amount}}</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Education</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$educ_amount}}</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Medical Expenditure </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$medical_amount}}</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Clothing</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$clothing_amount}}</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Insurance Plan </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$plan_amount}}</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Transportation</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$trans_amount}}</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Others </strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$others_amount}}</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Total Monthly Expenditure</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput"> &nbsp;{{$total_expenses}}</td>

                                        </tr>
                                            <tr>
                                                <td width="20%" nowrap="nowrap" class="reg_item"><strong>Remarks <font color="#ff0000">*</font> </strong></td>
                                                
                                                 <td width="*" colspan="3" nowrap="nowrap" class="segInput"> &nbsp;{{$monthly_expenses_remarks}}</td>
                                                    

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
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$classification}}</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>PhilHealth Member?</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$phic_member}}</td>
                                        </tr>
                                        <tr>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Other Sectoral</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$sectoral}}</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Category</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$phic_category}}</td>
                                        </tr>
                                             {{$other_row}}
                                              {{$id_row}}
                                        <tr> 
                                             <td width="20%" nowrap="nowrap" class="reg_item"><strong>Modifier</strong></td>
                                            <td width="*"  nowrap="nowrap" class="segInput">&nbsp;{{$modifier}}</td>
                                             <td width="20%" nowrap="nowrap" class="reg_item"><strong>Additional Support</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$additional_support}}</td>
                                            
                                        </tr>
                                        <tr>
                                        <td width="20%" nowrap="nowrap" class="reg_item"><strong>Sub Modifier</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;{{$sub_modifier}}</td>
                                            {{$other_support}}
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Point of Care</strong></td>
                                            <td width="*" colspan="3" nowrap="nowrap" class="segInput">
                                                &nbsp;{{$is_poc}}
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
                                            <td width="*" nowrap="nowrap" class="segInput">{{$final_diagnosis}}</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Duration of Problems / Symptoms</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">{{$duration_prob}}</td>
                                        </tr>
                                        <tr>   
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Previous Treatment / Duration</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">{{$prev_treatment}}</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Present Treatment Plan</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">{{$present_treatment}}</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Health Accessibility Problems</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">{{$health_access}}</td>
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
                                            <td width="*" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$source_referral}}</td>
                                        </tr>
                                        <tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Name of Referral</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$name_referral}}</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Address</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">{{$name_address}}</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Contact Number</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$referral_number}}</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Remarks</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">{{$remarks}}</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" nowrap="nowrap" class="reg_item"><strong>Social Worker</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">&nbsp;&nbsp;{{$social_worker}}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        </div>
                        <div>
                            {{$DeMe_submit}}
                            {{$DeMe_print}} 
                            {{$DeMe_cancel}}
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
                                                        {{$sfTemp}}
                                                        <tr>
                                                           {{$no_social_problem}}
                                                        </tr>
                                                    </tbody>
                                                </table>
                                           </div>
                                        </div>
                                    </td>
                                </tr>  
                            </tr>        
                        </table>
                        {{$social_submit}}
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
                                                        {{$peTemp}}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tr>
                        </table>
                        {{$problem_submit}}
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
                                                    {{$problemtemp}}
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
                                            {{$counseling}}
                                        </tr>
                                        <tr>
                                            <td width="22%" class="reg_item"><strong>Topic Concerns</strong></td>
                                            <td width="*" nowrap="nowrap" class="segInput">
                                                <table width="100%">
                                                    {{$topictemp}}
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
                                                        {{$no_reason}}
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
                                                        {{$social_diagnosis}}
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
                                                        {{$intervention}}
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
                                                        {{$action_taken}}
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
                                                        {{$fremarks}}
                                                    </tr>
                                                </table>
                                             </td>           
                                        </tr>
                                    </table>
                                 </td>
                            </tr>            
                        </table> 
                    </div>
                    <div>
                    {{$findings_submit}}
                    {{$Assess_print}}
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
                                                {{$planningTemp}}
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
                                                {{$referralTemp}}
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
                                                {{$psychoTemp}}
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
                                                                {{$caseTemp}}
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
                                                                {{$followupTemp}}
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
                                                                {{$coordinationTemp}}
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
                                                                {{$documentationTemp}}
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
                                                                {{$cremarks}}
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
                        <div>
                            {{$case_submit}}
                        </div>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="segPanel" id="dependent" style="display:none" align="left">
    <div align="center" style="overflow:hidden">
        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td  width="20%" nowrap="nowrap" ><strong>Name</strong></td>
                <td  width="40%" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$name_dep}}</td>
                <br/>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>     
            <tr>
                <td  width="20%" nowrap="nowrap" ><strong>Age</strong></td>
                <td  width="20%" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$age_dep}}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>  
            <tr>
                <td nowrap="nowrap" ><strong>Civil Status</strong></td>
                <td class="segSocial">&nbsp;&nbsp;{{$cstatus_dep}}</td>            
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr> 
            <tr>
                <td nowrap="nowrap" ><strong>Relation</strong></td>
                <td class="segSocial">&nbsp;&nbsp;{{$relation_dep}}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>  
            <tr>
                <td nowrap="nowrap" ><strong>Occupation</strong></td>
                <td class="segSocial">&nbsp;&nbsp;{{$occupation_dep}}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr> 
                {{$ot_occupation_dep}}
            <tr>
                <td>&nbsp;</td>
            </tr> 
            <tr>
                <td nowrap="nowrap" ><strong>Education</strong></td>
                <td class="segSocial">&nbsp;&nbsp;{{$education_dep}}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr> 
            <tr>
                <td nowrap="nowrap" ><strong>Monthly Income</strong></td>
                <td width="40%" nowrap="nowrap" class="segSocial">&nbsp;&nbsp;{{$monthly_income_dep}}</td>
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

{{$sTailScripts}}
{{$sTailScripts2}}
