<?php /* Smarty version 2.6.0, created on 2019-06-24 16:58:46
         compiled from registration_admission/reg_form.tpl */ ?>
				<?php echo $this->_tpl_vars['sRegFormJavaScript']; ?>


				<?php if ($this->_tpl_vars['error'] || $this->_tpl_vars['errorDupPerson']): ?>
			<table border=0 cellspacing=0 cellpadding=0 <?php echo $this->_tpl_vars['sFormWidth']; ?>
>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "registration_admission/reg_error_duplicate.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</table>
		<?php endif; ?>

				<?php echo $this->_tpl_vars['pretext']; ?>


		<?php if ($this->_tpl_vars['bSetAsForm']): ?>
		<!--<form method="post" action="<?php echo $this->_tpl_vars['thisfile']; ?>
" id="aufnahmeform" name="aufnahmeform" ENCTYPE="multipart/form-data" onSubmit="return chkform(this)">-->
		<form method="post" action="<?php echo $this->_tpl_vars['thisfile']; ?>
" id="aufnahmeform" name="aufnahmeform" ENCTYPE="multipart/form-data"  onSubmit="return false;">
		<?php endif; ?>
<!-- edited by VAN 02-11-08-->
		<table border=0 cellspacing=0 cellpadding=0 <?php echo $this->_tpl_vars['sFormWidth']; ?>
 style="margin-top:10px">
				<tr>
					<td class="reg_item">
						<?php echo $this->_tpl_vars['LDRegistryNr']; ?>

					</td>
					<td class="reg_input">
						<b><font size="+2"><?php echo $this->_tpl_vars['pid']; ?>
</font></b>

						<?php echo $this->_tpl_vars['sBarcodeImg']; ?>

					</td>

					<td <?php echo $this->_tpl_vars['sPicTdRowSpan']; ?>
 class="photo_id" align="center">
						<a href="#"  onClick="showpic(document.aufnahmeform.photo_filename)"><img <?php echo $this->_tpl_vars['img_source']; ?>
 name="headpic"></a>
						<br>
						<?php echo $this->_tpl_vars['sFileBrowserInput']; ?>

												<br>
												 <?php echo $this->_tpl_vars['sFileBrowserUpload']; ?>

					</td>

				</tr>

				<tr>
					<td  class="reg_item">
						<?php echo $this->_tpl_vars['LDRegDate']; ?>

					</td>
					<td class="reg_input">
						<!--<span style="color:#800000"><?php echo $this->_tpl_vars['sRegDate']; ?>
</span>-->
						<?php echo $this->_tpl_vars['sRegDate']; ?>

						<?php echo $this->_tpl_vars['sDateMiniCalendar']; ?>

						<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

					</td>
				</tr>

				<tr>
					<td  class="reg_item">
						<?php echo $this->_tpl_vars['LDRegTime']; ?>

					</td>
					<td class="reg_input">
						<span style="color:#800000"><?php echo $this->_tpl_vars['sRegTime']; ?>
</span>
					</td>
				</tr>

				
				<?php echo $this->_tpl_vars['segProfileType']; ?>


				<tr>
					<td colspan=3 >&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="dashlet">
							<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="*">
										<h1>Personal Details</h1>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				<?php echo $this->_tpl_vars['sPersonTitle']; ?>

				<?php echo $this->_tpl_vars['sNameLast']; ?>

				<?php echo $this->_tpl_vars['sNameFirst']; ?>

				<?php echo $this->_tpl_vars['sSuffix']; ?>

				<?php echo $this->_tpl_vars['sName2']; ?>

				<?php echo $this->_tpl_vars['sName3']; ?>

				<?php echo $this->_tpl_vars['sNameMiddle']; ?>

				<?php echo $this->_tpl_vars['sNameMaiden']; ?>

				<?php echo $this->_tpl_vars['sNameOthers']; ?>


				<tr>
					<td class="reg_item">
						<?php echo $this->_tpl_vars['LDBday']; ?>

					</td>
					<td class="reg_input">
						<?php echo $this->_tpl_vars['sBdayInput']; ?>
