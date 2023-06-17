<?php /* Smarty version 2.6.0, created on 2017-12-08 17:08:45
         compiled from nursing/nursing-notes.tpl */ ?>
<!-- <?php echo $this->_tpl_vars['sFormStart']; ?>
 -->
<?php echo $this->_tpl_vars['sFormNotes']; ?>

<head>
    <?php if (count($_from = (array)$this->_tpl_vars['javascripts'])):
    foreach ($_from as $this->_tpl_vars['script']):
?>
    <?php echo $this->_tpl_vars['script']; ?>

    <?php endforeach; unset($_from); endif; ?>
    <script type="text/javascript">var $j = jQuery.noConflict();</script>
</head>

<div style="width:630px;margin-top:20px;">
		<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center" style="font:12px Arial;">
			<tbody valign="middle">



				<tr>
					<td class="segPanelHeader" colspan="2">Patient Notes</td>
				</tr>
				<tr>
					<td class="segPanel">
						<table border="0" cellpadding="2" cellspacing="2" width="100%" align="center"  style="font:12px Arial;color:#000000">
							<tr>
								<td><label>Date:</label> </td>			
								<td><?php echo $this->_tpl_vars['pNotes_display']; ?>
</td>
								<?php echo $this->_tpl_vars['NotesDate']; ?>

							</tr>
							<tr>
								<td width="135px"><label>Impression/Diagnosis:</label></td>
								<td><?php echo $this->_tpl_vars['impression']; ?>
</td>
							</tr>
							<tr>
								<td><label>Diet:</label></td>
								<td><span style="position: absolute;"><?php echo $this->_tpl_vars['diet']; ?>
</span><span style="float: right;">&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['remarks']; ?>
</span></td>
								<!-- <td><?php echo $this->_tpl_vars['remarks']; ?>
</td> -->
							</tr>
							<tr>
								<td><label>IVF/Level/Due Time:</label></td>
								<td><?php echo $this->_tpl_vars['ivf']; ?>
</td>
							</tr>
							<tr>
								<td><label>Religion:</label></td>
								<td><?php echo $this->_tpl_vars['religion']; ?>
</td>
							</tr>
							<tr>
								<td><label>Height:</label></td>
								<td><?php echo $this->_tpl_vars['height']; ?>
cm</td>
							</tr>
								<tr>
								<td><label>Weight:</label></td>
								<td><?php echo $this->_tpl_vars['weight']; ?>
kg</td>
							</tr>
							<tr>
								<td><label>BMI:</label></td>
								<td><?php echo $this->_tpl_vars['bmi']; ?>
 <?php echo $this->_tpl_vars['bmi_category']; ?>
</td>
							</tr>
								<tr>
								<td>&nbsp;</td>
							</tr>
							<tr>
							<?php if ($this->_tpl_vars['lastmod']): ?>
								<td><label>Last modified by:</label></td>
									<td><span><?php echo $this->_tpl_vars['lastmod']; ?>
</span></td>
									<tr>
									<td><label>Date/time:</label></td>
									<td><span><?php echo $this->_tpl_vars['datetime']; ?>
</span></td></tr>
							<?php endif; ?>
							</tr>
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

<!-- <?php echo $this->_tpl_vars['sFormEnd']; ?>
 -->
<?php echo $this->_tpl_vars['sTailScripts']; ?>