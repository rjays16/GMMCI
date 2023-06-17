{{*created by cha 05-20-2009*}}
{{$sFormStart}}
		<div style="padding:10px;width:95%;border:0px solid black">
		<font class="warnprompt"><br></font>
		 <table cellpadding="2" cellspacing="2" border="0">
				<tbody>				
				<tr>
						<td class="segPanelHeader" width="10%" nowrap="nowrap" align="left">Last name</td>
						<td class="segPanel"><input type="text" size="25" id="donor_lname"></input></td>
				</tr>
				<tr>
						<td class="segPanelHeader" width="10%" nowrap="nowrap" align="left">First name</td>
						<td class="segPanel"><input type="text" size="25" id="donor_fname"></input></td>
				</tr> 
				<tr>
						<td class="segPanelHeader" width="10%" nowrap="nowrap" align="left">Middle name</td>
						<td class="segPanel"><input type="text" size="25" id="donor_mname"></input></td>
				</tr>
				<tr>
						<td class="segPanelHeader" width="10%" nowrap="nowrap" align="left">Birthdate</td>
						<td class="segPanel">{{$sDonorBirthDate}}{{$sDonorBirthDateIcon}}{{$jsCalendarSetup}} 
							<input type="text" size="5" id="donor_age" onfocus="computeAge()" onclick="computeAge()"> year(s) old
						</td>
				</tr> 
				<tr>
					 <td class="segPanelHeader" width="10%" nowrap="nowrap" align="left">Sex</td>
					 <td class="segPanel"><input type="radio" name="donor_sex" id="donor_sex" value="M">Male
								<input type="radio" name="donor_sex" id="donor_sex" value="F">Female
								</td>
				</tr>  
				<tr>
					 <td class="segPanelHeader" width="10%" nowrap="nowrap" align="left">Blood Type</td>
					 <td class="segPanel"><input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="A">A
								<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="B">B
								<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="AB">AB
								<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="O">O
								</td>
				</tr>
				<tr>
					 <td class="segPanelHeader" width="10%" nowrap="nowrap" align="left">Civil Status</td>
					 <td class="segPanel"><input type="radio" name="donor_civilstat" id="donor_civilstat" value="Single">Single
								<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Married">Married
								<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Divorced">Divorced
								<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Widowed">Widowed
								<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Separated">Separated
								</td>
				</tr>
		 </tbody>
		</table> 
		<table class="segPanel" cellpadding="2" cellspacing="2" border="0">
				<tr>
						<td width="10%" nowrap="nowrap" align="left">House No./Street</td>
						<td><input type="text" size="25" id="donor_street"></input></td>
				</tr>
				<tr>
						<td width="10%" nowrap="nowrap" align="left">Barangay's Name</td>
						<td class="yui-skin-sam">
								<div id="barangay_autocomplete"> 
										<input type="text" size="25" name="donor_brgy" id="donor_brgy"/>
										<input type="hidden" id="donor_brgy_nr" name="donor_brgy_nr"/>
										<div id="barangay_container"></div>
								</div>
						</td>
				</tr>
				<tr>
						<td width="10%" nowrap="nowrap" align="left">Municipality's Name</td>
						<td class="yui-skin-sam">
						<div id="municipality_autocomplete">
										<input type="text" size="25" name="donor_mun" id="donor_mun"/>
										<input type="hidden" id="donor_mun_nr" name="donor_mun_nr"/>
										<div id="municipality_container"></div>
						</div>
						</td>
				</tr>
				<tr>
						<td>
								<input height="23" border="0" align="absmiddle" width="72" type="image" alt="Save data" src="../../gui/img/control/default/en/en_savedisc.gif" name="save" id="save" onclick="startAJAXSave(); return false;"/>
						</td>
						<td>
								<a href ="javascript:window.parent.location.reload();"><input height="23" border="0" align="absmiddle" width="72" type="image" alt="Cancel" src="../../gui/img/control/default/en/en_cancel.gif" name="cancel" id="cancel"/></a>
						</td>
				</tr>
			</table>
			 
</div>  

{{$sFormEnd}} 
{{$sTailScripts}}