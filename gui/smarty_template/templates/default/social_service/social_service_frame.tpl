{{* Template for medocs (medical diagnosis/therapy record) *}}
{{* Note: the input tags are left here in raw form to give the GUI designer freedom to change  the input dimensions *}}
{{* Note: be very careful not to rename nor change the type of the input  *}}

{{*if $bSetAsForm*}}
{{*$sDocShotcuts*}}
{{*$sDocsJavaScript*}}
<div style="width:100%">
	<table border="0" cellpadding="2" cellspacing="2" width="100%">
		<tbody>
			<tr>
				<td class="segPanelHeader" colspan="2">Patient Information</td>
			</tr>
			<tr>
				<td class="segPanel" id="hpid" width="30%"><strong>Health Record Number</strong></td>
				<td class="segPanel" id="spid">{{$sPid}}</td>
			</tr>
			<tr>
				<td class="segPanel" id="hmss_no"><strong>MSS Number</strong></td>
				<td class="segPanel" id="smss_no">{{$sMss_no}}</td>
			</tr>
		</tbody>
	</table>
	<br>
	<table border="0" cellpadding="2" cellspacing="2" width="100%">
		<tbody>
			<tr>
				<td class="segPanelHeader">Admitting Diagnosis</td>
			</tr>
			<tr>
				<td class="segPanel" id="admitting_diagnosis"></td>
			</tr>
		</tbody>
	</table>
	<br>
	<button id="updateprofile">Update profile</button>
	<table border="0" cellpadding="2" cellspacing="2" width="100%">
		<tbody>
			<tr>
				<td class="segPanelHeader" colspan="3">Profile</td>
			</tr>	
			<tr>
				<td class="segPanel" width="30%"><strong>Informant</strong></td>
				<td class="segPanel" id="respondent"></td>
				<input type="hidden" id="h_respondent" name="h_respondent" value="" />
			</tr>
			<tr>
				<td class="segPanel"><strong>Relation to Patient</strong></td>
				<td class="segPanel" id="relation_patient"></td>
				<input type="hidden" id="h_relation_patient" name="h_relation_patient" value="" />
			</tr>
			<tr>
				<td class="segPanel"><strong>Education</strong></td>
				<td class="segPanel" id="occupation"></td>
				<input type="hidden" id="h_occupation" name="h_occupation" value="" />
			</tr>
			<tr>
				<td class="segPanel"><strong>Number of Children</strong></td>
				<td class="segPanel" id="nrchldren"></td>
				<input type="hidden" id="h_nrchldren" name="h_nrchldren" value="" />
			</tr>
			<tr>
				<td class="segPanel"><strong>Number of Dependents</strong></td>
				<td class="segPanel" id="nrdep"></td>
				<input type="hidden" id="h_nrdep" name="h_nrdep" value="" />
			</tr>
			<tr>
				<td class="segPanel"><strong>Source of Income</strong></td>
				<td class="segPanel" id="source_income"></td>
				<input type="hidden" id="h_source_income" name="h_source_income" value="" />
			</tr>
			<tr>
				<td class="segPanel"><strong>Monthly Income</strong></td>
				<td class="segPanel" id="monthly_income"></td>
				<input type="hidden" id="h_monthly_income" name="h_monthly_income" value="" />
			</tr>
			<tr>
				<td class="segPanel"><strong>Per Capita Income</strong></td>
				<td class="segPanel" id="capita_income"></td>
				<input type="hidden" id="h_capita_income" name="h_capita_income" value="" />
			</tr>
			<tr>
				<td class="segPanel"><strong>Monthly Expenses</strong></td>
				<td class="segPanel" id="monthly_expenses"></td>
				<input type="hidden" id="h_monthly_expenses" name="h_monthly_expenses" value="" />
			</tr>
			
		</tbody>
	</table>
	
	<div id="classify">
		<h3>Classification type</h3>	
		<button id="show">Classify patient</button>
	</div>
	
	<!--added by VAN 06-25-08 -->
	<!--
	<input type="checkbox" name="checkShow" id="checkShow" value="1" onclick="showAllSS();" />
	<strong>Show All Classification</strong>
	-->
	<!-- -->
	<!--<br /><br />-->
	<div id="classification">	
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
	<!--added by VAN 06-25-08 -->
	<!--
	<div id="classification_prev" style="display:none">
		
	</div>
	-->
	<!-- -->
</div>
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
<br />

