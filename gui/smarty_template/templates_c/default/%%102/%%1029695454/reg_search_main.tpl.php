<?php /* Smarty version 2.6.0, created on 2018-04-24 13:10:46
         compiled from registration_admission/reg_search_main.tpl */ ?>
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
        <table align="center" cellpadding="5" cellspacing="5" class="reg_searchmask">
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
                    <td width="35%">                      <input type="text" name="searchkey" id="searchkey" size=40 maxlength=80 onKeyUp="DisabledSearch(this.value);" onBlur="DisabledSearch(this.value);" value="">
                      <input type="hidden" id="debug" value="" disabled="disabled" />
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
		
				<tr>
			<td colspan=8><?php echo $this->_tpl_vars['sPreviousPage']; ?>
</td>
			<td align=right colspan="2"><?php echo $this->_tpl_vars['sNextPage']; ?>
</td>
		</tr>
		<tr class="reg_list_titlebar">
			<td width="10%"><?php echo $this->_tpl_vars['LDRegistryNr']; ?>
</td>
			<td width="2%"><?php echo $this->_tpl_vars['LDSex']; ?>
</td>
			<td width="11%"><?php echo $this->_tpl_vars['LDLastName']; ?>
</td>
			<td width="*"><?php echo $this->_tpl_vars['LDFirstName']; ?>
</td>
			<td width="11%"><?php echo $this->_tpl_vars['LDMiddleName']; ?>
</td>
			<td width="5%"><?php echo $this->_tpl_vars['LDBday']; ?>
</td>
			<td width="15%"><?php echo $this->_tpl_vars['segBrgy']; ?>
</td>
			<td width="10%"><?php echo $this->_tpl_vars['segMuni']; ?>
</td>
			<td width="3%"><?php echo $this->_tpl_vars['LDZipCode']; ?>
</td>
			<td width="3%"><?php echo $this->_tpl_vars['LDOptions']; ?>
</td>
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