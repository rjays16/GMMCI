<?php /* Smarty version 2.6.0, created on 2018-02-19 15:56:08
         compiled from blood/blood-request-new.tpl */ ?>
<div align="center" style="font:bold 12px Tahoma; color:#990000; "><?php echo $this->_tpl_vars['sWarning']; ?>
</div><br />

<?php echo $this->_tpl_vars['sFormStart']; ?>

	<table border="0" cellspacing="2" cellpadding="2" width="95%" align="center">
		<tbody>
			<tr>
				<td class="segPanelHeader" width="*">
					Request Details
				</td>
				<td class="segPanelHeader" width="15%">
					<!--Reference No.-->
					Batch No.
				</td>
				<td class="segPanelHeader" width="20%">
					Request Date
				</td>
			</tr>
			<tr>
				<td rowspan="3" class="segPanel" align="center" valign="top">
					<table width="95%" border="0" cellpadding="1" cellspacing="0" style="font-size:11px" >
						<tr>
							<td><strong>Transaction type</strong>
							&nbsp;&nbsp;&nbsp;
								<?php echo $this->_tpl_vars['sIsCash']; ?>

								<?php echo $this->_tpl_vars['sIsCharge']; ?>
<span id="type_charge" style="display:none"><?php echo $this->_tpl_vars['sChargeTyp']; ?>
</span>
								<!--&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sIsTPL']; ?>
-->
							</td>
						</tr>
					</table>
					<table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
						<tr>
							<td align="right" width="1"><strong>HRN</strong></td>
							<td colspan="3" valign="middle"></strong><span id="hrn" style="font:bold 12px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sPatientHRN']; ?>
</span></td>
						</tr>
						<tr>
							<td align="right" width="1" valign="top"><strong>Name</strong></td>
							<td width="1" valign="middle">
								<?php echo $this->_tpl_vars['sOrderEncID']; ?>

								<?php echo $this->_tpl_vars['sOrderName']; ?>

							</td>
							<td width="1" valign="middle">
								<?php echo $this->_tpl_vars['sSelectEnc']; ?>

							</td>
							<td valign="middle">
								<?php echo $this->_tpl_vars['sClearEnc']; ?>

							</td>
						</tr>
						<tr>
							<td valign="top"><strong>Address</strong></td>
							<td colspan="3"><?php echo $this->_tpl_vars['sOrderAddress']; ?>
</td>
						</tr>
					<tr>
						<td colspan="4">
						<table width="100%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
							<tr>
                                <td valign="top" width="20%">
                                    <strong>HACT?</strong>
                                </td>
                                <td valign="top" align="left" width="1%">
                                    <strong>:</strong>
                                </td>
                                <td valign="top" width="*">
                                    <span id="hact" style="font:bold 10px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sHACT']; ?>
</span>
                                </td>
                                <td valign="top" width="20%">
                                    <strong>Blood Type</strong>
                                </td>
                                <td valign="top" align="left" width="1%">
                                    <strong>:</strong>
                                </td>
                                <td valign="top" width="*">
                                    <span id="bloodtype" style="font:bold 10px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sBloodType']; ?>
</span>
                                </td>
                            </tr>    
                            <tr>
								<td valign="top" width="20%">
									<strong>From RDU?</strong>
								</td>
								<td valign="top" align="left" width="1%">
									<strong>:</strong>
								</td>
								<td valign="top" width="*">
									<span id="rdu" style="font:bold 10px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sRDU']; ?>
</span>
								</td>
                                <td valign="top" width="20%">
                                    <strong>Walkin?</strong>
                                </td>
                                <td valign="top" align="left" width="1%">
                                    <strong>:</strong>
                                </td>
                                <td valign="top" width="30%">
                                    <span id="walkin" style="font:bold 10px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sWalkin']; ?>
</span>
                                </td>
								<!--<td colspan="3">
									<table border="0" width="100%">
										<tr>
											<td valign="top" width="20%">
												<strong>Walkin?</strong>
											</td>
											<td valign="top" align="left" width="1%">
												<strong>:</strong>
											</td>
											<td valign="top" width="30%">
												<span id="walkin" style="font:bold 10px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sWalkin']; ?>
