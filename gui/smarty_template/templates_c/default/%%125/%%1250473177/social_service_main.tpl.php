<?php /* Smarty version 2.6.0, created on 2018-03-20 21:51:21
         compiled from social_service/social_service_main.tpl */ ?>
<!--<start>-->
<div id="dialog1" style="width:60%;">
	<div class="hd" style="width:100%">Select social service classification...</div>
	<div class="bd" style="width:100%">
		<form id="enSScode" action="Javascript:void(null);" >
		<table width="95%" class="segPanel" style="margin-top:1%">
			<tr>
				<td width="35%" align="left"><span style="color:#FF0000">*</span><b>Code:</b></td>
				<td align="left">
				<select name="service_code" id="service_code" onchange="xajax_OnChangeOptions(this.value, document.getElementById('encounter_nr').value, document.getElementById('pid').value)">
					<option value="0">-Select Code-</option>
				</select>
				<input type="hidden" name="withrec" id="withrec" value="" />
				<input type="hidden" name="subc" id="subc" value="" />
				<!--commented by VAN -->
				<!--</span><span id="sscDesc">(classification description)</span>	-->
				</td>
			</tr>
			<!--added by VAN 07-04-08 -->
			<tr id="subclass" style="">
				<td align="left"><span style="color:#FF0000">*</span><b>Sub Classification:</b></td>
				<td align="left">
				<!--<select name="subservice_code" id="subservice_code" onchange="">-->
				<select name="subservice_code" id="subservice_code" onchange="xajax_OnChangeSubOptions(this.value); checkSubClassification(this.value);">
					<option value="0">-Select Code-</option>
				</select>

				</td>
			</tr>
			<!-- -->
			<!-- Added by Gervie 04-22-2017 -->
			<tr id="_pwd-id" hidden>
				<td align="left"><span style="color: #FF0000">*</span><b>PWD ID:</b></td>
				<td align="left">
					<input type="text" name="pwd_id" style="width:130px;font:bold 12px Arial;" placeholder="PWD ID Number">
					&nbsp;<input type="checkbox" name="pwd_temp" style="vertical-align: middle;" onclick="pwdTemp()"> <strong>Is Temp?</strong>
				</td>
			</tr>
			<tr id="_pwd-expiry" hidden>
				<td align="left"><span style="color: #FF0000">*</span><b>Expiration Date:</b></td>
				<td align="left">
					<div style="display:inline-block">
                        <input type="text" maxlength="10" id="pwd_expiration" name="pwd_expiration" style="width:130px;font:bold 12px Arial;" placeholder="[mm/dd/yyyy]" readonly>
                    	<script type="text/javascript">
                            now = new Date();
                            Calendar.setup ({
                                    inputField: "pwd_expiration",
                                    dateFormat: "%m/%d/%Y",
                                    trigger: "pwd_expiration",
                                    showTime: false,
                                    fdow: 0,
                                    min : Calendar.dateToInt(now),
                                    onSelect: function() { this.hide() }
                            });
                          </script>
                    </div>
				</td>
			</tr>
			<tr id="subID" style="">
				<td align="left"><span style=" color:#FF0000">*</span><b>ID No.:</b></td>
				<td align="left">
					<input type="text" name="idnumber" id="idnumber" value="" size="35">
				</td>
			</tr>
			<!--added by VAN 08-05-08 -->
			<tr id="other_text" style="">
				<td align="left"><span style=" color:#FF0000">*</span><b>Other Classification:</b></td>
				<td align="left">
					<input type="text" name="subservice_code2" id="subservice_code2" value="" size="35">
				</td>
			</tr>
			<!-- -->

			<tr id="personalMod">
				<td align="left" valign="top"><b>Re: Personal Circumstances</b></td>
				<!--<td><textarea id="personal_circumstance" name="personal_circumstance" rows="2" cols="33"></textarea></td>-->
				<td align="left">
					<select name="personal_circumstance" id="personal_circumstance" onchange="">
						<option value="0" >-Select Personal Circumstances-</option>
					</select>
				</td>
			</tr>
			<tr id="communityMod">
				<td align="left" valign="top"><b>Re: Community Situations</b></td>
				<!--<td><textarea id="community_situation" name="community_situation" rows="2" cols="33"></textarea></td>-->
				<td align="left">
					<select name="community_situation" id="community_situation" onchange="">
						<option value="0">-Select Community Situations-</option>
					</select>
				</td>
			</tr>
			<tr id="diseaseMod">
				<td align="left" valign="top"><b>Re: Nature of Illness/Disease</b></td>
				<!--<td><textarea id="nature_of_disease" name="nature_of_disease" rows="2" cols="33"></textarea></td>-->
				<td align="left">
					<select name="nature_of_disease" id="nature_of_disease" onchange="">
						<option value="0">-Select Nature of Illness-</option>
					</select>
				</td>
			</tr>
		</table>
		<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

		</form>
	</div>
