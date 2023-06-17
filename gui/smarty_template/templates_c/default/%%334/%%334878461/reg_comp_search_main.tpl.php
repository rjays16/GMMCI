<?php /* Smarty version 2.6.0, created on 2018-05-02 17:48:52
         compiled from registration_admission/reg_comp_search_main.tpl */ ?>
<style type="text/css">
<!--
body {
	background-color: #EBF0FE;
}
-->
</style><div align="center">  
  <?php echo $this->_tpl_vars['sPretext']; ?>

  
    <?php echo $this->_tpl_vars['sJSGetHelp']; ?>

  
    <?php echo $this->_tpl_vars['sJSFormCheck']; ?>

  <br />
  <table border=0 align="center" cellpadding=2 class="reg_searchmask_border">
    <tr>
      <td>
        <table align="center" border="0" cellpadding="5" cellspacing="5" class="reg_searchmask">
          <tbody>
            <tr>
              <td>
                <form <?php echo $this->_tpl_vars['sFormParams']; ?>
>
                &nbsp;
                <br><table width="100%" border="0" >
  <tr>
    <td bgcolor="#EBF0FE"><?php echo $this->_tpl_vars['searchprompt']; ?>
</td>
  </tr>
</table>

                
                <br> 
				<table width="100%" border="0" class="reg_searchmask">
                  <tr>
                    <td width="35%">                <?php echo $this->_tpl_vars['sSearchKey']; ?>

				<input type="hidden" name="enctype" id="enctype"/>
				<!--<?php echo $this->_tpl_vars['sKeyPost']; ?>
-->
				</td>
                    <td>                    &nbsp;<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

                    </form>			  </td>
            </tr>
        </table>
               <?php echo $this->_tpl_vars['LDTipsTricks']; ?>
 <br> 
                
                <!-- commented out by pet due to changes in vanessa's search codes; aug.5,2008
                  <?php echo $this->_tpl_vars['sCheckBoxFirstName']; ?>
 <?php echo $this->_tpl_vars['LDIncludeFirstName']; ?>

				  -->
                <br><br>
				  <?php echo $this->_tpl_vars['sCheckAll']; ?>
&nbsp;<?php echo $this->_tpl_vars['LDCheckAll']; ?>
&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sCheckER']; ?>
&nbsp;<?php echo $this->_tpl_vars['LDCheckER']; ?>
&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sCheckOPD']; ?>
&nbsp;<?php echo $this->_tpl_vars['LDCheckOPD']; ?>
&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['sCheckIPD']; ?>
&nbsp;<?php echo $this->_tpl_vars['LDCheckIPD']; ?>
	  
      </td>
    </tr>
          </tbody>
  </table>	    </td>
	  </tr>
    <tr>
      <td><?php echo $this->_tpl_vars['sCancelButton']; ?>
 </td>
    </tr>
    </table>
</div>

<p align="center">

<?php echo $this->_tpl_vars['LDSearchFound']; ?>


<?php if ($this->_tpl_vars['bShowResult']): ?>
<p align="center">
<div align="center">
	<table border=0 cellpadding=2 cellspacing=1>

				<tr class="reg_list_titlebar">
            <td width="10%"><strong><font color="#000066"><?php echo $this->_tpl_vars['LDCaseNr']; ?>
</font></strong></td>
			<td width="15%"><strong><font color="#000066"><?php echo $this->_tpl_vars['LDRegistryNr']; ?>
</font></strong></td>
			<td width="2%"><strong><font color="#000066"><?php echo $this->_tpl_vars['LDSex']; ?>
</font></strong></td>
			<td width="15%"><strong><font color="#000066"><?php echo $this->_tpl_vars['LDLastName']; ?>
</font></strong></td>
			<td width="*"><strong><font color="#000066"><?php echo $this->_tpl_vars['LDFirstName']; ?>
</font></strong></td>
			<td width="5%"><strong><font color="#000066"><?php echo $this->_tpl_vars['LDBday']; ?>
</font></strong></td>
			<td width="5%"><strong><font color="#000066"><?php echo $this->_tpl_vars['LDAdmission']; ?>
</font></strong></td>
			<td width="10%"><strong><font color="#000066"><?php echo $this->_tpl_vars['LDLocation']; ?>
</font></strong></td>
			<td width="5%"><strong><font color="#000066"><?php echo $this->_tpl_vars['LDDischarge']; ?>
</font></strong></td>
			<td width="5%"><strong><font color="#000066"><?php echo $this->_tpl_vars['LDOptions']; ?>
</font></strong></td>
            <td width="2%" align="center"><strong><font color="#000066"><?php echo $this->_tpl_vars['LDOptions2']; ?>
</font></strong></td>
		</tr>

				<?php echo $this->_tpl_vars['sResultListRows']; ?>


		<tr>
			<td colspan=8><?php echo $this->_tpl_vars['sPreviousPage']; ?>
</td>
			<td align=right colspan="2"><?php echo $this->_tpl_vars['sNextPage']; ?>
</td>
		</tr>
	</table>
  <?php endif; ?>
  <?php echo $this->_tpl_vars['yhPrevNext']; ?>

  <?php echo $this->_tpl_vars['sPostText']; ?>
</div>