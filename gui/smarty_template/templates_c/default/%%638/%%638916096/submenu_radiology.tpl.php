<?php /* Smarty version 2.6.0, created on 2018-05-24 12:52:21
         compiled from radiology/submenu_radiology.tpl */ ?>
		<blockquote>
<!--			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
				<TBODY>
						<TR>
 				<td>
					<table cellSpacing=1 cellPadding=3 width=600>
					<tbody class="submenu">
						<tr>
							<td class="submenu_title" colspan=2>Transactions</td>
						</tr>
						<tr>
							<td class="submenu_item" width=30%><nobr><?php echo $this->_tpl_vars['LDCreateTransaction']; ?>
</nobr></td>
							<td>Create Service Transaction</td>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<tr>
							<td class="submenu_item" witdth=30%><nobr><?php echo $this->_tpl_vars['LDManageTransactions']; ?>
</nobr></td>
							<td>View, edit and delete service transactions</td>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<tr>			
							<td class="submenu_item" widht=30%><nobr><?php echo $this->_tpl_vars['LDServicePrices']; ?>
</nobr></td>
							<td>Set the price for Radiology prices</td>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<tr>			
							<td class="submenu_item" widht=30%><nobr><?php echo $this->_tpl_vars['LDViewAssignRequest']; ?>
</nobr></td>
							<td><?php echo $this->_tpl_vars['LDViewAssignRequestTxt']; ?>
</td>
						</tr>
					</tbody>
					</table>
				</td>
					</TR>
				<TBODY>
			</TABLE>
			<p>
-->
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE cellSpacing=1 cellPadding=3 width=600>
								<TBODY class="submenu">
									<tr>
										<TD class="submenu_title" colspan=3>Test Request</TD>
									</tr>
									<?php echo $this->_tpl_vars['LDCreateNewRadioServiceRequest']; ?>

									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

									<?php echo $this->_tpl_vars['LDRadioServiceRequestList']; ?>

									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
									
									<!--<tr>
										<TD align="center" width="6%"><?php echo $this->_tpl_vars['sRadioTechIcon']; ?>
</TD>
										<TD class="submenu_item" width=37%><nobr><?php echo $this->_tpl_vars['LDRadioTech']; ?>
</nobr></TD>
										<TD>Record served radiological (XRAY, CT-SCAN, MRI, ULTRASOUND and others) requests.</TD>
									</tr> -->
									
									<!-- <?php echo $this->_tpl_vars['LDRadioScheduleRequestCalendar']; ?>

									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> -->
									
									<?php echo $this->_tpl_vars['LDRadioScheduleRequestList']; ?>

									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
									
									<?php echo $this->_tpl_vars['LDUndoneRequest']; ?>

									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

									<?php echo $this->_tpl_vars['LDDoneRequest']; ?>

                                    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                                    
                                    <?php echo $this->_tpl_vars['LDUnifiedResults']; ?>

                                    
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			<p></p>
			<!--
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE cellSpacing=1 cellPadding=3 width=600>
								<TBODY class="submenu">
									<tr>
										<TD class="submenu_title" colspan=3>
										Radiology Borrowing System
										</TD>
									</tr>
									<?php echo $this->_tpl_vars['LDRadioPatientList']; ?>

									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

									<?php echo $this->_tpl_vars['LDRadioBorrowList']; ?>

								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			-->
			<!-- <TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE cellSpacing=1 cellPadding=3 width=600>
								<TBODY class="submenu">
									<tr>
										<TD class="submenu_title" colspan=3>
										Radiology Borrowing System
										</TD>
									</tr>
									<tr>
										<TD align="center" width="6%"><?php echo $this->_tpl_vars['sRadioPatientListIcon']; ?>
</TD>
										<TD class="submenu_item" width=37%><nobr><?php echo $this->_tpl_vars['LDRadioPatientList']; ?>
</nobr></TD>
										<TD>List of all radiology patients</TD>
									</tr>
									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
									<tr>
										<TD align="center"><?php echo $this->_tpl_vars['sRadioBorrowListIcon']; ?>
</TD>
										<TD class="submenu_item" width=37%><nobr><?php echo $this->_tpl_vars['LDRadioBorrowList']; ?>
</nobr></TD>
										<TD>List of all borrowed films</TD>
									</tr>
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE> -->
			<p></p>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<tr>
							<TD class="submenu_title" colspan=3>Administration</TD>
						</tr>
						<!--edited by VAN 03-15-08 -->
						<!--
						<tr>
							<TD width="6%" align="center"><?php echo $this->_tpl_vars['sRadioServicesIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDRadioServicesOLD']; ?>
</nobr></TD>
							<TD>Manage radiology services options</TD>
						</tr>
						
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						-->
						<tr>
							<TD align="center" width="6%"><?php echo $this->_tpl_vars['sRadioServicesIcon']; ?>
</TD>
							<TD class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDRadioServices']; ?>
</nobr></TD>
							<TD>Manage radiology services options</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<tr>
							<TD align="center"><?php echo $this->_tpl_vars['sRadioServicesGroupIcon']; ?>
</TD>
							<TD class="submenu_item" width=37%><nobr><?php echo $this->_tpl_vars['LDRadioServicesGroups']; ?>
</nobr></TD>
							<TD>Manage radiology group options</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<!-- added by VAN 07-07-08 -->
						<tr>
							<TD align="center"><?php echo $this->_tpl_vars['sRadioFindingCodeIcon']; ?>
</TD>
							<TD class="submenu_item" width=37%><nobr><?php echo $this->_tpl_vars['LDRadioFindingCode']; ?>
</nobr></TD>
							<TD>Manage radiology finding's code</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<tr>
							<TD align="center"><?php echo $this->_tpl_vars['sRadioImpressionCodeIcon']; ?>
</TD>
							<TD class="submenu_item" width=37%><nobr><?php echo $this->_tpl_vars['LDRadioImpressionCode']; ?>
</nobr></TD>
							<TD>Manage radiology impression's code</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<!-- <tr>
							<TD align="center"><?php echo $this->_tpl_vars['sRadioDoctorPartnerIcon']; ?>
</TD>
							<TD class="submenu_item" width=37%><nobr><?php echo $this->_tpl_vars['LDRadioDoctorPartner']; ?>
</nobr></TD>
							<TD>Manage radiology's co-reader physicians for film reading</TD>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<tr>
							<TD align="center"><?php echo $this->_tpl_vars['sRadioDOCSchedulerIcon']; ?>
</TD>
							<td class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDRadioDOCScheduler']; ?>
</nobr></td>
							<td>Resident in-charge scheduler, plan, view, update, edit, etc.</td>
						</tr>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> -->
						<tr>
							<TD align="center"><?php echo $this->_tpl_vars['sRadioReportIcon']; ?>
</TD>
							<td class="submenu_item" width=35%><nobr><?php echo $this->_tpl_vars['LDRadioReport']; ?>
</nobr></td>
							<td>View and print specific status reports</td>
						</tr>
						<!--<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>-->
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TBODY>
			</TABLE>
			<p>
			<a href="<?php echo $this->_tpl_vars['breakfile']; ?>
"><img <?php echo $this->_tpl_vars['gifClose2']; ?>
 alt="<?php echo $this->_tpl_vars['LDCloseAlt']; ?>
" <?php echo $this->_tpl_vars['dhtml']; ?>
></a>
			<p>
			</blockquote>