<?php /* Smarty version 2.6.0, created on 2018-04-24 13:13:01
         compiled from registration_admission/admit_form.tpl */ ?>

	<?php if ($this->_tpl_vars['bSetAsForm']): ?>
	<form method="post" action="<?php echo $this->_tpl_vars['thisfile']; ?>
" name="aufnahmeform" id="aufnahmeform" onSubmit="return false;">
	<?php endif; ?>

		<table border="0" cellspacing=1 cellpadding=0 width="100%">

		<?php if ($this->_tpl_vars['error']): ?>
				<tr>
					<td colspan=4 class="warnprompt">
						<center>
						<?php echo $this->_tpl_vars['sMascotImg']; ?>

						<?php echo $this->_tpl_vars['LDError']; ?>

						</center>
					</td>
				</tr>
		<?php endif; ?>

		<?php if ($this->_tpl_vars['is_discharged']): ?>
				<tr>
					<td bgcolor="red" colspan="3">
						&nbsp;
						<?php echo $this->_tpl_vars['sWarnIcon']; ?>

						<font color="#ffffff">
						<b>
						<?php echo $this->_tpl_vars['sDischarged']; ?>

						</b>
						</font>
					</td>
				</tr>
		<?php endif; ?>

				<tr>
					<td  class="adm_item">
						<p><?php echo $this->_tpl_vars['LDCaseNr']; ?>

</p>
					<p>Bar Code 				    </p></td>
					<td class="adm_input">
						<?php echo $this->_tpl_vars['encounter_nr']; ?>

						<br>
						<?php echo $this->_tpl_vars['sEncBarcode']; ?>
 <?php echo $this->_tpl_vars['sHiddenBarcode']; ?>

					</td>
					<td <?php echo $this->_tpl_vars['sRowSpan']; ?>
 align="center" class="photo_id">
						<?php echo $this->_tpl_vars['img_source']; ?>

					</td>
				</tr>

				<tr id="rowDateConsult" style="display:none">
					<td  class="adm_item">
						<?php echo $this->_tpl_vars['LDConsultDate']; ?>
:
					</td>
					<td  class="adm_item">
						<?php echo $this->_tpl_vars['sConsultDate']; ?>

						<?php if ($this->_tpl_vars['sAdmissionBol']): ?>
							<?php echo $this->_tpl_vars['jsCalendarSetup2']; ?>

							<?php echo $this->_tpl_vars['sDateMiniCalendar2']; ?>

						<?php endif; ?>
					</td>
				</tr>
				<tr id="rowTimeConsult" style="display:none">
					<td  class="adm_item">
						<?php echo $this->_tpl_vars['LDConsultTime']; ?>
:
					</td>
					<td  class="adm_item">
						<?php echo $this->_tpl_vars['sConsultTime']; ?>

					</td>
				</tr>

				<tr id="rowDateAdmit">
					<td  class="adm_item" id="rowDate">
						<?php echo $this->_tpl_vars['LDAdmitDate']; ?>
:
					</td>
					<!--commented by VAN 01-21-09 -->
					<!--
					<td class="adm_input">
						<?php if ($this->_tpl_vars['sAdmission']): ?>
							<?php echo $this->_tpl_vars['sAdmitDate2']; ?>

							<?php echo $this->_tpl_vars['sDateMiniCalendar2']; ?>

							<?php echo $this->_tpl_vars['jsCalendarSetup2']; ?>

						<?php else: ?>
							<?php echo $this->_tpl_vars['sAdmitDate']; ?>

						<?php endif; ?>
					</td>
					-->
					<td class="adm_input">
						<?php echo $this->_tpl_vars['sAdmitDate2']; ?>

						<?php echo $this->_tpl_vars['sDateMiniCalendar2']; ?>

						<?php echo $this->_tpl_vars['jsCalendarSetup2']; ?>

					</td>
				</tr>

				<tr id="rowTimeAdmit">
					<td class="adm_item" id="rowTime">
					<?php echo $this->_tpl_vars['LDAdmitTime']; ?>