</span>
											</td>
											<td valign="top" width="10%">
												<strong>PE?</strong>
											</td>
											<td valign="top" align="left" width="1%">
												<strong>:</strong>
											</td>
											<td valign="top" width="20%">
												<span id="pe" style="font:bold 10px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sPE']; ?>
</span>
											</td>
										</tr>
									</table>
								</td>-->
							</tr>
							<tr>
								<td valign="top" width="20%">
									<strong>Patient Type</strong>
								</td>
								<td valign="top" align="left" width="1%">
									<strong>:</strong>
								</td>
								<td valign="top" width="*">
									<span id="patient_enctype" style="font:bold 10px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sPatientType']; ?>
</span>
								</td>
								<td valign="top" width="10%">
									<strong>Sex</strong>
								</td>
								<td valign="top" align="left" width="1%">
									<strong>:</strong>
								</td>
								<td valign="top" width="20%">
									<span id="sex" style="font:bold 10px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sPatientSex']; ?>
</span>
								</td>

							</tr>
							<tr>
								<td valign="top" width="20%">
									<strong>Birth Date</strong>
								</td>
								<td valign="top" align="left" width="1%">
									<strong>:</strong>
								</td>
								<td valign="top" width="*">
									<span id="dob" style="font:bold 10px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sPatientBdate']; ?>
</span>
								</td>
								<td valign="top" width="5%">
									<strong>Age</strong>
								</td>
								<td valign="top" align="left" width="1%">
									<strong>:</strong>
								</td>
								<td valign="top" width="20%">
									<span id="age" style="font:bold 10px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sPatientAge']; ?>
</span>
								</td>
							</tr>
							<tr>
								<td valign="top" width="20%">
									<strong>Location/Clinic</strong>
								</td>
								<td valign="top" align="left" width="1%">
									<strong>:</strong>
								</td>
								<td valign="top" colspan="4">
									<span id="patient_location" style="font:bold 10px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sPatientLoc']; ?>
</span>
								</td>
							</tr>
							<tr>
								<td valign="top"  width="20%">
									<strong>Medico Legal</strong>
								</td>
								<td valign="top" align="left" width="1%">
									<strong>:</strong>
								</td>
								<td valign="top" colspan="4">
									<span id="patient_medico_legal" style="font:bold 10px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sPatientMedicoLegal']; ?>
</span>
								</td>
							</tr>
							<tr>
								<td valign="top"  width="20%">
									<strong>Diagnosis</strong>
								</td>
								<td valign="top" align="left" width="1%">
									<strong>:</strong>
								</td>
								<td valign="top" colspan="4">
									<span id="adm_diagnosis" style="font:bold 10px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sAdmDiagnosis']; ?>
</span>
								</td>
							</tr>
							<tr>
								<td valign="top" width="20%">
									<strong>Adm. Date</strong>
								</td>
								<td valign="top" align="left" width="1%">
									<strong>:</strong>
								</td>
								<td valign="top" width="30%">
									<span id="admission_date" style="font:bold 10px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sAdmissionDate']; ?>
</span>
								</td>
								<td valign="top" width="5%">
									<strong>Disc. Date</strong>
								</td>
								<td valign="top" align="left" width="1%">
									<strong>:</strong>
								</td>
								<td valign="top" width="30%">
									<span id="discharged_date" style="font:bold 10px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sDischargedDate']; ?>
</span>
								</td>
							</tr>
							<tr id="ic_row" style="display:none">
								<td valign="top" align="left" width="5%"><strong>Charge to Company</strong></td>
								<td valign="top" align="left" width="1%">
									<strong>:</strong>
								</td>
								<td valign="top" width="*" align="left" colspan="4">
									<?php echo $this->_tpl_vars['sChargeToComp']; ?>

									&nbsp;
									<span id="compName" style="font:bold 12px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sCompanyName']; ?>