&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['segAge']; ?>
&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sCrossImg']; ?>
 <?php echo $this->_tpl_vars['sDeathDate']; ?>

					</td>
					<td>&nbsp;<?php echo $this->_tpl_vars['sTempBday']; ?>
</td>
										<!-- added by LST -- 08.30.2009 -- for fingerprint enrollment -->
								<!--
										<td <?php echo $this->_tpl_vars['sPicTdRowSpan']; ?>
 class="photo_id" align="center">
												<a href="#"  onClick="showFPImage(document.aufnahmeform.fpimage_filename)"><img <?php echo $this->_tpl_vars['fpimg_source']; ?>
 name="fpimage"></a>
												<br>
												<?php echo $this->_tpl_vars['sFPImageEnrollment']; ?>

										</td>
							-->
				</tr>

                <!-- added by VAS 08-17-2012 -- for NICU patient, that will automatically copy the birth date and time as admission date -->
                <?php if ($this->_tpl_vars['sIsNewborn']): ?>
                <tr>
                    <td class="reg_item"><?php echo $this->_tpl_vars['LDBirthTime']; ?>
</td>
                    <td class="reg_input"><?php echo $this->_tpl_vars['sBirthTime']; ?>
</td>
                </tr>
                <?php endif; ?>
                <!-- -->

				<tr id="senior_row">
					<td class="reg_item"><?php echo $this->_tpl_vars['LDSenior']; ?>
</td>
					<td class="reg_input"><?php echo $this->_tpl_vars['sSenior']; ?>
</td>
				</tr>
				<tr id="veteran_row">
					<td class="reg_item"><?php echo $this->_tpl_vars['LDVeterans']; ?>
</td>
					<td class="reg_input"><?php echo $this->_tpl_vars['sVeterans']; ?>
</td>
				</tr>

				<tr>
					<td class="reg_item">
						<?php echo $this->_tpl_vars['LDBirthplace']; ?>

					</td>
					<td class="reg_input">
						<?php echo $this->_tpl_vars['sBirthplace']; ?>

					</td>
				</tr>
				<tr>
					<td class="reg_item">
						<?php echo $this->_tpl_vars['LDSex']; ?>

					</td>
					<td class="reg_input">
						<?php echo $this->_tpl_vars['sSexM']; ?>
 <?php echo $this->_tpl_vars['LDMale']; ?>
&nbsp;&nbsp; <?php echo $this->_tpl_vars['sSexF']; ?>
 <?php echo $this->_tpl_vars['LDFemale']; ?>

					</td>
				</tr>

			<?php if ($this->_tpl_vars['LDBloodGroup']): ?>
				<tr>
				<td class="reg_item">
					<?php echo $this->_tpl_vars['LDBloodGroup']; ?>

				</td>
				<td colspan=2 class="reg_input">
					<?php echo $this->_tpl_vars['sBGAInput'];  echo $this->_tpl_vars['LDA']; ?>
  &nbsp;&nbsp; <?php echo $this->_tpl_vars['sBGBInput'];  echo $this->_tpl_vars['LDB']; ?>
 &nbsp;&nbsp; <?php echo $this->_tpl_vars['sBGABInput'];  echo $this->_tpl_vars['LDAB']; ?>
  &nbsp;&nbsp; <?php echo $this->_tpl_vars['sBGOInput'];  echo $this->_tpl_vars['LDO']; ?>

				</td>
				</tr>
			<?php endif; ?>

			<?php if ($this->_tpl_vars['LDCivilStatus']): ?>
				<tr>
				<td class="reg_item">
					<?php echo $this->_tpl_vars['LDCivilStatus']; ?>

				</td>
				<td colspan=2 class="reg_input">
					<!--added by VAN 04-26-08-->
					<?php echo $this->_tpl_vars['sCSChildInput'];  echo $this->_tpl_vars['LDChild']; ?>


					<?php echo $this->_tpl_vars['sCSSingleInput'];  echo $this->_tpl_vars['LDSingle']; ?>

					<?php echo $this->_tpl_vars['sCSMarriedInput'];  echo $this->_tpl_vars['LDMarried']; ?>

					<?php echo $this->_tpl_vars['sCSDivorcedInput'];  echo $this->_tpl_vars['LDDivorced']; ?>

					<?php echo $this->_tpl_vars['sCSWidowedInput'];  echo $this->_tpl_vars['LDWidowed']; ?>

					<?php echo $this->_tpl_vars['sCSSeparatedInput'];  echo $this->_tpl_vars['LDSeparated']; ?>

				</td>
				</tr>
			<?php endif; ?>

			<?php echo $this->_tpl_vars['sReligion']; ?>

			<?php echo $this->_tpl_vars['sEthnicOrig']; ?>

			<?php echo $this->_tpl_vars['sCellPhone1']; ?>

			<?php echo $this->_tpl_vars['sPhone1']; ?>

			<tr>
					<td colspan=3 >&nbsp;</td>
				</tr>
			<tr>
				<td colspan=2>
					<div class="dashlet">
						<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="*">
									<h1><?php echo $this->_tpl_vars['LDAddress']; ?>