</div>


	<!-- Update profile -->
<div id="dialog2" style="width:100%;overflow-y:scroll;">
	<div class="hd" style="width:100%">Update Profile</div>
	<div clas="bd" style=" width:100%">
	<form id="frmupdate" action="Javascript:void(null);">

		<table border=0 class="segPanel" align="center" style="width:100%; margin-top:1%">
			<tr>
				<tr>
				<td colspan="2"><b>Profile: </b></td>
				<td width="24%" height="20px" colspan="2"><b> Monthly Expenses: </b></td>
				</tr>
			</tr>
			<tr>
				<td width="15%" height="20px">Address:</td>
				<td width="15%"><textarea class="segInput" id="address" name="address" cols="20" rows="5" /></textarea></td>
				<td width="*" height="20px" colspan="2">
					<table>
						<tr>
							<td width="10%">House and Lot:</td>
							<td width="*">
								<table width="100%">
									<tr>
										<td width="35%"><input name="hauz_lot_type" id="hauz_lot_type1" type="radio" value="1"  onClick="checkHouse(this.value);" >Free</td>
									</tr>
									<tr>
										<td width="35%"><input name="hauz_lot_type" id="hauz_lot_type2" type="radio" value="2"  onClick="checkHouse(this.value);" >Owned</td>
									</tr>
									<tr>
										<td width="35%"><input name="hauz_lot_type" id="hauz_lot_type3" type="radio" value="3" onClick="checkHouse(this.value);" >Rent</td>
									</tr>
								</table>
							</td>
							<td>
								<table>
									<tr>
										<td width="25%"> <input name="hauz_lot_type" id="hauz_lot_type4" type="radio" value="4" onClick="checkHouse(this.value);" >Shared</td>
									</tr>
									<tr>
										<td width="25%"><input name="hauz_lot_type" id="hauz_lot_type5" type="radio" value="5" onClick="checkHouse(this.value);" >Amortization</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width="18%" height="20px">Informant:</td>
				<td width="20%">
					<input class="segInput" type="text" id="resp" name="resp" value="" />
				</td>
				<td>House</td>
				<td width="40%">
						Php&nbsp;<input class="segInput" type="text" id="hauz_lot2" onblur="assignHauz(); formatValue(this, 2); computeTotal();" name="hauz_lot2" value="" class="text input_mask mask_date_us" style="text-align:right" size="10" align="right" />
						<input type="hidden" id="hauz_lot" name="hauz_lot" value="" align="right"/>
				</td>
			<tr>

			<tr>

				<td>Relation to Patient :</td>
				 <td><input class="segInput" type="text" id="relation" name="relation" value="" /></td>
				<td>Food :</td>
				<td>Php&nbsp;<input class="segInput" type="text" id="food2" name="food2" value="" onblur="assignFood(); formatValue(this, 2); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="10"/>
								<input type="hidden" id="food" name="food" value=""/></td>

			</tr>
			<tr>
				<td>Education :</td>
				<td><select class="segInput" id="occupation_select" name="occupation_select" >
							<option value="0">Not Indicated</option>
						</select>
				</td>
				<td>Light :</td>
				<td>Php&nbsp;<input class="segInput" type="text" id="light2" name="light2" value="" onblur="assignLight(); formatValue(this, 2); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="10" />
										 <input type="hidden" id="light" name="light" value=""/></td>
			</tr>
			<tr>
				<td>Number of Dependents :</td>
				<td><input class="segInput" type="text" id="nr_dep" name="nr_dep" value="" onBlur="computeCapita();" /></td>
				<td>Water :</td>
				<td>Php&nbsp;<input class="segInput" type="text" id="water2" name="water2" value="" onblur="assignWater(); formatValue(this, 2); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="10" />
								<input type="hidden" id="water" name="water" value=""/></td>
			</tr>
			<tr>
				<td>Number of Children :</td>
				<td><input class="segInput" type="text" id="nr_chldren" name="nr_chldren" value=""/></td>
				<td>Transportation :</td>
				<td>Php&nbsp;<input class="segInput" type="text" id="transport2" name="transport2" value="" onblur="assignTransport(); formatValue(this, 2); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="10" />
										 <input type="hidden" id="transport" name="transport" value=""/></td>
			</tr>
			<tr>
				<td>Source of Income :</td>
				<td><input class="segInput" type="text" id="s_income" name="s_income" value=""/></td>
				<td>Other :</td>
				<td>Php&nbsp;<input class="segInput" type="text" id="other2" name="other2" value="" onblur="assignOther(); formatValue(this, 2); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="10" />
										 <input type="hidden" id="other" name="other" value=""/></td>
			</tr>
			<tr>
				<td>Monthly Income :</td>
				<td><input class="segInput" type="text" id="m_income2" name="m_income2" value="" onblur="assignM_income(); computeCapita(); formatValue(this, 2);" class="text input_mask mask_date_us" />
					 <input type="hidden" id="m_income" name="m_income" value=""/>
				</td>
				<td>Total Monthly Expenditure</td>
				<td>Php&nbsp;<input class="segInput" type="text" id="m_expenses" name="m_expenses" value="" readonly="1" class="text input_mask mask_date_us" style="text-align:right" size="10" /></td>
			</tr>
			<tr>
				<td>Per Capita Income :</td>
				<td><input class="segInput" type="text" id="m_capita_income" name="m_capita_income" value="" readonly="1" class="text input_mask mask_date_us" style="text-align:right" />
					 <input type="hidden" id="m_cincome" name="m_cincome" value=""/>
				</td>

			</tr>

		</table>

		<?php echo $this->_tpl_vars['sHiddenInputsB']; ?>

	</form>
	</div>
