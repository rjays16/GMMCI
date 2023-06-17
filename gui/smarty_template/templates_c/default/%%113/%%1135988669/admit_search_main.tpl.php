<?php /* Smarty version 2.6.0, created on 2017-12-12 01:10:00
         compiled from registration_admission/admit_search_main.tpl */ ?>

<?php echo $this->_tpl_vars['sPretext']; ?>


<?php echo $this->_tpl_vars['sJSFormCheck']; ?>


<p>

<table class="admit_searchmask_border" border=0 cellpadding=10>
	<tr>
		<td>
			<table class="admit_searchmask" cellpadding="5" cellspacing="5">
			<tbody>
				<tr>
					<td>
						<form <?php echo $this->_tpl_vars['sFormParams']; ?>
>
							&nbsp;
							<br>
							<?php echo $this->_tpl_vars['searchprompt']; ?>

							<br><br>
														<input type="text" name="searchkey" id="searchkey" size=40 maxlength=80 onKeyUp="DisabledSearch();" onBlur="DisabledSearch();">
							
														&nbsp;<?php echo $this->_tpl_vars['sHiddenInputs']; ?>
&nbsp;<?php echo $this->_tpl_vars['sAllButton']; ?>

							<p>
							<?php echo $this->_tpl_vars['sCheckBoxFirstName']; ?>
 <?php echo $this->_tpl_vars['LDIncludeFirstName']; ?>

							</p>
							
							<!-- added by VAN 06-25-08-->
							<?php if ($this->_tpl_vars['sClinics']): ?>
								<?php echo $this->_tpl_vars['sCheckAll']; ?>
&nbsp;<?php echo $this->_tpl_vars['LDCheckAll']; ?>
&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sCheckYes']; ?>
&nbsp;<?php echo $this->_tpl_vars['LDCheckYes']; ?>
&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sCheckNo']; ?>
&nbsp;<?php echo $this->_tpl_vars['LDCheckNo']; ?>

								<br>
							<?php endif; ?>	
							<!-- -->
							
						</form>
					</td>
				</tr>
			</tbody>
			</table>
		</td>
	</tr>
</table>
<p>
<?php echo $this->_tpl_vars['sCancelButton']; ?>

<p>

<?php echo $this->_tpl_vars['LDSearchFound']; ?>


<?php if ($this->_tpl_vars['bShowResult']): ?>
	<p>
	<table border=0 cellpadding=2 cellspacing=1>
		<tr>
			<td colspan=10><?php echo $this->_tpl_vars['sPreviousPage']; ?>
</td>
			<td align=right><?php echo $this->_tpl_vars['sNextPage']; ?>
</td>
		</tr>
		
				<tr class="reg_list_titlebar">
			<td width="15%"><?php echo $this->_tpl_vars['LDCaseNr']; ?>
</td>
			<td><?php echo $this->_tpl_vars['segEncDate']; ?>
</td>			
			<td><?php echo $this->_tpl_vars['segCurrentDept']; ?>
</td>			
			<td><?php echo $this->_tpl_vars['LDSex']; ?>
</td>
			<td><?php echo $this->_tpl_vars['LDLastName']; ?>
</td>
			<td><?php echo $this->_tpl_vars['LDFirstName']; ?>
</td>
			<td><?php echo $this->_tpl_vars['LDMiddleName']; ?>
</td>
			<td><?php echo $this->_tpl_vars['LDBday']; ?>
</td>
			<td><?php echo $this->_tpl_vars['segBrgy']; ?>
</td>
			<td><?php echo $this->_tpl_vars['segMuni']; ?>
</td>
<!--
			<td><?php echo $this->_tpl_vars['LDZipCode']; ?>
</td>
-->
			<td>&nbsp;<?php echo $this->_tpl_vars['LDOptions']; ?>
</td> 
			
			<!-- added by VAN 06-25-08 -->
			<?php if ($this->_tpl_vars['LDServeOption']): ?>
				<td><?php echo $this->_tpl_vars['LDServeOption']; ?>
</td>        
			<?php endif; ?>	
			<!-- -->
		</tr>

				<?php echo $this->_tpl_vars['sResultListRows']; ?>


		<tr>
			<td colspan=10><?php echo $this->_tpl_vars['sPreviousPage']; ?>
</td>
			<td align=right><?php echo $this->_tpl_vars['sNextPage']; ?>
</td>
		</tr>
	</table>
	
<?php endif; ?>
<hr>
<?php echo $this->_tpl_vars['yhPrevNext']; ?>

<?php echo $this->_tpl_vars['sPostText']; ?>