</span>&nbsp;<?php echo $this->_tpl_vars['sCompanyID']; ?>

								</td>
								</tr>
							<tr>
								<td valign="top" align="left" width="5%"><strong>Program Partner:</strong></td>
								<td valign="top" align="left" width="1%">
										<strong>:</strong>
									</td>
								<td valign="top" width="*">
									<?php echo $this->_tpl_vars['sProgramPartner']; ?>

								</td>
							</tr>
							<tr id='prg_name_div'>
								<td  align="left" width="5%"><strong>Partner Name</strong></td>
									<td  align="left" width="1%"><strong>:</strong></td>
									<td  align="left" colspan="4" >
										<?php echo $this->_tpl_vars['spartnerName']; ?>

									</td>
							</tr>

							<tr>
								<td valign="top" align="left" width="5%"><strong>Repeat Request</strong></td>
								<td valign="top" align="left" width="1%">
									<strong>:</strong>
								</td>
								<td valign="top" width="30%">
									<?php echo $this->_tpl_vars['sRepeat']; ?>

								</td>
							</tr>
							<tr id="repeatinfo" style="display:none">
										<td valign="top" colspan="4">
										<table width="100%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
											<tr>
												<td valign="top" align="left" width="32%"><strong>Previous Refno</strong></td>
												<td valign="top" align="left" width="1">
													<strong>:</strong>
												</td>
												<td valign="top" colspan="2"><?php echo $this->_tpl_vars['sParentRefno']; ?>
</td>
											</tr>
											<tr>
												<td valign="top" align="left" width="5%"><strong>Approved By</strong></td>
												<td valign="top" align="left" width="1%">
													<strong>:</strong>
												</td>
												<td valign="top" colspan="2"><?php echo $this->_tpl_vars['sHead']; ?>
</td>
											</tr>
											<tr>
												<td valign="top" align="left" width="5%"><strong>User ID</strong></td>
												<td valign="top" align="left" width="1%">
													<strong>:</strong>
												</td>
												<td valign="top" colspan="2"><?php echo $this->_tpl_vars['sHeadID']; ?>
</td>
											</tr>
											<tr>
												<td valign="top" align="left" width="5%"><strong>Password</strong></td>
												<td valign="top" align="left" width="1%">
													<strong>:</strong>
												</td>
												<td valign="top" colspan="2"><?php echo $this->_tpl_vars['sHeadPassword']; ?>
</td>
											</tr>
										</table>
										</td>
									</tr>
						
                            <tr>
                                <td valign="top" align="left" width="5%"><strong>PHIC no</strong></td>
                                <td valign="top" align="left" width="1%">
                                    <strong>:</strong>
                                </td>
                                <td valign="top" width="30%">
                                    <span id="phic_nr" style="font-weight:bold;color:#000080"><?php echo $this->_tpl_vars['sPhicNo']; ?>
</span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" width="5%"><strong>Category</strong></td>
                                <td valign="top" align="left" width="1%">
                                    <strong>:</strong>
                                </td>
                                <td valign="top" width="30%">
                                    <span id="mem_category" style="font-weight:bold;color:<?php echo $this->_tpl_vars['sMemCategoryColor']; ?>
