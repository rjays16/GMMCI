<?php /* Smarty version 2.6.0, created on 2018-02-06 21:11:01
         compiled from nursing/waitlist_details.tpl */ ?>
<?php echo $this->_tpl_vars['sFormStart']; ?>

<div style="width:630px;margin-top:20px;">
		<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center" style="font:12px Arial;">
			<tbody valign="middle">
				<tr>
					<td class="segPanelHeader" colspan="2">Patient Data</td>
				</tr>
				<tr>
					<td class="segPanel">
						<table border="0" cellpadding="2" cellspacing="2" width="100%" align="center"  style="font:12px Arial;color:#000000">
							<tr>
								<td width="135px"><label>HRN:</label></td>
								<td><?php echo $this->_tpl_vars['patient_id']; ?>
</td>
							</tr>
							<tr>
								<td><label>Case Number:</label></td>
								<td><?php echo $this->_tpl_vars['casenum']; ?>
</td>
							</tr>
							<tr>
								<td><label>Patient Name:</label></td>
								<td><?php echo $this->_tpl_vars['patient_name']; ?>
</td>
							</tr>
							<tr>
								<td><label>Birthday:</label></td>
								<td><?php echo $this->_tpl_vars['birthday']; ?>
</td>
							</tr>
							<tr>
								<td><label>Ward:</label></td>
								<td><?php echo $this->_tpl_vars['patient_ward']; ?>
</td>
							</tr>
							<!--<tr>
								<td><label>Room:</label></td>
								<td></td>
							</tr>-->
							<tr>
								<td>&nbsp;</td>
							</tr>
						</table>
						<table border="0" cellpadding="2" cellspacing="2" width="100%" align="center"  style="font:12px Arial;color:#000000">
							<tr>
								<td width="20%" style="font:bold 12px Arial;" align="center"><label><strong>Options:</strong></label></td>
								<td width="10%">&nbsp;</td>
								<td width="70%">&nbsp;</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><?php echo $this->_tpl_vars['patient_details']; ?>
</td>
								<td><?php echo $this->_tpl_vars['patient_details_info']; ?>
</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><?php echo $this->_tpl_vars['nurse_notes']; ?>
</td>
								<td><?php echo $this->_tpl_vars['nurse_notes_info']; ?>
</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><?php echo $this->_tpl_vars['patient_transfer']; ?>
</td>
								<td><?php echo $this->_tpl_vars['patient_transfer_info']; ?>
</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><?php echo $this->_tpl_vars['patient_to_be_discharge']; ?>
</td>
								<td><?php echo $this->_tpl_vars['patient_to_be_discharge_info']; ?>
</td>
							</tr>
							<!--<tr>
								<td>&nbsp;</td>
								<td><?php echo $this->_tpl_vars['patient_discharge']; ?>
</td>
								<td><?php echo $this->_tpl_vars['patient_discharge_info']; ?>
</td>
							</tr>-->
							<tr>
								<td>&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<!--<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td align="center"><?php echo $this->_tpl_vars['cancelBtn']; ?>
 </td>
				</tr>   -->
			</tbody>
		</table>
</div>
<?php echo $this->_tpl_vars['submitted']; ?>

<?php echo $this->_tpl_vars['encounter_nr']; ?>

<?php echo $this->_tpl_vars['pid']; ?>

<?php echo $this->_tpl_vars['ward']; ?>

<?php echo $this->_tpl_vars['ward_nr']; ?>

<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>