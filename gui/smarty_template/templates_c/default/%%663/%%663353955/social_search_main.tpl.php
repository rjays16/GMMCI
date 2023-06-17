<?php /* Smarty version 2.6.0, created on 2017-10-24 01:53:44
         compiled from social_service/social_search_main.tpl */ ?>

<?php echo $this->_tpl_vars['sPretext']; ?>


<?php echo $this->_tpl_vars['sJSFormCheck']; ?>


<p>
<center>
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

							<br>
														<input type="text" name="searchkey" id="searchkey" size=40 maxlength=80 onKeyUp="DisabledSearch();" onBlur="DisabledSearch();">
							<p>
							<!-- <?php echo $this->_tpl_vars['sCheckBoxFirstName']; ?>
 <?php echo $this->_tpl_vars['LDIncludeFirstName']; ?>
 --> 
							<!-- "First Name" in this text as search key removed by pet (aug.5,2008) in accordance with VAS' search code changes -->
							
														<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

						</form>
					</td>
				</tr>
			</tbody>
			</table>
		</td>
	</tr>
</table>
</center>
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
			<td align=right colspan="2"><?php echo $this->_tpl_vars['sNextPage']; ?>
</td>
		</tr>
				<tr class="reg_list_titlebar">
			<!--
			<td><?php echo $this->_tpl_vars['LDCaseNr']; ?>
</td>
			<td><?php echo $this->_tpl_vars['segEncDate']; ?>
</td>
			<td><?php echo $this->_tpl_vars['LDSex']; ?>
</td>
			<td><?php echo $this->_tpl_vars['LDLastName']; ?>
</td>
			<td><?php echo $this->_tpl_vars['LDFirstName']; ?>
</td>
			<td><?php echo $this->_tpl_vars['LDBday']; ?>
</td>
			<td><?php echo $this->_tpl_vars['segBrgy']; ?>
</td>
			<td><?php echo $this->_tpl_vars['segMuni']; ?>
</td>
			<td><?php echo $this->_tpl_vars['LDZipCode']; ?>
</td>
			<td>&nbsp;<?php echo $this->_tpl_vars['LDOptions']; ?>
</td>  
			-->
			<!--added by VAN 05-08-08 -->
			<td><?php echo $this->_tpl_vars['LDPID']; ?>
</td>
			
			<td width="12%"><?php echo $this->_tpl_vars['LDCaseNr']; ?>
</td>
			<!--<td><?php echo $this->_tpl_vars['segEncDate']; ?>
</td>-->
			<td width="7%"><?php echo $this->_tpl_vars['LDMSSno']; ?>
</td>
			<td width="1%"><?php echo $this->_tpl_vars['LDSex']; ?>
</td>
			<td width="8%" align="center"><?php echo $this->_tpl_vars['LDAge']; ?>
</td>
			<td><?php echo $this->_tpl_vars['LDLastName']; ?>
</td>
			<td><?php echo $this->_tpl_vars['LDFirstName']; ?>
</td>
			<td><?php echo $this->_tpl_vars['LDMiddleName']; ?>
</td>
			
			<td width="10%"><?php echo $this->_tpl_vars['LDAdmissionDate']; ?>
</td>
			<td width="10%"><?php echo $this->_tpl_vars['LDDischargeDate']; ?>
</td>
			<td><?php echo $this->_tpl_vars['LDDepartment']; ?>
</td>
			
			<!-- commented by VAN 06-26-08 -->
			<!--
			<td><?php echo $this->_tpl_vars['LDBday']; ?>
</td>
			<td><?php echo $this->_tpl_vars['segBrgy']; ?>
</td>
			<td><?php echo $this->_tpl_vars['segMuni']; ?>
</td>
			-->
			<td>&nbsp;<?php echo $this->_tpl_vars['LDOptions']; ?>
</td>         
			
		</tr>

				<?php echo $this->_tpl_vars['sResultListRows']; ?>


		<tr>
			<td colspan=10><?php echo $this->_tpl_vars['sPreviousPage']; ?>
</td>
			<td align=right colspan="2"><?php echo $this->_tpl_vars['sNextPage']; ?>
</td>
		</tr>
	</table>
<?php endif; ?>
<hr>
<?php echo $this->_tpl_vars['sPostText']; ?>