"><?php echo $this->_tpl_vars['sMemCategory']; ?>
</span>
                                </td>
                            </tr>    
                         
                        			<!-- added by VAN 06-02-2011 TEMPORARY WORKAROUND -->
						 
                         <tr>
								<td valign="top" align="left" width="5%"><strong>With Manual Payment</strong></td>
								<td valign="top" align="left" width="1%">
									<strong>:</strong>
								</td>
								<td valign="top" width="30%">
									<?php echo $this->_tpl_vars['sManualCheck']; ?>

								</td>
							</tr>
							<tr id="manual" style="display:none">
									<td valign="top" colspan="4">
											<table width="100%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
												<tr>
													<td valign="top" align="left" width="32%"><strong>Type</strong></td>
													<td valign="top" align="left" width="1">
														<strong>:</strong>
													</td>
													<td valign="top" colspan="4">
														<?php echo $this->_tpl_vars['sManualTypeSelection']; ?>

													</td>
												</tr>
												<tr>
													<td valign="top" align="left" width="32%"><strong><span id="label_manual">Control Number</span></strong></td>
													<td valign="top" align="left" width="1">
														<strong>:</strong>
													</td>
													<td valign="top" colspan="2">
														<?php echo $this->_tpl_vars['sManualNumber']; ?>

													</td>
												</tr>
												<tr>
													<td valign="top" align="left" width="32%"><strong>Approved by</strong></td>
													<td valign="top" align="left" width="1">
														<strong>:</strong>
													</td>
													<td valign="top" colspan="2">
														<?php echo $this->_tpl_vars['sManualApprovedby']; ?>

													</td>
												</tr>
												<tr>
													<td valign="top" align="left" width="32%"><strong>Reason for Manual Payment</strong></td>
													<td valign="top" align="left" width="1">
														<strong>:</strong>
													</td>
													<td valign="top" colspan="2">
														<?php echo $this->_tpl_vars['sManualReason']; ?>

													</td>
												</tr>
											</table>
									</td>
							</tr>
						<!-- -->
						</table>
					 </td>
					</table>
				</td>
				<td class="segPanel" align="center">
					<?php echo $this->_tpl_vars['sRefNo']; ?>

					<?php echo $this->_tpl_vars['sResetRefNo']; ?>

				</td>
				<td class="segPanel" align="center" valign="middle">
					<?php echo $this->_tpl_vars['sOrderDate']; ?>

					<?php echo $this->_tpl_vars['sCalendarIcon']; ?>

					<!--<strong style="font-size:10px">mm/dd/yyyy</strong>-->
				</td>
			</tr>
			<tr>
				<td class="segPanelHeader">Discounts</td>
				<td class="segPanelHeader">Request Options</td>
			</tr>

			<tr>
				<td class="segPanel" align="center" valign="top">
						<table width="100%">
							<tr>
								<td valign="middle">
									<div style=""><strong>Classification: </strong><span id="sw-class" style="font:bold 14px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sClassification']; ?>
</span></div>
									<div style="margin-top:5px; vertical-align:middle; "><?php echo $this->_tpl_vars['sDiscountShow']; ?>
</div>
									<br>
									<span id='override_row' style="display:none; font:bold 11px Tahoma;">Discount:
										<br>
										Free All <?php echo $this->_tpl_vars['sFree']; ?>

										<br><?php echo $this->_tpl_vars['sAdjustedAmount']; ?>
</span>
								</td>
								<td><?php echo $this->_tpl_vars['sDiscountInfo']; ?>
</td>
							</tr>

							</table>
						<?php echo $this->_tpl_vars['sBtnDiscounts']; ?>

					</td>
				<!-- -->
				<td class="segPanel" align="center" valign="top">
					<table>
						 <tr>
							 <td valign="top" width="5%"><strong>Priority</strong></td>
							 <td valign="top" width="7%"><?php echo $this->_tpl_vars['sNormalPriority']; ?>
</td>
							 <td valign="top" width="7%"><?php echo $this->_tpl_vars['sUrgentPriority']; ?>
</td>
						 </tr>
						 <tr>
							 <td valign="top" width="5%" colspan="3"><strong style="float:left; margin-top:10px">Comments </strong></td>
						 </tr>
						 <tr>
							 <td align="center" valign="middle" width="5%" colspan="3"><?php echo $this->_tpl_vars['sComments']; ?>
</td>
						 </tr>
										<!--added by angelo m. 09.19.2010-->
										<!--start-->
										<tr>
											<td valign="left"><div style=""><strong>Borrowed?</strong></div></td>
											<td valign="left"><?php echo $this->_tpl_vars['schkIsBorrowed']; ?>
</td>
										</tr>
										<?php echo $this->_tpl_vars['sReplaceRow']; ?>

										<tr>
											<td valign="left"><div style=""><strong>Quantity:</strong></div></td>
											<td valign="left"><?php echo $this->_tpl_vars['sqty_borrowed']; ?>
</td>
											<td valign="left">bag/s</td>
										</tr>
										<tr>
											<td valign="left"><div style=""><strong>Remarks:</strong></div></td>
										</tr>
										<tr>
											<td colspan="3" valign="left"><?php echo $this->_tpl_vars['sbb_remarks']; ?>
</td>
										</tr>

										<!--end-->
					</table>
				</td>
			</tr>
		</tbody>
	</table>

