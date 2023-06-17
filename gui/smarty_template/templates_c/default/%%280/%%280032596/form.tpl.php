<?php /* Smarty version 2.6.0, created on 2018-05-11 15:34:07
         compiled from repgen/form.tpl */ ?>

<?php echo $this->_tpl_vars['sFormStart']; ?>

	<div style="padding:10px;width:95%;border:0px solid black">
	
	<!-- <font class="prompt"><?php echo $this->_tpl_vars['sDeleteOK'];  echo $this->_tpl_vars['sSaveFeedBack']; ?>
</font> -->
	<font class="warnprompt"><?php echo $this->_tpl_vars['sMascotImg']; ?>
 <?php echo $this->_tpl_vars['sDeleteFailed']; ?>
 <?php echo $this->_tpl_vars['LDOrderNrExists']; ?>
 <br> <?php echo $this->_tpl_vars['sNoSave']; ?>
</font>
	<table border="0" cellspacing="1" cellpadding="3" style="" width="100%">
		<tbody class="submenu">
				<?php if ($this->_tpl_vars['sShowCategory']): ?>
				<tr>
					<td align="right" width="140"><b>Select category</b></td>
					<td width="80%"><?php echo $this->_tpl_vars['sReportCategory']; ?>
</td>
				</tr>
				<?php endif; ?>
				<tr>
					<td align="right" width="140"><b>Select report</b></td>
					<td width="80%"><?php echo $this->_tpl_vars['sReportSelect']; ?>
</td>
				</tr>

				<!-- added by cha 07-20-09 -->
				<tr id="codetype" style="display:none">
					<td align="right" width="140"><b>Select Code type</b></td>
					<td width="80%"><b><?php echo $this->_tpl_vars['sICD10code']; ?>
ICD10 Codes <?php echo $this->_tpl_vars['sICPcode']; ?>
ICP Codes</b></td>
				</tr>

				<tr id="icd_type" style="display:none">
					<td align="right" width="140"><b>Select ICD10 Code</b></td>
					<td class="yui-skin-sam">
							<div id="icd_autocomplete">
									<input type="text" id="icd_code" name="icd_code"/>
									<input type="hidden" id="icd_code_nr" name="icd_code_nr"/>
									<div id="icd_container"></div>
							</div>
					</td>
				</tr>

				<tr id="icp_type" style="display:none">
					<td align="right" width="140"><b>Select ICP Code</b></td>
					<td class="yui-skin-sam">
							<div id="icp_autocomplete">
									<input type="text" id="icp_code" name="icp_code"/>
									<input type="hidden" id="icp_code_nr" name="icp_code_nr"/>
									<div id="icp_container"></div>
							</div>
					</td>
				</tr>

				<tr id="patient_type" style="display:none">
					<td align="right" width="140"><b>Patient Type</b></td>
					<td width="80%"><?php echo $this->_tpl_vars['sPatientType']; ?>
</td>
				</tr>

				<!-- added by VAN 09-12-08 -->
				<tr id="dept_row" style="display:none">
					<td align="right" width="140"><b>Select department</b></td>
					<td width="80%"><?php echo $this->_tpl_vars['sReportSelectDept']; ?>
</td>
				</tr>

				<tr id="dept_row_sub" style="display:none">
					<td align="right" width="140"><b>Select department</b></td>
					<td width="80%"><?php echo $this->_tpl_vars['sReportSelectDeptSub']; ?>
</td>
				</tr>

			<tr id="icd_class">
				<td align="right" width="140"><b>Select Diagnosis Classification</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sICDClassification']; ?>
</td>
			</tr>

			<tr id="notifiable_format" style="display:none">
				<td align="right" width="140"><b>Select Printout Format</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sNotifiableFormat']; ?>
</td>
			</tr>

			<!-- added by Cherry 11-25-09 -->
			<tr id="age_row" style="display:none">
				<td align="right" width="140"><b>Select Age Distribution</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sReportSelectAge']; ?>
</td>
			</tr>

			<!-- added by Cherry 05-09-09 -->
			<tr id="code" style="display:none">
				<td align="right" width="140"><b>Select Code</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sReportSelectCode']; ?>
</td>
			</tr>
			<!-- -->

			<tr id="mode_row" style="display:none">
				<td align="right" width="140"><b>Select mode of report</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sReportSelectKey']; ?>
</td>
			</tr>

			<tr id="died_row" style="display:none">
				<td align="right" width="140"><b>Select Status</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sReportSelectKey2']; ?>
</td>
			</tr>

			<tr id="phic_row" style="display:none">
				<td align="right" width="140"><b>Select Classification</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sReportSelectKey3']; ?>
</td>
			</tr>

			<!--added by Cherry 09-10-10-->
			<tr id="medocs_encoder" style="display:none">
				<td align="right" width="140"><b>Select Encoder</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sReportEncoder']; ?>
</td>
			</tr>
			<!-- end Cherry -->
			<tr id="insurance_type" style="display:none">
					<td align="right" width="140"><b>Insurance Type</b></td>
					<td width="80%"><b><?php echo $this->_tpl_vars['sInsurances']; ?>
</td>
			</tr>
			<tr id="is_senior" style="display:none">
					<td align="right" width="140"><b>Patient Type</b></td>
					<td width="80%"><b><?php echo $this->_tpl_vars['sSenior']; ?>
</td>
			</tr>
			<!-- added by Cherry 04-15-09 -->
			<tr id="loc_row" style="display:none">
				<td align="right" width="140"><b>Select location</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sReportSelectLoc']; ?>
</td>
			</tr>

			<!-- -->
			<tr>
				<td align="right" width="140"><b>From</b></td>
				<td><?php echo $this->_tpl_vars['sFromDateHidden'];  echo $this->_tpl_vars['sFromDateInput'];  echo $this->_tpl_vars['sFromDateIcon']; ?>
</td>
			</tr>

			<tr>
				<td align="right" width="140"><b>To</b></td>
				<td><?php echo $this->_tpl_vars['sToDateHidden'];  echo $this->_tpl_vars['sToDateInput'];  echo $this->_tpl_vars['sToDateIcon']; ?>
</td>
			</tr>

			<tr id="shiftrow" style="display:none">
				<td align="right" width="140"><b>Time</b></td>
				<td colspan="80%"><?php echo $this->_tpl_vars['sShift']; ?>
</td>
			</tr>

			<tr id="orderby" style="display:none">
				<td align="right" width="140"><b>Alphabetical</b></td>
				<td colspan="80%"><?php echo $this->_tpl_vars['sOrderBy']; ?>
</td>
			</tr>

			<!-- added by cha 07-20-09 -->
			<tr id="export_type" style="display:none">
				<td align="right" width="140"><b>Export as</b></td>
				<td width="80%"><?php echo $this->_tpl_vars['sExportAsPdf']; ?>
PDF <?php echo $this->_tpl_vars['sExportAsExcel']; ?>
EXCEL</td>
			</tr>
			<!-- end cha -->

			<tr>
				<td align=right width=140><?php echo $this->_tpl_vars['LDReset']; ?>
</td>
				<td align=right><?php echo $this->_tpl_vars['sUpdateButton']; ?>
</td>
			</tr>
		</tbody>
	</table>

	<?php echo $this->_tpl_vars['sHiddenInputs']; ?>


<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<?php echo $this->_tpl_vars['sTransactionDetailsControls']; ?>

<br/>
<div style="float:left;">
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="1%"><?php echo $this->_tpl_vars['sContinueButton']; ?>
</td>
	</tr>
</table>
</div>


</div>
<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>