</div>

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
							<td class="jedPanel3" id="spid" width="50%"><?php echo $this->_tpl_vars['sPid']; ?>
</td>
							<td rowspan="8" class="photo_id"><?php echo $this->_tpl_vars['img_source']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel" id="hmss_no"><strong>MSS Number</strong></td>
							<td class="jedPanel3" id="smss_no"><?php echo $this->_tpl_vars['sMss_no'];  echo $this->_tpl_vars['sMss_no2']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel" id="hcase_no"><strong>Case Number</strong></td>
							<td class="jedPanel3" id="scase_no"><?php echo $this->_tpl_vars['sEncNrPID']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Title</strong></td>
							<td class="jedPanel3"><?php echo $this->_tpl_vars['title']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Family Name</strong></td>
							<td class="jedPanel3"><?php echo $this->_tpl_vars['name_last']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Given Name</strong></td>
							<td class="jedPanel3"><?php echo $this->_tpl_vars['name_first']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Gender</strong></td>
							<td class="jedPanel3"  colspan="2"><?php echo $this->_tpl_vars['sSexType']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Date of Birth</strong></td>
							<td class="jedPanel3"  colspan="2"><?php echo $this->_tpl_vars['sBdayDate']; ?>
 &nbsp; <?php echo $this->_tpl_vars['sCrossImg']; ?>
 &nbsp; <font color="black"><?php echo $this->_tpl_vars['sDeathDate']; ?>
</font></td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Place of Birth</strong></td>
							<td class="jedPanel3"  colspan="2"><?php echo $this->_tpl_vars['sBirthPlace']; ?>