<br>
	<div align="left" style="width:95%">
		<table width="100%">
			<tr>
				<td  width="40%" align="left" nowrap="nowrap">
					<?php echo $this->_tpl_vars['sBtnAddItem']; ?>

					<?php echo $this->_tpl_vars['sBtnEmptyList']; ?>

					<?php echo $this->_tpl_vars['sHistoryButton']; ?>

                    <?php echo $this->_tpl_vars['sTransfusionHistoryButton']; ?>

					<?php echo $this->_tpl_vars['sOtherButton']; ?>

					<?php echo $this->_tpl_vars['sBtnPDF']; ?>

                    <?php echo $this->_tpl_vars['sBtnCoverage']; ?>

                    <?php echo $this->_tpl_vars['sBtnReport']; ?>

                    <?php echo $this->_tpl_vars['sBtnGenXM']; ?>

                   
				</td>
                <td nowrap="nowrap" width="20%">
                    <input id="coverage" type="hidden" value="-1" />
                    <span id="cov_type" style="font:bold 12px Tahoma"></span>
                    <span id="cov_amount" style="font:bold 12px Tahoma;color:#000044"></span>

                    <span style="font:bold 12px Tahoma; display:none">PHIC Coverage:</span>
                    <span id="phic_cov" style="font:bold 12px Tahoma; color:#000044; display:none"></span>
                    <img id="phic_ajax" src="images/ajax_spinner.gif" border="0" title="Loading..." style="display:none" />
                </td>
                <td align="right">
					<?php echo $this->_tpl_vars['sContinueButton']; ?>

					<?php echo $this->_tpl_vars['sBreakButton']; ?>

				</td>
			</tr>
		</table>
		<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr id="order-list-header">
					<th width="4%" nowrap align="left">Cnt : <span id="counter">0</span></th>
					<th width="0.5%"></th>
					<th width="15%" nowrap align="left">&nbsp;&nbsp;Code</th>
					<th width="*" nowrap align="left">&nbsp;&nbsp;Service Description</th>
					<th width="5%" nowrap align="left" style="display:none">Served?<input type="checkbox" id="check_all" name="check_all" onclick="setSampleCheckInStatus();"></th>
					<th width="10%" align="center" nowrap="nowrap">Quantity</th>
					<th width="5%" align="center">Received</th>
					<th width="13%" align="center">Original Price</th>
					<th width="13%">Discounted Price</th>
					<th width="13%" align="center">Net Price</th>
				</tr>
			</thead>
			<tbody>
<?php echo $this->_tpl_vars['sOrderItems']; ?>


			<tbody id="socialServiceNotes" style="display:none">
				<tr>
					<td colspan="9"><?php echo $this->_tpl_vars['sSocialServiceNotes']; ?>
</td>
				</tr>
			</tbody>
		</table>

		<table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
			<tr>
			<tr>
					<td width="*" align="right" style="background-color:#ffffff; padding:4px" height=""><strong>Sub-Total</strong>
					<td id="show-sub-total" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold">
			</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Discount</strong>
					<td id="show-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold">
				</tr>
				<tr>
					<td align="right" style="background-color:#ffffff; padding:4px"><strong>Net Total</strong>
					<td id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold">
				</tr>


		</table>
		<div align="center">

			<?php echo $this->_tpl_vars['sViewPDF']; ?>
 &nbsp; <?php echo $this->_tpl_vars['sClaimStub']; ?>

		
		</div>
	</div>


<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<?php echo $this->_tpl_vars['sIntialRequestList']; ?>

<br/>
<img src="" vspace="2" width="1" height="1"><br/>
<?php echo $this->_tpl_vars['sDiscountControls']; ?>

<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>



<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>

<hr/>
<!--
<input type="button" name="btnRefreshDiscount" id="btnRefreshDiscount" onclick="refreshDiscount()" value="Refresh Discount">
<input type="button" name="btnRefreshTotal" id="btnRefreshTotal" onclick="refreshTotal()" value="Refresh Totals">
-->
<?php echo $this->_tpl_vars['sRefreshDiscountButton']; ?>

<?php echo $this->_tpl_vars['sRefreshTotalButton']; ?>