</h1>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>

			<?php echo $this->_tpl_vars['segAddressNew']; ?>


			<?php echo $this->_tpl_vars['sPhone2']; ?>

			<?php echo $this->_tpl_vars['sCellPhone2']; ?>

			<?php echo $this->_tpl_vars['sFax']; ?>

			<?php echo $this->_tpl_vars['sEmail']; ?>


			<?php if ($this->_tpl_vars['sERDepartments']): ?>
				<tr>
					<td colspan=3 >&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="dashlet">
							<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="*">
										<h1><?php echo $this->_tpl_vars['sFamilyBackground']; ?>
</h1>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>

				<!-- edited by VAN 05-19-08 -->
				<?php if ($this->_tpl_vars['segPersonInput']): ?>

				<?php if ($this->_tpl_vars['sIsNewborn']): ?>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2">&nbsp;&nbsp;First Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Middle Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Last Name</td>
				</tr>

				<tr>
					<td class="reg_item"><?php echo $this->_tpl_vars['sFather']; ?>
</td>
					<td class="reg_input" colspan="2"><?php echo $this->_tpl_vars['sFather_fname'];  echo $this->_tpl_vars['sFather_mname'];  echo $this->_tpl_vars['sFather_lname']; ?>
</td>
				</tr>
				<?php else: ?>
				<tr>
					<td class="reg_item"><?php echo $this->_tpl_vars['sFather']; ?>
</td>
				</tr>
				<?php endif; ?>
				<?php if ($this->_tpl_vars['sIsNewborn']): ?>
				<tr>
					<td class="reg_item"><?php echo $this->_tpl_vars['sMother']; ?>
</td>
					<td colspan="2">
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
                                <td>&nbsp;&nbsp;</td>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">First Name</span></br><?php echo $this->_tpl_vars['sMother_fname']; ?>

								</td>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">Maiden Name</span><br /><?php echo $this->_tpl_vars['sMother_mdname']; ?>

								</td>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">Middle Name</span><br /><?php echo $this->_tpl_vars['sMother_mname']; ?>

								</td>
								<td width="*" nowrap="nowrap">
									<span class="reg_label">Last Name</span><br /><?php echo $this->_tpl_vars['sMother_lname']; ?>

								</td>
							</tr>

						</table>
					</td>
				</tr>
				<?php else: ?>
				<tr>
					<td class="reg_item"><?php echo $this->_tpl_vars['sMother']; ?>
</td>
				</tr>
				<?php endif; ?>
				<?php else: ?>

				<tr>
					<td class="reg_item"><?php echo $this->_tpl_vars['sFather']; ?>
</td>
					<td class="reg_input" colspan="2"><?php echo $this->_tpl_vars['sFather_name']; ?>
</td>
				</tr>
				<tr>
					<td class="reg_item"><?php echo $this->_tpl_vars['sMother']; ?>
</td>
					<td class="reg_input" colspan="2"><?php echo $this->_tpl_vars['sMother_name']; ?>
</td>
				</tr>
				<?php endif; ?>

				<?php echo $this->_tpl_vars['sSpouse']; ?>

				<?php echo $this->_tpl_vars['sGuardian']; ?>

			<?php endif; ?>
			<?php if (! $this->_tpl_vars['sERDepartments']): ?>

				<tr>
					<td colspan=3 >&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="dashlet">
							<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="*">
										<h1><?php echo $this->_tpl_vars['sFamilyBackground']; ?>