</font></td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Age</strong></td>
							<td class="jedPanel3"  colspan="2"><?php echo $this->_tpl_vars['sAge']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Civil Status</strong></td>
							<td class="jedPanel3"  colspan="2"><?php echo $this->_tpl_vars['sCivilStat']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Religion</strong></td>
							<td class="jedPanel3"  colspan="2"><?php echo $this->_tpl_vars['sReligion']; ?>
</font></td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Occupation</strong></td>
							<td class="jedPanel3"  colspan="2"><?php echo $this->_tpl_vars['sOccupation']; ?>
</font></td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Address</strong></td>
							<td class="jedPanel3" colspan="2"><?php echo $this->_tpl_vars['sAddress']; ?>
</font></td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Father's Name</strong></td>
							<td class="jedPanel3" colspan="2"><?php echo $this->_tpl_vars['sFather']; ?>
</font></td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Mother's Name</strong></td>
							<td class="jedPanel3" colspan="2">
							<table border="0" cellpadding="1" cellspacing="0">
							<tr>
							<td align="center" valign="middle" style="padding-right: 15px"><strong><?php echo $this->_tpl_vars['sMotherFirstName']; ?>
</strong></td><td align="center" valign="middle" style="padding-right: 15px"><strong><?php echo $this->_tpl_vars['sMotherMaidenName']; ?>
</strong></td><td align="center" valign="middle" style="padding-right: 15px"><strong><?php echo $this->_tpl_vars['sMotherMiddleName']; ?>
</strong></td><td align="center" valign="middle" style="padding-right: 15px"><strong><?php echo $this->_tpl_vars['sMotherLastName']; ?>
</strong></td>
							</tr>
							<tr>
							<td align="center" valign="middle" style="padding-right: 15px"><font size="1">First Name</font></td><td align="center" valign="middle" style="padding-right: 15px"><font size="1">Maiden Name</font></td><td align="center" valign="middle" style="padding-right: 15px"><font size="1">Middle Name</font></td><td align="center" valign="middle" style="padding-right: 15px"><font size="1">Last Name</font></td>
							</tr></table></font></td>
						</tr>
						<tr id="senior_row" style="display:none">
							<td class="segPanel" id="tdsenior1"><strong><?php echo $this->_tpl_vars['sSeniorLabel']; ?>
</strong></td>
							<td class="jedPanel3" id="tdsenior2"><?php echo $this->_tpl_vars['sSeniorID']; ?>