<!-- added by VAN -->
<div id="rqbilllistdiv" align="left" style="display:none">
<table width="100%" style="display:none">
<span style="font-weight:bold" class="segPanel">List of Current Billing</span>
	<table id="rqbilllisttable" width="88%" class="segList" border="0">
		<thead>
			<tr>
				<th colspan="5" align="left">Billing Statement</th>
				<!--<th width="25%">Billing Date and Time</th>
				<th width="20%">Billed Amount</th>
				<th>&nbsp;</th>
				-->
				<th align="center" width="20%">Details</th>
			</tr>
		</thead>
		<tbody id="rqbillisttbody">
			<!--added by VAN 05-09-08 -->
			<!--
			<tr>
				<td colspan="6">No requests available at this time.</td>
			</tr>
			-->
		</tbody>
	</table>
</table>
</div>
<!-- -->

<br>
<table width="100%">
<div id="rqlistdiv" align="left" style="display:''">
<span style="font-weight:bold" class="segPanel">List of Current Request</span>
	<table id="rqlisttable" width="88%" class="segList" border="0">
		<thead>
			<tr>
				<th width="25%">Batch No.</th>
				<th width="25%">Date Requested</th>
				<th width="25%">Department</th> 
				<th width="20%">Total Charge</th>
				<th width="5%">&nbsp;</th>
				<th width="20">Discount</th>
			</tr>
		</thead>
		<tbody id="rqlisttbody">
			<!--added by VAN 05-09-08 -->
			<!--
			<tr>
				<td colspan="6">No requests available at this time.</td>
			</tr>
			-->
		</tbody>
	</table>
</div>
</table>

<!-- classify patient -->
<!-- action="Javascript:void(null);" action="$('thisfile').value" onsubmit="submitSSCForm();-->
<div id="dialog1" style="width:60%">
<div class="hd" style="width:100%">Select social service classification...</div>
<div class="bd" style="width:100%">
<form id="enSScode" action="Javascript:void(null);" >
	<table width="380px" class="segPanel">
		<tr>
		  <td width="35%" align="left"><span style=" color:#FF0000">*</span><b>Code:</b></td>
		  <td align="left"><span>	 
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
		<tr id="subclass" style="display:none">
		  <td align="left"><span style=" color:#FF0000">*</span><b>Sub Classification:</b></td>
		  <td align="left"><span>	 
			<!--<select name="subservice_code" id="subservice_code" onchange="">-->
			<select name="subservice_code" id="subservice_code" onchange="xajax_OnChangeSubOptions(this.value);">
				<option value="0">-Select Code-</option>
			</select>
			
		  </td>
		</tr>
		<tr id="subID" style="display:none">
		  <td align="left"><span style=" color:#FF0000">*</span><b>ID No.:</b></td>
		  <td align="left"><span>	 
			<!--<select name="subservice_code" id="subservice_code" onchange="">-->
			<input type="text" name="idnumber" id="idnumber" value="" size="35">
			
		  </td>
		</tr>
		<!-- -->
		<!--added by VAN 08-05-08 -->
		<tr id="other_text" style="display:none">
		  <td align="left"><span style=" color:#FF0000">*</span><b>Other Classification:</b></td>
		  <td align="left"><span>	 
				<input type="text" name="subservice_code2" id="subservice_code2" value="" size="35">
		  </td>
		</tr>
		<!-- -->
		
		<tr id="personalMod" style="display:none">	
			<td align="left" valign="top"><b>Re: Personal Circumstances</b></td>
			<!--<td><textarea id="personal_circumstance" name="personal_circumstance" rows="2" cols="33"></textarea></td>-->
			<td align="left"><span>	 
			<select name="personal_circumstance" id="personal_circumstance" onchange="">
				<option value="0" >-Select Personal Circumstances-</option>
			</select>
			</td>
		</tr>
		<tr id="communityMod" style="display:none">	
			<td align="left" valign="top"><b>Re: Community Situations</b></td>
			<!--<td><textarea id="community_situation" name="community_situation" rows="2" cols="33"></textarea></td>-->
			<td align="left"><span>	 
			<select name="community_situation" id="community_situation" onchange="">
				<option value="0">-Select Community Situations-</option>
			</select>
			</td>
		</tr>
		<tr id="diseaseMod" style="display:none">	
			<td align="left" valign="top"><b>Re: Nature of Illness/Disease</b></td>
			<!--<td><textarea id="nature_of_disease" name="nature_of_disease" rows="2" cols="33"></textarea></td>-->
			<td align="left"><span>	 
			<select name="nature_of_disease" id="nature_of_disease" onchange="">
				<option value="0">-Select Nature of Illness-</option>
			</select>
			</td>
		</tr>
	</table>
	{{$sHiddenInputs}}	