</h1>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				<!--<?php echo $this->_tpl_vars['sMother']; ?>

				<?php echo $this->_tpl_vars['sFather']; ?>
-->
				<!-- edited by VAN 05-19-08 -->
				<?php if ($this->_tpl_vars['segPersonInput']): ?>
				<?php if ($this->_tpl_vars['sIsNewborn']): ?>
				<tr class="personDetails">
					<td class="reg_item"><?php echo $this->_tpl_vars['sFather']; ?>
</td>
					<td colspan="2">
						<table width="100%" cellpadding="0" cellspacing="2">
							<tr>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">First Name</span></br><?php echo $this->_tpl_vars['sFather_fname']; ?>

								</td>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">Middle Name</span><br /><?php echo $this->_tpl_vars['sFather_mname']; ?>

								</td>
								<td width="*" nowrap="nowrap">
									<span class="reg_label">Last Name</span><br /><?php echo $this->_tpl_vars['sFather_lname']; ?>

								</td>
							</tr>
						</table>
					</td>
				</tr>
				<?php else: ?>
				<tr class="personDetails">
					<td class="reg_item"><?php echo $this->_tpl_vars['sFather']; ?>
</td>
				</tr>
				<?php endif; ?>
				<?php if ($this->_tpl_vars['sIsNewborn']): ?>
				<tr class="personDetails">

					<td class="reg_item"><?php echo $this->_tpl_vars['sMother']; ?>
</td>
					<td colspan="2">
						<table width="100%" cellpadding="0" cellspacing="2">
							<tr>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">First Name</span></br><?php echo $this->_tpl_vars['sMother_fname']; ?>

								</td>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">Maiden Name</span><br /><?php echo $this->_tpl_vars['sMother_mdname']; ?>

								</td>
								<td width="1%" nowrap="nowrap">
									<span class="reg_label">Middle Name</span><br /><?php echo $this->_tpl_vars['sMother_mname']; ?>

								</td>
								<td width="*" nowrap="nowrap">
									<span class="reg_label">Last Name</span><br /><?php echo $this->_tpl_vars['sMother_lname']; ?>

								</td>
							</tr>
						</table>
					</td>
				</tr>
				<?php else: ?>
				<tr class="personDetails">
					<td class="reg_item"><?php echo $this->_tpl_vars['sMother']; ?>
</td>
				</tr>
				<?php endif; ?>

				<?php else: ?>
				<tr class="personDetails">
					<td class="reg_item"><?php echo $this->_tpl_vars['sFather']; ?>
</td>
					<td class="reg_input" colspan="2"><?php echo $this->_tpl_vars['sFather_name']; ?>
</td>
				</tr>
				<tr class="personDetails">
					<td class="reg_item"><?php echo $this->_tpl_vars['sMother']; ?>
</td>
					<td class="reg_input" colspan="2"><?php echo $this->_tpl_vars['sMother_name']; ?>
</td>
				</tr>

				<?php endif; ?>
				<?php echo $this->_tpl_vars['sSpouse']; ?>

				<?php echo $this->_tpl_vars['sGuardian']; ?>

			<?php endif; ?>

			<tr>
					<td colspan=3 >&nbsp;</td>
				</tr>
			<tr>
				<td colspan="2">
					<div class="dashlet">
						<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="*">
									<h1>Other Personal Details:</h1>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
				
				<?php echo $this->_tpl_vars['sOccupation']; ?>

				<?php echo $this->_tpl_vars['sEmployer']; ?>

				<?php echo $this->_tpl_vars['sCitizenship']; ?>

				<?php echo $this->_tpl_vars['sSSSNr']; ?>

				<?php echo $this->_tpl_vars['sNatIdNr']; ?>

			
				<tr>
					<td class="reg_item">
						<?php echo $this->_tpl_vars['LDContactNo']; ?>

					</td>
					<td class="reg_input">
						<?php echo $this->_tpl_vars['sContactNo']; ?>

					</td>
				</tr>
		

			<tr>
				<td colspan=3 >&nbsp;</td>
			</tr>
			<tr>
				<td colspan=3 >
						<?php echo $this->_tpl_vars['sArrows']; ?>

				</td>
			</tr>
			<tr>
				<td colspan=3 >&nbsp;</td>
			</tr>

			<?php if ($this->_tpl_vars['bShowInsurance']): ?>
				<tr class="personDetails">
					<td colspan=3 >&nbsp;</td>
				</tr>
				<tr class="personDetails">
					<td colspan=2>
						<div class="dashlet">
							<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="*">
										<h1>Insurances:</h1>
									</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				<tr class="personDetails">
					<td class="reg_item"><?php echo $this->_tpl_vars['LDInsuranceClass']; ?>
