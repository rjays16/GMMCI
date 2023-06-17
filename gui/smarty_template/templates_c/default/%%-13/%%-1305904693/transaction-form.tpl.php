<?php /* Smarty version 2.6.0, created on 2017-11-14 18:25:10
         compiled from industrial_clinic/transaction-form.tpl */ ?>
<?php echo $this->_tpl_vars['form_start']; ?>

<div style="width:100%" align="left">
	<table border="0" cellspacing="1" cellpadding="0" width="75%" align="left" style="" >
		<tbody>
			<tr>
				<td colspan="4" class=""><?php echo $this->_tpl_vars['outputResponse']; ?>
</td>

			</tr>
			<tr>
				<td colspan="2" class="segPanelHeader">Transaction Details</td>
			</tr>
			<tr>
				<td class="segPanel">
					<table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
						<tbody>
							<tr>
								<td class="" align="left" valign="middle" width="20%">HRN</td>
								<td class="segInput" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['hrn']; ?>
</td>
								<td rowspan="5" class="photo_id" align="right" id="photo_row" style="background-color:transparent">
									<img width="180px" height="150px" src="<?php echo $this->_tpl_vars['img_source']; ?>
" name="headpic" id="headpic" border="0">
									<input type="hidden" id="photo_src" name="photo_src" value=""/>
								</td>
								</td>
							</tr>
							<tr>
								<td class="" align="left" valign="middle" width="20%">Full Name</td>
								<td class="segInput" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['full_name']; ?>
</td>
							</tr>
							<tr>
								<td class="" align="left" valign="middle" width="20%">Address</td>
								<td class="segInput" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['address']; ?>
</td>
							</tr>
							<tr>
								<td class="" align="left" valign="middle" width="20%"><strong>Age</strong></td>
								<td class="seg_input" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['age']; ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Birthday&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['birthday']; ?>
</td>
							</tr>
							<tr>
								<td class="" align="left" valign="middle" width="20%"><strong>Gender</strong></td>
								<td class="seg_input" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['gender']; ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Civil Status&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['civil_status']; ?>
</td>
							</tr>



						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td class="segPanel">
					<table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
						<tbody>
							<tr>
								<td class="" align="left" valign="middle" width="30%"><strong>Transactional Date</strong></td>
								<td class="segInput" align="left" valign="middle" width="50" nowrap="nowrap"><?php echo $this->_tpl_vars['transaction_date']; ?>
</td>
								<td class="segInput" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['transaction_time']; ?>
</td>
							</tr>
							<tr>
								<td class="" align="left" valign="middle" width="30%"><strong>Case No</strong></td>
								<td class="segInput" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['caseNo']; ?>
</td>
							</tr>
							<tr>
								<td class="" align="left" valign="middle" width="30%"><strong>Charge to Agency</strong></td>
								<td class="seg_input" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['chargeToAgency']; ?>
</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td class="segPanel">
					<table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
						<tbody>
							<tr>
								<td class="" align="left" valign="middle" width="30%"><strong>Agency / Organization</strong></td>
								<td class="segInput" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['agency_organization']; ?>
</td>
							</tr>
							<tr>
								<td class="" align="left" valign="middle" width="30%"><strong>Position</strong></td>
								<td class="segInput" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['position']; ?>
</td>
							</tr>
							<tr>
								<td class="" align="left" valign="middle" width="30%"><strong>ID No.</strong></td>
								<td class="segInput" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['id_no']; ?>
</td>
							</tr>
							<tr>
								<td class="" align="left" valign="middle" width="10%" rowspan="4" ><strong>Status</strong></td>
								<td class="segInput" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['status']; ?>
</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
                        <tr>
                                <td class="segPanel">
                                        <table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family: Arial, Helvitica, sans-serif">
                                                <tbody>
                                                        <tr>
                                                                <td class="" align="left" valign="middle" width="30%"><strong>History of Smoking</strong></td>
                                                                <td class="segInput" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['hSmoking']; ?>
</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="" align="left" valign="middle" width="30%"><strong>Alcohol Drinker</strong></td>
                                                            <td class="segInput" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['hDrinker']; ?>
</td>
                                                        </tr>
                                                </tbody>
                                        </table>
                                </td>
                        </tr>
			<tr>
				<td class="segPanel">
					<table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
						<tbody>
							<tr>
								<td class="" align="left" valign="middle" width="30%"><strong>Purpose of Exam</strong></td>
								<td class="segInput" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['purpose_exam'];  echo $this->_tpl_vars['others']; ?>
</td>
							</tr>
							<tr>
								<td class="" align="left" valign="middle" width="30%"><strong>Remarks</strong></td>
								<td class="segInput" align="left" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['remarks']; ?>
</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>

			<tr>
				<td class="">
					<table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
						<tbody>
							<tr>
								<td class="" align="right" valign="middle" width="70%" rowspan="4"> </td>
							</tr>
							<tr>
								<td class="" align="right" valign="middle" width="70%" rowspan="4"> </td>
							</tr>
							<tr>
								<td class="" align="right" valign="middle" width="70%" rowspan="4"> </td>
							</tr>
							<tr>
								<td class="" align="right" valign="middle" width="70%"><strong><?php echo $this->_tpl_vars['submitButton']; ?>