:
					</td>
					<td class="adm_input">
						<!--edited by VAN 01-21-09 -->
						<?php echo $this->_tpl_vars['sAdmitTime'];  echo $this->_tpl_vars['sAdmitTime2']; ?>

					</td>
				</tr>
				<!--commented by VAN 01-21-09 -->
				<!--
			<?php if (! $this->_tpl_vars['sAdmission']): ?>

				<tr id="adm_date" style="display:none">
					<td  class="adm_item">
						<?php echo $this->_tpl_vars['LDAdmitDate2']; ?>
:
					</td>
					<td class="adm_input">
						<?php echo $this->_tpl_vars['sAdmitDate2']; ?>

						<?php echo $this->_tpl_vars['sDateMiniCalendar2']; ?>

						<?php echo $this->_tpl_vars['jsCalendarSetup2']; ?>

					</td>
				</tr>

				<tr id="adm_time" style="display:none">
					<td class="adm_item">
					<?php echo $this->_tpl_vars['LDAdmitTime2']; ?>
:
					</td>
					<td class="adm_input">
						<?php echo $this->_tpl_vars['sAdmitTime2']; ?>

					</td>
				</tr>

			<?php endif; ?>
				-->
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDTitle']; ?>
:
					</td>
					<td class="adm_input">
						<?php echo $this->_tpl_vars['title']; ?>

					</td>
				</tr>

				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDLastName']; ?>
:
					</td>
					<td bgcolor="#ffffee" class="vi_data"><b>
						<?php echo $this->_tpl_vars['name_last']; ?>
</b>
					</td>
				</tr>

				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDFirstName']; ?>