&nbsp;</td>
					<td colspan=2 class="reg_input">
						<?php echo $this->_tpl_vars['sErrorInsClass']; ?>

						<?php if (count($_from = (array)$this->_tpl_vars['sInsClasses'])):
    foreach ($_from as $this->_tpl_vars['InsClass']):
?>
							<?php echo $this->_tpl_vars['InsClass']; ?>

						<?php endforeach; unset($_from); endif; ?>
						&nbsp;&nbsp;<span name="iconIns" id="iconIns" style="display:none"><?php echo $this->_tpl_vars['sBtnAddItem']; ?>
</span>
					</td>
				</tr>
				<!---added by VAN 09-04-07----------->
				<tr class="personDetails">
					<td class="reg_item"><?php echo $this->_tpl_vars['LDInsuranceNr']; ?>
</td>
					<td colspan=2 class="reg_input">
						<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="70%">
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
					</td>
				</tr>
			<?php endif; ?>

			<?php if ($this->_tpl_vars['bNoInsurance']): ?>
				<tr class="personDetails">
					<td>&nbsp;</td>
					<td colspan=2 class="reg_input">
						<?php echo $this->_tpl_vars['LDSeveralInsurances']; ?>

					</td>
				</tr>
			<?php endif; ?>

			<tr class="personDetails">
				<td colspan="3">&nbsp;</td>
			</tr>

			<?php if ($this->_tpl_vars['bShowOtherHospNr']): ?>
				<tr class="personDetails">
					<td class="reg_item" valign=top class="reg_input">
						<?php echo $this->_tpl_vars['LDOtherHospitalNr']; ?>

					</td>
					<td colspan=2 class="reg_input">
						<?php echo $this->_tpl_vars['sOtherNr']; ?>

						<?php echo $this->_tpl_vars['sOtherNrSelect']; ?>

					</td>
				</tr>
				<?php endif; ?>

			<?php if ($this->_tpl_vars['sERDepartments']): ?>

				<tr class="personDetails">
					<td colspan=3>
							<?php echo $this->_tpl_vars['sERDepartments']; ?>

					</td>
				</tr>
				<tr class="personDetails">
					<td colspan=3>
							<?php echo $this->_tpl_vars['sERCategory']; ?>

					</td>
				</tr>
				<tr>
					<td colspan=3 >&nbsp;</td>
				</tr>

			<?php endif; ?>
			<tr class="personDetails">
				<td class="reg_item">
					<?php echo $this->_tpl_vars['LDRegBy']; ?>

				</td>
				<td colspan=2 class="reg_input">
					<?php echo $this->_tpl_vars['sRegByInput']; ?>

				</td>
			</tr>
			<tr class="personDetails">
				<td class="reg_item">
					<?php echo $this->_tpl_vars['LDDept']; ?>

				</td>
				<td colspan=2 class="reg_input">
						<?php echo $this->_tpl_vars['sDeptInput']; ?>

				</td>
			</tr>
		</table>

		<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

		<?php echo $this->_tpl_vars['sUpdateHiddenInputs']; ?>

		<p>
		<?php echo $this->_tpl_vars['pbSubmit']; ?>
 <?php echo $this->_tpl_vars['pbERSubmit']; ?>
 &nbsp;&nbsp; <?php echo $this->_tpl_vars['pbReset']; ?>
  &nbsp;&nbsp; <?php echo $this->_tpl_vars['pbForceSave']; ?>


		<?php if ($this->_tpl_vars['bSetAsForm']): ?>
		</form>
		<?php endif; ?>

		<?php echo $this->_tpl_vars['sNewDataForm']; ?>