</strong></td>
								<td class="" align="right" valign="middle" width="*" nowrap="nowrap"><?php echo $this->_tpl_vars['cancelButton']; ?>
</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<table border="0" cellspacing="1" cellpadding="0" width="25%" align="right" style="">
		<tbody>
			<tr class="">
				<td  class="">
				<table border="0" cellspacing="1" cellpadding="0" width="100%" align="" style="" bgcolor="#F4F7FB">
					<tbody>
						<tr>
							<td  colspan="2" class="segPanelHeader">
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
							<font size="2" face="Verdana,Helvetica,Arial" color="#cc0000">
								Options for this person
								<a href="javascript:gethelp('preg_options.php')"><img height="15" border="0" align="absmiddle" width="15" src="../../gui/img/common/default/frage.gif"></a>
							</font>
							</td>
						</tr>
						<tr>
							<td  >&nbsp;</td>
						</tr>
						<tr>
							<td  align="">&nbsp;</td>
							<td  ><a onmouseout="nd()" onclick="showReg();" href="javascript:void(0)"> <img border="0" src="../../gui/img/common/default/newpatient.gif"/>Show Person Registration</a></td>
						</tr>
						<tr>
							<td  align="">&nbsp;</td>
							<td  class=""><a onmouseout="nd()" onclick="showRegUpdate();" href="javascript:void(0)"> <img border="0" src="../../gui/img/common/default/patdata.gif" />
							Update Person Registration</a></td>
						</tr>
						<tr>
							<td  align="">&nbsp;</td>
							<td  class=""><a onmouseout="nd()" onclick="openExaminationsTray();" href="javascript:void(0)"> <img border="0" src="../../gui/img/common/default/consultation.gif" />
							Examinations</a></td>
						</tr>
						<tr>
							<td  align="">&nbsp;</td>
							<td  class=""><?php echo $this->_tpl_vars['medExamLink']; ?>
</td>
						</tr>
						<tr> <!-- added by: syboy 08/06/2015; edited by: syboy 01/06/2016 : meow -->
						<?php if ($this->_tpl_vars['allow_accessFollowUpForm']): ?>
							<td  align="">&nbsp;</td>
							<td  class=""><a onmousedown="nd()" onclick="openMedExamChartFollowUpForm();" href="javascript:void(0)"> <img border="0" src="../../gui/img/common/default/chart.gif" />
								Medical Exam Chart(Follow-up Form)</a>
							</td>
						<?php else: ?>
							<td  align="">&nbsp;</td>
							<td  class=""><a onmousedown="nd()" onclick="openMedExamChart(0,0);" href="javascript:void(0)"><img border="0" src="../../gui/img/common/default/chart.gif" /> Medical Exam Chart(Follow-up Form)</a></td>
						<?php endif; ?>
						</tr> <!-- end -->
						<tr>
							<td  align="">&nbsp;</td>
							<td  class=""><a onmousedown="nd()" onclick="openMedDentalCert();" href="javascript:void(0)"> <img border="0" src="../../gui/img/common/default/icon_acro.gif" />
							Medical/Dental Certificate</a></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><a onmousedown="nd()" onclick="openDriverCert();" href="javascript:void(0)"><img border="0" src="../../gui/img/common/default/icon_acro.gif" />
							Med. Cert Driver's License</a></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><a onmousedown="nd()" onclick="openLtoCert();" href="javascript:void(0)"><img border="0" src="../../gui/img/common/default/icon_acro.gif" />
							LTO Medical Certificate</a></td>
						</tr>
						<tr>
							<td  align="">&nbsp;</td>
							<td  class=""><a onmousedown="nd()" onclick="openClinical();" href="javascript:void(0)"> <img border="0" src="../../gui/img/common/default/icon_acro.gif" />
							HSSC Clinical FOrm</a></td>
						</tr>
                        <tr>
                            <td align="">&nbsp;</td>
                            <td class=""><a onmousedown="nd()" onclick="openVaccination();" href="javascript:void(0)"> <img border="0" src="../../gui/img/common/default/icon_acro.gif" />
                            HSSC Vaccination Certificate</a></td>
                        </tr>
						<!-- <tr>
							<td  align="">&nbsp;</td>
							<td  class=""><a href=""> <img border="0" src="../../gui/img/common/default/home2.gif" />
							Refer/Transfer Department</a></td>
						</tr>
						<tr>
							<td  align="">&nbsp;</td>
							<td  class=""><a href=""> <img border="0" src="../../images/cashier_delete_small.gif" />
							Cancel Transaction</a></td>
						</tr>
						<tr>
							<td  align="">&nbsp;</td>
							<td  class=""><a href=""><img border="0" src="../../gui/img/common/default/storage.gif" />
							DB Record History</a></td>
						</tr> -->
						<tr>
							<td  align="">&nbsp;</td>
							<td  class=""><a onmousedown="nd()" onclick="modeHistory('all');" href="javascript:void(0)"><img  border="0" src="../../gui/img/common/default/indexbox2.gif" />
							Transaction History List</a></td>
						</tr>
						</tbody>
				</table>
				</td>
			</tr>
	</tbody>
	</table>
	<div id="ic-transaction" style="margin-top:10px"></div>
</div>


	<div id="ic-transaction" style="margin-top:10px"></div>

<?php echo $this->_tpl_vars['form_end']; ?>