:
					</td>
					<td bgcolor="#ffffee" class="vi_data">
						<?php echo $this->_tpl_vars['name_first']; ?>
 &nbsp; <?php echo $this->_tpl_vars['sCrossImg']; ?>

					</td>
				</tr>

			<?php if ($this->_tpl_vars['name_2']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDName2']; ?>
:
					</td>
					<td bgcolor="#ffffee">
						<?php echo $this->_tpl_vars['name_2']; ?>

					</td>
				</tr>
			<?php endif; ?>

			<?php if ($this->_tpl_vars['name_3']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDName3']; ?>
:
					</td>
					<td bgcolor="#ffffee">
						<?php echo $this->_tpl_vars['name_3']; ?>

					</td>
				</tr>
			<?php endif; ?>

			<?php if ($this->_tpl_vars['name_middle']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDNameMid']; ?>
:
					</td>
					<td bgcolor="#ffffee">
						<?php echo $this->_tpl_vars['name_middle']; ?>

					</td>
				</tr>
			<?php endif; ?>

				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDBday']; ?>
:
					</td>
					<td bgcolor="#ffffee" class="vi_data" width="39%">
						<?php echo $this->_tpl_vars['sBdayDate']; ?>
 &nbsp; <?php echo $this->_tpl_vars['sCrossImg']; ?>
 &nbsp;<font color="black"><?php echo $this->_tpl_vars['sDeathDate']; ?>
</font>
					</td>
					<td bgcolor="#ffffee">
						&nbsp;&nbsp;<?php echo $this->_tpl_vars['LDSex']; ?>
: <?php echo $this->_tpl_vars['sSexType']; ?>

					</td>
				</tr>

			<?php if ($this->_tpl_vars['LDBloodGroup']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDBloodGroup']; ?>
:
					</td>
					<td class="adm_input" colspan=2>
						<?php echo $this->_tpl_vars['blood_group']; ?>

					</td>
				</tr>
			<?php endif; ?>

				<tr>
						<td class="adm_item">
								<?php echo $this->_tpl_vars['LDVitalSigns']; ?>

						</td>
						<td class="adm_input" colspan=2>
								<?php echo $this->_tpl_vars['vital_signs']; ?>

						</td>
				</tr>

				<!-- Added Genesis 05/20/14!-->
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDContactNo']; ?>
 :
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sContactNo']; ?>

					</td>
				</tr>
				<!--end added Genesis 05/20/14 !-->

				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDAddress']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['segAddress']; ?>

<!--
						<?php echo $this->_tpl_vars['addr_str']; ?>
  <?php echo $this->_tpl_vars['addr_str_nr']; ?>

						<br>
						<?php echo $this->_tpl_vars['addr_zip']; ?>
 <?php echo $this->_tpl_vars['addr_citytown_name']; ?>

-->
					</td>
				</tr>

				<!--added by CHA, May 21, 2010-->
				<!--<tr id="mother_nr_row" style="display:none">
					 <td class="adm_item">
						<?php echo $this->_tpl_vars['LDMotherNr']; ?>

					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sMotherCaseNr']; ?>

						<?php echo $this->_tpl_vars['sMotherWardNr']; ?>

						<?php echo $this->_tpl_vars['sMotherRoomNr']; ?>

						<?php echo $this->_tpl_vars['sMotherDeptNr']; ?>

						<?php echo $this->_tpl_vars['sMotherSelect']; ?>

					</td>
				</tr> -->
				<!--end CHA, May 21, 2010-->

				<tr>
					<td class="adm_item">
						<font color="red"><?php echo $this->_tpl_vars['LDAdmitClass']; ?>
</font>:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sAdmitClassInput']; ?>

						<?php echo $this->_tpl_vars['sAdmitClassInput2']; ?>

						<?php echo $this->_tpl_vars['sAdmitClassInput3']; ?>

					</td>
				</tr>
			<?php if ($this->_tpl_vars['segORNumber']): ?>
				<tr>
					<td class="adm_item">
						<font color="red"><?php echo $this->_tpl_vars['segORNumber']; ?>
</font>:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sORNumber']; ?>

						<?php echo $this->_tpl_vars['sORTEMP']; ?>

						<?php echo $this->_tpl_vars['sOrDialog']; ?>


					</td>
				</tr>
			<?php endif; ?>
			<!---added 03-07-07---->
			<?php if ($this->_tpl_vars['LDInformant']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDInformant']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['informant_name']; ?>

					</td>
				</tr>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['LDInfoAdd']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDInfoAdd']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['info_address']; ?>

					</td>
				</tr>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['LDInfoRelation']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDInfoRelation']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['relation_informant']; ?>

					</td>
				</tr>
			<?php endif; ?>

			<!--added by VAN 06-13-08 -->
			<?php if ($this->_tpl_vars['segShowIfFromER'] && $this->_tpl_vars['LDTriageCategory']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDTriageCategory']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sCategory']; ?>

					</td>

				</tr>
			<?php endif; ?>
			<!-- -->

			<!-- -->
			<tr>
				<td class="adm_item">
					<?php echo $this->_tpl_vars['LDConfidential']; ?>
:
				</td>
				<td colspan=2 class="adm_input">
					<?php echo $this->_tpl_vars['sConfidential']; ?>

				</td>

			</tr>

			<!-- -->

			<!--added by VAN 04-28-08 -->

			<?php if ($this->_tpl_vars['LDMedico'] && $this->_tpl_vars['segShowIfFromER']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDMedico']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['Medico']; ?>

					</td>

				</tr>
				<tr id="ERMedico">
					<td class="adm_item" width="30%">
						<?php echo $this->_tpl_vars['LDMedicoCases']; ?>

					</td>
					<td colspan=2 class="adm_input">
							<table width="63%" height="84" border="0" cellpadding="1" id="srcMedicoTable" style="width:100%; font-size:12px">
								<td width="36%" height="80" valign="middle" id="leftTdMedico">
									<?php echo $this->_tpl_vars['rowMedicoA']; ?>
					</td>
								<td width="64%" valign="middle" id="rightTdMedico">
									<?php echo $this->_tpl_vars['rowMedicoB']; ?>

																		<?php echo $this->_tpl_vars['sdescription']; ?>

																</td>
								</table>

					</td>

				</tr>

				<!--added by VAN 06-12-08 -->
				<tr id="ERMedicoPOI">
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDPOI']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sPOI']; ?>

					</td>
				</tr>
				<tr id="ERMedicoTOI">
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDTOI']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sTOI']; ?>

					</td>
				</tr>
				<tr id="ERMedicoDOI">
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDDOI']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sDOI']; ?>

						<?php echo $this->_tpl_vars['sDateMiniCalendar']; ?>

						<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

					</td>
				</tr>
				<?php endif; ?>

				<?php if ($this->_tpl_vars['LDDOA'] && $this->_tpl_vars['segShowIfFromER']): ?>
					<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDDOA']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sDOA']; ?>

						&nbsp;&nbsp;
						<?php echo $this->_tpl_vars['sDOAreason']; ?>

					</td>

				</tr>
				<?php endif; ?>
			<!---------------->

			<!--added by VAN 08-20-08-->
			<?php if ($this->_tpl_vars['LDWard']): ?>
				<!--<tr <?php echo $this->_tpl_vars['segERDetailsHideable']; ?>
>-->
				<tr id="mode_assignment" class="ERDetails" style="display">	<!---edited by CHA, 04-29-2010---->
					<td class="adm_item">
						Mode in Room Assignment:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sLDRoomMode']; ?>

					</td>
				</tr>
			<?php endif; ?>
			<!-- -->

				<!---added 03-07-07---->
			<?php if ($this->_tpl_vars['LDWard']): ?>
				<!--<tr <?php echo $this->_tpl_vars['segERDetailsHideable']; ?>
>-->
				<tr id="accomodation_assignment" class="ERDetails" style="display">	<!---edited by CHA, 04-29-2010---->
					<td class="adm_item">
						<font color="red"><?php echo $this->_tpl_vars['LDWard']; ?>
</font>:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sWardInput']; ?>

					</td>
				</tr>
			<?php endif; ?>

			<!------added by VAN 01-31-08 ----------------->

			<?php if ($this->_tpl_vars['LDWard']): ?>
				<!--<tr <?php echo $this->_tpl_vars['segERDetailsHideable']; ?>
>-->
				<tr id="room_assignment" class="ERDetails" style="display"> <!---edited by CHA, 04-29-2010---->
					<td class="adm_item">
						<font color="red"><?php echo $this->_tpl_vars['LDRoom']; ?>
</font>:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sLDRoom']; ?>

					</td>
				</tr>

				<!--<tr style="display:none" id="area_row" <?php echo $this->_tpl_vars['segERDetailsHideable']; ?>
>-->
				<tr id="area_assignment" style="display">	<!---edited by CHA, 04-29-2010---->
					<td class="adm_item">
						<font color="red"><?php echo $this->_tpl_vars['LDArea']; ?>
</font>:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sLDArea']; ?>

					</td>
				</tr>
			<?php endif; ?>

			<!-- added by VAN 08-20-08 -->
			<?php if ($this->_tpl_vars['LDWard']): ?>
				<tr id="datefrom_row" style="display:none" <?php echo $this->_tpl_vars['segERDetailsHideable']; ?>
>
					<td class="adm_item">
						Date and Time (From):
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sLDDateFrom']; ?>

						<?php echo $this->_tpl_vars['sDateMiniCalendar3']; ?>

						<?php echo $this->_tpl_vars['jsCalendarSetup3']; ?>

						&nbsp;&nbsp;
						<?php echo $this->_tpl_vars['sLDTimeFrom']; ?>

					</td>
				</tr>
			<?php endif; ?>
			<!-- -->

			<?php if ($this->_tpl_vars['LDWard']): ?>
				<!--<tr <?php echo $this->_tpl_vars['segERDetailsHideable']; ?>
>-->
				<tr id="bed_assignment" class="ERDetails" style="display">	<!---edited by CHA, 04-29-2010---->
					<td class="adm_item">
						<font color="red"><?php echo $this->_tpl_vars['LDBed']; ?>
</font>:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sLDBed']; ?>

					</td>
				</tr>
			<?php endif; ?>

			<!--------------------------------------------->

				<!----added 02-27-07 -->
				<tr <?php echo $this->_tpl_vars['segERDetailsHideable']; ?>
>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDDoctor']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['doctor_name']; ?>

						<!-- Added by Jarel 03102015 -->
						<table id="order-list-att" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
							<thead>
									<tr id="order-list-header-att">
											<th width="4%" nowrap></th>
											<th width="*" nowrap align="left">&nbsp;&nbsp;Doctor's Name</th>
											<th width="1"></th>
									</tr>
							</thead>
							<tbody>
								<?php echo $this->_tpl_vars['sAttDrItems']; ?>


						</table>
						<!-- End 03102015 -->
					</td>
					<td colspan=2 >
						<?php echo $this->_tpl_vars['doctor_name2']; ?>

					</td>
				</tr>

				<!----added 02-27-07 -->

			<!--<?php if ($this->_tpl_vars['LDDepartment']): ?>-->
				<tr <?php echo $this->_tpl_vars['segERDetailsHideable']; ?>
>
					<td class="adm_item">
						<font color="red"><?php echo $this->_tpl_vars['LDDepartment']; ?>
</font>:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sDeptInput']; ?>

					</td>
				</tr> 
			<!--<?php endif; ?>-->


			<!-- burn added : May 16, 2006 -->
			<?php if ($this->_tpl_vars['segERDiagnosis']): ?>
				<!--<tr class="ERDetails"> -->
								<tr id="diagnosis_assignment" style="display">	<!---edited by CHA, 04-29-2010---->
					<td class="adm_item">
						<?php echo $this->_tpl_vars['segERDiagnosis']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['er_opd_diagnosis']; ?>

					</td>
				</tr>
			<?php endif; ?>

			<?php if ($this->_tpl_vars['segComplaint']): ?>
				<tr id="complaint_assignment" style="display">
					<td class="adm_item">
						<?php echo $this->_tpl_vars['segChiefComplaint']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['chief_complaint']; ?>

					</td>
				</tr>
			<?php endif; ?>

			<?php if ($this->_tpl_vars['segEROPDDr']): ?>
				<tr class="ERDetails">
					<td class="adm_item">
						<?php echo $this->_tpl_vars['segEROPDDr']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sERDrInput']; ?>

						<!-- Added by Jarel 03102015 -->
						<table id="order-list-adm" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
							<thead>
									<tr id="order-list-header-adm">
											<th width="4%" nowrap></th>
											<th width="*" nowrap align="left">&nbsp;&nbsp;Doctor's Name</th>
											<th width="1"></th>
									</tr>
							</thead>
							<tbody>
								<?php echo $this->_tpl_vars['sConDrItems']; ?>


						</table>
						<!-- End 03102015 -->
					</td>
				</tr>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['segEROPDDepartment']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['segEROPDDepartment']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sERDeptInput']; ?>

					</td>
				</tr>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['LDDiagnosis'] && $this->_tpl_vars['segShowIfFromER']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDDiagnosis']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['referrer_diagnosis']; ?>

					</td>
				</tr>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['LDTherapy']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDTherapy']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['referrer_recom_therapy']; ?>

					</td>
				</tr>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['LDRecBy'] && $this->_tpl_vars['segShowIfFromER']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDRecBy']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['referrer_dr_name']; ?>

					</td>
					<!--<td colspan=2>
						<?php echo $this->_tpl_vars['referrer_dept_name']; ?>

					</td> -->
					<td colspan=2>
						<?php echo $this->_tpl_vars['referrer_dr']; ?>

					</td>
					<td colspan=2>
						<?php echo $this->_tpl_vars['name1']; ?>

					</td>
					<td colspan=2>
						<?php echo $this->_tpl_vars['name2']; ?>

					</td>
					<td colspan=2>
						<?php echo $this->_tpl_vars['lname']; ?>

					</td>
					<!--<td colspan=2>
						<?php echo $this->_tpl_vars['referrer_dept']; ?>

					</td>-->
				</tr>
			<?php endif; ?>

			<?php if ($this->_tpl_vars['LDRecDept'] && $this->_tpl_vars['segShowIfFromER']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDRecDept']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['referrer_dept_name']; ?>

					</td>
					<td colspan=2>
						<?php echo $this->_tpl_vars['referrer_dept']; ?>

					</td>
				</tr>
			 <?php endif; ?>

			 <?php if ($this->_tpl_vars['LDRecIns'] && $this->_tpl_vars['segShowIfFromER']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDRecIns']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['referrer_institution']; ?>

					</td>
				</tr>
			 <?php endif; ?>
			 <?php if ($this->_tpl_vars['LDSpecials'] && $this->_tpl_vars['segShowIfFromER']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDSpecials']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['referrer_notes']; ?>

					</td>
				</tr>
			 <?php endif; ?>
				<!-- The insurance class  -->
			 <?php if ($this->_tpl_vars['LDBillType']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDBillType']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sBillTypeInput']; ?>
&nbsp;&nbsp;<span name="iconIns" id="iconIns" style="display:none"><?php echo $this->_tpl_vars['sBtnAddItem']; ?>
</span>
					</td>
					<!--<td><?php echo $this->_tpl_vars['sBtnAddItem']; ?>
</td>-->
				</tr>
			 <?php endif; ?>
				<!-- edited 03-06-07------------->

			 <?php if ($this->_tpl_vars['LDInsuranceNr']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDInsuranceNr']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<!--<?php echo $this->_tpl_vars['insurance_nr']; ?>
-->
						<!-- -->

						<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
							<thead>
									<tr id="order-list-header">
											<th width="4%" nowrap></th>
											<th width="*" nowrap align="left">&nbsp;&nbsp;Insurance Company</th>
											<th width="20%" nowrap align="right">&nbsp;&nbsp;Insurance No.</th>
											<th width="18%" nowrap align="right">&nbsp;&nbsp;Principal Holder</th>
											<th width="1"></th>
									</tr>
							</thead>
							<tbody>
								<?php echo $this->_tpl_vars['sOrderItems']; ?>


						</table>

						<!-- -->
					</td>

				</tr>
				<?php endif; ?>

				<!--
				<?php if ($this->_tpl_vars['LDInsuranceCo']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDInsuranceCo']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['insurance_firm_name']; ?>

					</td>
				</tr>
				<?php endif; ?>
				-->
				<!-- edited 03-06-07------------->
			<?php if ($this->_tpl_vars['LDCareServiceClass']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDCareServiceClass']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sCareServiceInput']; ?>
 <?php echo $this->_tpl_vars['LDFrom']; ?>
 <?php echo $this->_tpl_vars['sCSFromInput']; ?>
 <?php echo $this->_tpl_vars['LDTo']; ?>
 <?php echo $this->_tpl_vars['sCSToInput']; ?>
 <?php echo $this->_tpl_vars['sCSHidden']; ?>

					</td>
				</tr>
			<?php endif; ?>

			<?php if ($this->_tpl_vars['LDRoomServiceClass']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDRoomServiceClass']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sCareRoomInput']; ?>
 <?php echo $this->_tpl_vars['LDFrom']; ?>
 <?php echo $this->_tpl_vars['sRSFromInput']; ?>
 <?php echo $this->_tpl_vars['LDTo']; ?>
 <?php echo $this->_tpl_vars['sRSToInput']; ?>
 <?php echo $this->_tpl_vars['sRSHidden']; ?>

					</td>
				</tr>
			<?php endif; ?>

			<?php if ($this->_tpl_vars['LDAttDrServiceClass']): ?>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDAttDrServiceClass']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sCareDrInput']; ?>
 <?php echo $this->_tpl_vars['LDFrom']; ?>
 <?php echo $this->_tpl_vars['sDSFromInput']; ?>
 <?php echo $this->_tpl_vars['LDTo']; ?>
 <?php echo $this->_tpl_vars['sDSToInput']; ?>
 <?php echo $this->_tpl_vars['sDSHidden']; ?>

					</td>
				</tr>
			<?php endif; ?>

				<!-----added 03-08-07------------->
				<?php if ($this->_tpl_vars['LDCondition'] && $this->_tpl_vars['segShowIfFromER']): ?>
				<tr class="ERDetails">
					<td class="adm_item">
						<font color="red"><?php echo $this->_tpl_vars['LDCondition']; ?>
</font>:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sCondition']; ?>

					</td>
				</tr>
				<?php endif; ?>
				<?php if ($this->_tpl_vars['LDResults'] && $this->_tpl_vars['segShowIfFromER']): ?>
				<tr class="ERDetails">
					<td class="adm_item">
						<font color="red"><?php echo $this->_tpl_vars['LDResults']; ?>
</font>:
					</td>
					<td colspan=2 class="adm_input">
						<!--edited by VAN 02-27-08 -->
						<?php if ($this->_tpl_vars['segAdmissionShow']): ?>
							<?php echo $this->_tpl_vars['sResults']; ?>

						<?php else: ?>
							<table width="63%" height="84" border="0" cellpadding="1" id="srcResultTable" style="width:100%; font-size:12px">
								<td width="36%" height="80" valign="middle" id="leftTdResult">
									<?php echo $this->_tpl_vars['rowResultA']; ?>
					</td>
								<td width="64%" valign="middle" id="rightTdResult">
									<?php echo $this->_tpl_vars['rowResultB']; ?>
					</td>
								</table>
						<?php endif; ?>
					</td>

				</tr>
				<?php endif; ?>
				<?php if ($this->_tpl_vars['LDDisposition'] && $this->_tpl_vars['segShowIfFromER']): ?>
				<tr class="ERDetails">
					<td class="adm_item">
						<font color="red"><?php echo $this->_tpl_vars['LDDisposition']; ?>
</font>:
					</td>
					<td colspan=2 class="adm_input">
						<!--edited by VAN 02-27-08 -->
						<?php if ($this->_tpl_vars['segAdmissionShow']): ?>
							<?php echo $this->_tpl_vars['sDisposition']; ?>

						<?php else: ?>
						<table width="63%" height="84" border="0" cellpadding="1" id="srcResultTable" style="width:100%; font-size:12px">
							<td width="36%" height="80" valign="middle" id="leftTdResult">
								<?php echo $this->_tpl_vars['rowDispositionA']; ?>
					</td>
							<td width="64%" valign="middle" id="rightTdResult">
								<?php echo $this->_tpl_vars['rowDispositionB']; ?>
					</td>
							</table>
						<?php endif; ?>
					</td>
				</tr>
				<?php endif; ?>

                <!-- added by VAN 10-12-2011 -->
                <?php if ($this->_tpl_vars['LDSmokers']): ?> 
                <tr>
                    <td class="adm_item">
                        <?php echo $this->_tpl_vars['LDSmokers']; ?>
:
                    </td>
                    <td colspan=2 class="adm_input">
                        <?php echo $this->_tpl_vars['sSmokersInput']; ?>

                    </td>
                </tr>
                <?php endif; ?>
                <?php if ($this->_tpl_vars['LDDrinker']): ?>
                <tr>
                    <td class="adm_item">
                        <?php echo $this->_tpl_vars['LDDrinker']; ?>
:
                    </td>
                    <td colspan=2 class="adm_input">
                        <?php echo $this->_tpl_vars['sDrinkerInput']; ?>

                    </td>
                </tr>
                <?php endif; ?>
                <!-- -->
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDAdmitBy']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['encoder']; ?>

					</td>
				</tr>
				<tr>
					<td class="adm_item">
						<?php echo $this->_tpl_vars['LDDeptBelong']; ?>
:
					</td>
					<td colspan=2 class="adm_input">
						<?php echo $this->_tpl_vars['sDeptBelong']; ?>

					</td>
				</tr>

				<!-------------------------------->

				<?php echo $this->_tpl_vars['sHiddenInputs']; ?>


				<tr>
					<td colspan="3">&nbsp;

					</td>
				</tr>
				<tr>
					<td>
						<?php echo $this->_tpl_vars['pbSave']; ?>

					</td>
					<td align="right">
						<?php echo $this->_tpl_vars['pbRefresh']; ?>
 <?php echo $this->_tpl_vars['pbRegData']; ?>

					</td>
					<td align="right">
						<?php echo $this->_tpl_vars['pbCancel']; ?>
					</td>
				</tr>

		</table>

			<?php echo $this->_tpl_vars['sErrorHidInputs']; ?>

			<?php echo $this->_tpl_vars['sUpdateHidInputs']; ?>

			 <?php echo $this->_tpl_vars['isWellBaby']; ?>
 <!--added by CHA, April 29,2010 -->
	<?php if ($this->_tpl_vars['bSetAsForm']): ?>
	</form>

	<p><?php endif; ?>

<?php echo $this->_tpl_vars['sNewDataForm']; ?>
</p>
	<p>&nbsp;</p>
	<p>