</form>
</div>
</div>


<!-- Update profile -->
<div id="dialog2" style="width:80%">
<div class="hd" style="width:100%">Update Profile</div>
<div clas="bd" style=" width:100%">
	<form id="frmupdate" action="Javascript:void(null);">
		<table width="523" class="segPanel" align="center">
			<tr>
				<td width="18%" height="20px">Informant:</td>
				<td width="29%">
			  <input type="text" id="resp" name="resp" value="" /></td>
				<td width="24%" height="20px" colspan="2">Monthly Expenses:</td>
			</tr>
			<tr>
				<td>Relation to Patient :</td>
			   <td><input type="text" id="relation" name="relation" value="" /></td>
				<td>House and Lot:</td>
				<td width="29%">Php&nbsp;<input type="text" id="hauz_lot2" onblur="assignHauz(); formatValue(this, 2); computeTotal();" name="hauz_lot2" value="" class="text input_mask mask_date_us" style="text-align:right" size="15" />
												<input type="hidden" id="hauz_lot" name="hauz_lot" value=""/></td>
			</tr>
			<tr>
				<td>Educational Attainment :</td>
				<td><!--
						<select id="occupation_select" name="occupation_select" >
									<option value="0">Not Indicated</option>
								</select>
						-->
						<select id="occupation_select" name="occupation_select" >
							<option value="0">Not Indicated</option>
						</select>			
				</td>
				<td>Food :</td>
				<td>Php&nbsp;<input type="text" id="food2" name="food2" value="" onblur="assignFood(); formatValue(this, 2); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15"/>
								<input type="hidden" id="food" name="food" value=""/></td>
			</tr>
			<tr>
				<td>Number of Dependents :</td>
				<td><input type="text" id="nr_dep" name="nr_dep" value="" onBlur="computeCapita();" /></td>
				<td>Light :</td>
				<td>Php&nbsp;<input type="text" id="light2" name="light2" value="" onblur="assignLight(); formatValue(this, 2); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15" />
				             <input type="hidden" id="light" name="light" value=""/></td>
			</tr>
			<tr>
				<td>Number of Children :</td>
				<td><input type="text" id="nr_chldren" name="nr_chldren" value=""/></td>
				<td>Water :</td>
				<td>Php&nbsp;<input type="text" id="water2" name="water2" value="" onblur="assignWater(); formatValue(this, 2); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15" />
								<input type="hidden" id="water" name="water" value=""/></td>
			</tr>
			<tr>
				<td>Source of Income :</td>
				<td><input type="text" id="s_income" name="s_income" value=""/></td>
				<td>Transportation :</td>
				<td>Php&nbsp;<input type="text" id="transport2" name="transport2" value="" onblur="assignTransport(); formatValue(this, 2); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15" />
				             <input type="hidden" id="transport" name="transport" value=""/></td>
			</tr>
			<tr>
				<td>Monthly Income :</td>
				<td><input type="text" id="m_income2" name="m_income2" value="" onblur="assignM_income(); computeCapita(); formatValue(this, 2);" class="text input_mask mask_date_us" />
					 <input type="hidden" id="m_income" name="m_income" value=""/>
				</td>
				<td>Other :</td>
				<td>Php&nbsp;<input type="text" id="other2" name="other2" value="" onblur="assignOther(); formatValue(this, 2); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15" />
				             <input type="hidden" id="other" name="other" value=""/></td>
			</tr>
			<tr>
				<td>Per Capita Income :</td>
				<td><input type="text" id="m_capita_income" name="m_capita_income" value="" readonly="1" class="text input_mask mask_date_us" style="text-align:right" />
					 <input type="hidden" id="m_cincome" name="m_cincome" value=""/>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" align="right">Total Monthly Expenditure :</td>
				<td>Php&nbsp;<input type="text" id="m_expenses" name="m_expenses" value="" readonly="1" class="text input_mask mask_date_us" style="text-align:right" size="15" /></td>
			</tr>
			
	  </table>
		{{$sHiddenInputsB}}	
	</form>
</div>
</div>



{{$sTailScripts}}
{{$sTailScripts2}}
<!--</form>-->
{{*/if*}}