</td>
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
			<!--<td valign="top">
				<div class="dashlet" align="left" style="width:100%">
					<table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="99%" nowrap="nowrap"><h1>Patient profile</h1></td>
							<td>
								<button class="jedInput" id="updateprofile" style="margin-left:8px">Update profile</button>
							</td>
						</tr>
					</table>
					<table border="0" cellpadding="2" cellspacing="2" width="100%" style="border:2px solid #cccccc">
						<tbody>
							<tr>
								<td class="segPanel" width="45%">
									<strong>Informant</strong>
									<input type="hidden" id="h_respondent" name="h_respondent" value="" />
								</td>
								<td class="jedPanel3" id="respondent"></td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Relation to Patient</strong>
									<input type="hidden" id="h_relation_patient" name="h_relation_patient" value="" />
								</td>
								<td class="jedPanel3" id="relation_patient"></td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Education</strong>
									<input type="hidden" id="h_occupation" name="h_occupation" value="" />
								</td>
								<td class="jedPanel3" id="occupation"></td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Number of Children</strong>
									<input type="hidden" id="h_nrchldren" name="h_nrchldren" value="" />
								</td>
								<td class="jedPanel3" id="nrchldren"></td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Number of Dependents</strong>
									<input type="hidden" id="h_nrdep" name="h_nrdep" value="" />
								</td>
								<td class="jedPanel3" id="nrdep"></td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Source of Income</strong>
									<input type="hidden" id="h_source_income" name="h_source_income" value="" />
								</td>
								<td class="jedPanel3" id="source_income"></td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Monthly Income</strong>
									<input type="hidden" id="h_monthly_income" name="h_monthly_income" value="" />
								</td>
								<td class="jedPanel3" id="monthly_income"></td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Per Capita Income</strong>
									<input type="hidden" id="h_capita_income" name="h_capita_income" value="" />
								</td>
								<td class="jedPanel3" id="capita_income"></td>
							</tr>
							<tr>
								<td class="segPanel">
									<strong>Monthly Expenses</strong>
									<input type="hidden" id="h_monthly_expenses" name="h_monthly_expenses" value="" />
								</td>
								<td class="jedPanel3" id="monthly_expenses"></td>
							</tr>
						</tbody>
					</table>
				</div>
			</td> -->

			<!-- Added by Cherry 07-12-10 -->

			 <td width="*" valign="top">
				<!--<div id="classify" class="dashlet" align="left" style="width:100%">-->
				<div id="profile" class="dashlet" align="left" style="width:100%">
					<table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="99%" nowrap="nowrap"><h1>Patient profile</h1></td>
							<td nowrap="nowrap" id="update_profile">
								<!--<button class="jedInput" id="show_billing" style="margin-left:10px" disabled="disabled" onclick="js_showBillDetails()">Show billing</button>-->
								<!--<button class="jedInput" id="show" >Classify patient</button> -->
								<!--<button class="jedInput" onclick="cf.reload()">Refresh</button>-->
								<?php if (! $this->_tpl_vars['pdpdustaff']): ?>
								<?php echo $this->_tpl_vars['sConsultation']; ?>

								<?php endif; ?>
								<!-- commented out by Jane - 10/17/2013
								<button class="jedInput" id="updateprofile" style="margin-left:8px" disabled="disabled" onclick="profileShow(0)">New profile</button>
								-->
								<button class="jedInput" id="intake" style="margin-left:8px" onclick="showProfileForm();">Profile Intake</button>
                                <!--<a target="_blank" class="jedInput" id="updateprofile" style="margin-left:8px" onclick="showProfile()" disabled="%= bit>">New Profile</a>-->
							</td>
						</tr>
					</table>
				</div>
				<?php echo $this->_tpl_vars['sProfileList']; ?>

				<!-- <div id="classification"></div> -->
			</td>
			<!--End Cherry-->
		</tr>
	</table>
</div>
<div class="segPanel" id="consultation_dialog" style="display:none" align="center">
    <div align="center" style="overflow:hidden">
        <table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr id="app_discount" align="center">
                <td width="20%" nowrap="nowrap" ><strong>Apply Full Discount?</strong></td>
            </tr>
        </table>
     </div>
</div> 

<div align="center" style="margin-top:10px;width:99%">
	<table width="100%" border="0" cellspacing="5" cellpadding="0">
		<tr>
			<td width="50%" valign="top">
				<div id="classify" class="dashlet" align="left" style="width:100%">
					<table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="99%" nowrap="nowrap"><h1>Classification type</h1></td>
							<td nowrap="nowrap">
								<!-- edited by art 08/28/2014 (pdpu)-->
								<input type="hidden" id="pdpdustaff" name="pdpdustaff" value="<?php echo $this->_tpl_vars['pdpdustaff']; ?>
