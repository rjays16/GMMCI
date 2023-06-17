<?php /* Smarty version 2.6.0, created on 2017-11-29 16:00:11
         compiled from clinics/cancel_request_tray.tpl */ ?>
<div>
<?php echo $this->_tpl_vars['form_start']; ?>


<div style="width:90%; margin-top:10px" align="left">
	<table border="0" cellspacing="2" cellpadding="3" align="center" width="100%">
		<tbody>
			<tr>
				<td class="segPanelHeader" width="*" colspan="2">Patient Details</td>
			</tr>
			<tr>
				<td class="segPanel" align="left" valign="top">
					<table  width="100%" class="transaction_details_table" cellpadding="0" cellspacing="0" style="font:normal 12px Arial; padding:4px" >
						<tr>
							<td align="left" width="30%" nowrap="nowrap"><strong>PID : </strong><?php echo $this->_tpl_vars['sPatientID']; ?>
</td>
							<td nowrap="nowrap"><strong>Name : </strong><?php echo $this->_tpl_vars['patient_name']; ?>
</td>
							<td width="30%" nowrap="nowrap"><strong>Patient Type : </strong><?php echo $this->_tpl_vars['encounter_type']; ?>
</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<div>
	<table width="100%" cellpadding="0" cellspacing="0" style="font:12px Tahoma bold;">
		<tr>
			<td align="left" style="font:12px Arial bold;">
				
			</td>

		</tr>
		<tr>
			<td align="left" style="font:12px Arial bold;">
				
			</td>
			<td align="right">
				<span id="show_seldate" class="segInput" style="color: rgb(0, 0, 192); padding: 0px 2px; width: 200px; height: 24px;"><?php echo $this->_tpl_vars['dateToday']; ?>
</span>
				<input id="seldate" type="hidden" name="seldate" value="<?php echo $this->_tpl_vars['dateTodayValue']; ?>
"/>
				<button class="segButton" id="tg_seldate" name="tg_seldate" onclick="return false;" style="cursor:pointer"><img src="../../gui/img/common/default/calendar.png" border="0"/>Date of Request</button>
				<img src="../../images/cashier_refresh.gif" border="0" onclick="requestByDate();" align="absmiddle" class="segSimulatedLink" title="Refresh!"/>
				<script type="text/javascript">
						Calendar.setup ({
								displayArea: "show_seldate",
								inputField : "seldate",
								ifFormat : "%Y-%m-%d",
								daFormat : "	%B %e, %Y",
								showsTime : false,
								button : "tg_seldate",
								singleClick : true,
								step : 1
						});
				</script>
			</td>
		</tr>
	</table>
	</div>
</div>

<div id="tabs" style="width:90%;margin-top:5px">
	<ul>
		<li><a href="#tab-laboratory">Laboratory</a></li>
		<?php if ($this->_tpl_vars['isIC']): ?>
			<li><a href="#tab-iclab">IC Laboratory</a></li>
		<?php endif; ?>
		<li><a href="#tab-bloodbank">Blood Bank</a></li>
		<li><a href="#tab-splab">Special Lab</a></li>
		<li><a href="#tab-radiology">Radiology</a></li>
		<li><a href="#tab-ip">Pharmacy</a></li>
		<!-- <li><a href="#tab-mg">Murang Gamot</a></li> -->
		<li><a href="#tab-miscellaneous">Miscellanous</a></li>
	</ul>

	<div id="tab-laboratory">
			<div class="dashlet" style="margin-top:5px">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
					<tbody>
						<tr>
							<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
							<td align="right">
								<button class="segButton" onclick="deleteAllLabRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/cancel.png" border="0"/>Delete All</button>
							</td>
						</tr>
						<tr>
							<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
							<td><span id="lab-total-charge">0.00</span></td>
						</tr>
						<tr>
							<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
							<td><span id="lab-total-cash">0.00</span></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="lab_requests" align="center">
			</div>
	</div>

 <?php if ($this->_tpl_vars['isIC']): ?>
	<div id="tab-iclab">
			<div class="dashlet" style="margin-top:5px">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
					<tbody>
						<tr>
							<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
							<td align="right">
								<button class="segButton"  id="openICLabRequestBtn" name="openICLabRequestBtn"  onclick="openICLabRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/flag_yellow.png" border="0"/>New request</button>
								<button class="segButton" onclick="openLabResults();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/page_white_acrobat.png" border="0"/>Results</button>
							</td>
						</tr>
						<tr>
							<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
							<td><span id="iclab-total-charge">0.00</span></td>
						</tr>
						<tr>
							<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
							<td><span id="iclab-total-cash">0.00</span></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="iclab_requests" align="center">
			</div>
	</div>
	<?php endif; ?>

	<div id="tab-bloodbank">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" onclick="deleteAllBBRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/cancel.png" border="0"/>Delete All</button>
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="blood-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="blood-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="blood_requests" align="center">
		</div>
	</div>

	<div id="tab-splab">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openSpLabRequestBtn" name="openSpLabRequestBtn" onclick="deleteAllSPLRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/cancel.png" border="0"/>Delete All</button>
							<!--<button class="segButton" onclick="openLabResults();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/page_white_acrobat.png" border="0"/>Results</button>-->
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="splab-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="splab-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="splab_requests" align="center">
		</div>
	</div>

	<div id="tab-radiology">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openRadioRequestBtn" name="openRadioRequestBtn" onclick="deleteAllRadioRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/cancel.png" border="0"/>Delete All</button>
					
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="radio-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="radio-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="radio_requests" align="center">
		</div>
	</div>

	<div id="tab-ip">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openPharmaRequestBtnIP" name="openPharmaRequestBtnIP" onclick="deleteAllPharmaRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/cancel.png" border="0"/>Delete All</button>
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="ip-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="ip-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="ip_requests" align="center">
		</div>
	</div>



	<div id="tab-miscellaneous">
		<div class="dashlet" style="margin-top:5px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="dashletHeader" style="font: bold 12px Tahoma;">
				<tbody>
					<tr>
						<td width="10%" valign="top"><h1 style="white-space:nowrap">List of Requests</h1></td>
						<td align="right">
							<button class="segButton" id="openMiscellaneousRequestBtn" name="openMiscellaneousRequestBtn" onclick="deleteAllMiscRequest();return false;" style="cursor:pointer"><img src="../../gui/img/common/default/cancel.png" border="0"/>Delete All</button>
						</td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Charge: </strong></td>
						<td><span id="misc-total-charge">0.00</span></td>
					</tr>
					<tr>
						<td align="left" style="font: bold 12px Arial;"><strong>TOTAL Cash: </strong></td>
						<td><span id="misc-total-cash">0.00</span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="misc_requests" align="center">
		</div>
	</div>

</div>

<?php echo $this->_tpl_vars['form_end']; ?>

<?php echo $this->_tpl_vars['ptype']; ?>

<?php echo $this->_tpl_vars['request_source']; ?>

<?php echo $this->_tpl_vars['is_bill_final']; ?>

<?php echo $this->_tpl_vars['is_bill_deleted']; ?>

<?php echo $this->_tpl_vars['encounter_nr']; ?>

<?php echo $this->_tpl_vars['isIc_hidden']; ?>

</div>