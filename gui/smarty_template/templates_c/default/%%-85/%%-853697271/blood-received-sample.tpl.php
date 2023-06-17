<?php /* Smarty version 2.6.0, created on 2017-12-06 14:22:47
         compiled from blood/blood-received-sample.tpl */ ?>
<?php echo $this->_tpl_vars['sFormStart']; ?>

<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center">
	 
			<tr>
				<td class="segPanelHeader" width="*">
					Request Details
				</td>
			</tr>
			<tr>
				<td class="segPanel" align="center" valign="top">
					<table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
						<tr>
							<td align="left" width="20%"><strong>Reference No.</strong></td>
							<td valign="middle"></strong><span id="refno" style="font:bold 12px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sRefno']; ?>
</span></td>
                            <td align="right" width="1"><strong>&nbsp;</strong></td>
                            <td align="left" width="15%"><strong>HRN</strong></td>
                            <td valign="middle"></strong><span id="hrn" style="font:bold 12px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sHRN']; ?>
</span></td>
						</tr>
                        <tr>
                            <td align="left" width="20%"><strong>Patient Name</strong></td>
                            <td valign="middle"></strong><span id="pat_name" style="font:bold 12px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sPatientName']; ?>
</span></td>
                            <td align="right" width="1"><strong>&nbsp;</strong></td>
                            <td align="left" width="15%"><strong>Blood Type</strong></td>
                            <td valign="middle"></strong><span id="blood_type" style="font:bold 12px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sBloodType']; ?>
</span></td>
                        </tr>
                        <tr>
                            <td align="left" width="20%"><strong>Sex</strong></td>
                            <td valign="middle"></strong><span id="sex" style="font:bold 12px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sSex']; ?>
</span></td>
                            <td align="right" width="1"><strong>&nbsp;</strong></td>
                            <td align="left" width="15%"><strong>Age</strong></td>
                            <td valign="middle"></strong><span id="age" style="font:bold 12px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sAge']; ?>
</span></td>
                        </tr>
						<tr>
							<td align="left" width="20%"><strong>Test Name</strong></td>
							<td valign="middle"></strong><span id="test_name" style="font:bold 12px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sTestName']; ?>
</span></td>
							<td align="right" width="1"><strong>&nbsp;</strong></td>
							<td align="left" width="15%"><strong>Test Code</strong></td>
							<td valign="middle"></strong><span id="test_code" style="font:bold 12px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sTestCode']; ?>
</span></td>
						</tr>
                        <tr>
                            <td align="left" width="15%"><strong>Date Encoded</strong></td>
                            <td valign="middle"></strong><span id="date_encoded" style="font:bold 12px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sDateEncoded']; ?>
</span></td>                            
                            <td align="right" width="1"><strong>&nbsp;</strong></td>
                            <td align="left" width="20%"><strong>Quantity Requested</strong></td>
                            <td valign="middle"></strong><span id="qty" style="font:bold 12px Arial; color:#0000FF;"><?php echo $this->_tpl_vars['sQuantity']; ?>
</span></td>
                        </tr>

					</table>
				</td>
			</tr>
	
</table>
   <!--Add Blood Source and Others in TPL 2014-18-03 (Borj)-->
  <!--Add Blood Ward/Dept in TPL 2014-12-07 (Borj)-->
<div align="left" style="width:100%">
		<table id="RequestList" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="2%" nowrap align="left">Cnt : <span id="counter">0</span></th>
					<th width="*" nowrap align="left">Unit No.</th>
					<th width="13%" align="center"><br />
					<?php echo $this->_tpl_vars['sCheckboxAll']; ?>

					</th>
                    <th width="10%" nowrap align="left">Serial No.</th>
                    <th width="5%" align="center">Ward/Department</th>
					<th width="5%" align="center">Components</th>
					<th width="5%" align="center">Blood Source</th>
					<th width="13%" align="center">Date Received</th>
					<th width="13%" align="center">Date Started</th>
                    <th width="15%" align="center">Date Done</th>
                    <th width="10%" align="center">Result</th>
                    <th width="15%" align="center">Issuance Date</th>
                    <th width="15%" align="center">Returned</th>
                    <th width="15%" align="center">Reissue</th>
                    <th width="20%" align="center">Consumed</th>
					<!--<th width="10%" align="center">Submit</th>-->
					<!--<th width="10%" align="center">Repeat?</th>-->
				</tr>
			</thead>
			<tbody id="RequestList-body">
<?php echo $this->_tpl_vars['sOrderItems']; ?>

			</tbody>
		</table>

	</div>
<?php echo $this->_tpl_vars['printDialog']; ?>

<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<?php echo $this->_tpl_vars['jsPrintDialog']; ?>

<br/>
<?php echo $this->_tpl_vars['sSubmitButton'];  echo $this->_tpl_vars['sCloseButton'];  echo $this->_tpl_vars['sPrintButton']; ?>

<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>

<hr/>