">
                                <?php if ($this->_tpl_vars['show_billbtn']): ?>
                                	<!-- <?php if (! $this->_tpl_vars['pdpdustaff']): ?> -->
                                	<button class="jedInput" id="show_billing_with_discount" style="margin-left:10px" disabled="disabled" onclick="showBillWithAllDiscounts()">Show Bill With Discount</button>
                                	<!-- <?php endif; ?> -->
								    <button class="jedInput" id="show_billing" style="margin-left:10px" disabled="disabled" onclick="js_showBillDetails()">Show billing</button>
								<?php endif; ?>
								<!-- <?php if (! $this->_tpl_vars['pdpdustaff']): ?> -->
                                <button class="jedInput" id="show" >Classify patient</button>
                                <!-- <?php endif; ?> -->
                                <!-- end art  -->
								<!--<button class="jedInput" onclick="cf.reload()">Refresh</button>-->

								<button class="jedInput" id="btn-pmrf-cf1" onclick="editPmrfCf1()">CF1 | PMRF</button>
							</td>
						</tr>
					</table>
				</div>
				<?php echo $this->_tpl_vars['sClassificationList']; ?>

				<!-- <div id="classification"></div> -->
			</td>
			<td valign="top">
				<div id="requests" class="dashlet" align="left" style="width:100%">
					<table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="99%" nowrap="nowrap"><h1>List of current requests</h1></td>
							<td nowrap="nowrap" id="lingap_row" style="display:none">
								<?php echo $this->_tpl_vars['lingapReport']; ?>

							</td>
                            
							<td nowrap="nowrap" id="applybill_row" style="display: none;">
                                <button class="jedInput" id="discardDiscount" disabled="disabled" onclick="discardBillDiscount();">Omit Fixed Discount</button>
								<?php if (! $this->_tpl_vars['isPayWard']): ?>
								<button class="jedInput" id="applyDiscount" disabled="disabled" onclick="applyBillDiscount();">Apply Billing Discount</button>
								<?php endif; ?>
							</td>
                            
							<!--<td nowrap="nowrap">
								<button class="jedInput" onclick="rlst.reload()">Refresh</button>
							</td>-->
						</tr>
					</table>
				</div>
				<?php echo $this->_tpl_vars['sRequestList']; ?>

			</td>
		</tr>
	</table>
		<!--<table width="100%">
			<tr>
				<td width="20%" id="clstype">
					<span id="cType"></span>&nbsp;&nbsp;&nbsp;
					<span id="cDate"></span>&nbsp;&nbsp;&nbsp;
					<span id="cBy"></span>
				</td>
			</tr>
		</table>
		<table class="jedList" border="1" cellpadding="0" cellspacing="0" width="100%" style="width:100%;margin-bottom:10px">
			<thead>
				<tr>
					<th>Personal Circumtances</th>
					<th>Community Situation</th>
					<th>Nature of Illnes/Disease</th>
				</tr>
			</thead>
			<tbody id="ssctbody">
			</tbody>
		</table>-->
</div>


<div id="dialogMsg" title="Show Bill With Discount" style="display: none;">
    <table id="dialogCollection"><tbody></tbody></table>
</div>

<!--
<div align="center" style="margin-top:5px;width:95%">
	<table width="80%" border="0" cellspacing="5" cellpadding="0">
			<td valign="top">
			</td>
		</tr>
	</table>
</div>
-->
<!--
<tr><td>
	<table width="88%" cellpadding="1" cellspacing="1">
		<tbody>
		<tr>
			<td align="left"><b>Social Service Classification</b></td>
			<td align="right" colspan="4"><button id="show">Classfiy patient</button></td>
		</tr>
		<tr>
			<td colspan="3">
					<div id="sscdiv">
						<table id="ssctable" width="100%" border="1" >
							<thead>
								<tr class="segPanelHeader">
									<th width="50%" align="left">Code</th>
									<th width="20%" >Classified by</th>
									<th width="20%">Data classified</th>
								</tr>
							</thead>
							<tbody id="ssctbody">
							</tbody>
						</table>
				</div>
			</td>
		</tr>

		<tr><td>
					<div align=\"left\"	class=\"listControls\">
					<h1>C2</h1>
				</div>
				<div id="sscdiv">
						<table id="ssctable" width="100%" border="1" >
							<thead>
								<tr class="segPanelHeader">
									<th width="30%">Personal Circumstances</th>
									<th width="30%" >Community Situation</th>
									<th width="30%">Nature of Illness/Disease</th>
								</tr>
							</thead>
							<tbody id="ssctbody">
							</tbody>
						</table>
				</div>
				</td>
		</tr>
		</tbody>
	</table>
</td></tr>

<tr><td>
-->

<!-- classify patient -->
<!-- action="Javascript:void(null);" action="$('thisfile').value" onsubmit="submitSSCForm();-->
<br />
<br />

<?php echo $this->_tpl_vars['sTailScripts']; ?>

<?php echo $this->_tpl_vars['sTailScripts2']; ?>

<!--</form>-->