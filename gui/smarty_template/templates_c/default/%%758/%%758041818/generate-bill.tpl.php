<?php /* Smarty version 2.6.0, created on 2017-10-24 12:03:25
         compiled from industrial_clinic/generate-bill.tpl */ ?>
<div>

<div style="width:90%; margin-top:10px" align="left">
	<table border="0" cellspacing="2" cellpadding="3" align="left" width="100%">
		<tbody>
			<tr>
				<td width="15%"><strong>Agency</strong></td>
				<td width="85%"> : &nbsp;&nbsp;&nbsp; <?php echo $this->_tpl_vars['sAgency']; ?>
</td>
			</tr>

			<tr>
				<td><strong>Cut-Off Date</strong></td>
				<td> : &nbsp;&nbsp;&nbsp; <?php echo $this->_tpl_vars['sCutOff']; ?>
<td>
			</tr>
		</tbody>
	</table>
</div>

<br>
<br>
<br>

<div class="segContentPane" style="width:92%;">

	<div style="width:98%" align="right">
		<?php echo $this->_tpl_vars['sPreview']; ?>

		<?php echo $this->_tpl_vars['sGenerateBill']; ?>

	</div>

	<br>

	<div style="width:98%; height:290px; overflow-y:scroll;">
		<table id="employee-list" class="jedList" width="98%" cellspacing="0" cellpadding="0" border="1">

			<thead>
				<tr>
					<th align="left"> Transaction Date </th>
					<th align="left"> Employee Name </th>
					<th align="center"> Select All &nbsp;&nbsp; 
						<input checked id="selectall" type="checkbox" onclick="selectAllEmployee(this);" name="selectall" style="valign:bottom">
					</th>
				</tr>
			</thead>

			<tbody>
				<?php echo $this->_tpl_vars['sListRows']; ?>

			</tbody>
		</table>
	</div>
</div>

<div style="width:89.5%; margin-top:20px">
	<table class="segList" width="100%" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th align="right" style="font-weight:bold; font-size:12px;" colspan="2">
					<span>Total Bill of Employees</span>
					<span style="padding:12px">
						<button id="btnadd_discount" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" style="font:bold 12px Arial; cursor:pointer" role="button">
							<span class="ui-button-icon-primary ui-icon ui-icon-circle-plus"></span>
							<span class="ui-button-text">Discount Details</span>
						</button>
					</span>
				</th>
				<th width="3%"></th>
			</tr>
		</thead>

		<tbody>
			<tr> </tr>
			<tr>
				<td width="*" align="right" height="" style="background-color:#ffffff; padding:4px">
					SubTotal
				</td>
				<td id="show-sub-total" width="17% " align="right" style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold"> 0.00 </td>
				<td></td>
			</tr>
			<tr>
				<td align="right" style="background-color:#ffffff; padding:4px">
				Discount
				</td>
				<td id="show-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold"> 0.00 </td>
				<td></td>
			</tr>
			<tr>
				<td align="right" style="background-color:#ffffff; padding:4px">
				Total
				</td>
				<td id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold"> 0.00 </td>
				<td></td>
			</tr>
		</tbody>
	</table>
</div>

</div>