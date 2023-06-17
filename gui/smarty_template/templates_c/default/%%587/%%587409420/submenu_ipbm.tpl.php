<?php /* Smarty version 2.6.0, created on 2018-01-30 18:10:39
         compiled from ipbm/submenu_ipbm.tpl */ ?>
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
								<TBODY >
									<tr>
										<TD class="submenu_title" colspan=3>Patient Services</TD>
									</tr>
                  <?php echo $this->_tpl_vars['LDRegPatient']; ?>

                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
									<?php echo $this->_tpl_vars['LDSearch']; ?>

                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                  <?php echo $this->_tpl_vars['LDAdvSearch']; ?>

                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                  <?php echo $this->_tpl_vars['LDComprehensive']; ?>

									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			<BR/>
      <TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
        <TBODY>
          <TR>
            <TD>
              <TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
                <TBODY >
                  <tr>
                    <TD class="submenu_title" colspan=3>Triage</TD>
                  </tr>
                  <?php echo $this->_tpl_vars['LDConsultation']; ?>

                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                  <?php echo $this->_tpl_vars['LDAdmission']; ?>

                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                </TBODY>
              </TABLE>
            </TD>
          </TR>
        </TBODY>
      </TABLE>
      <BR/>
      <!-- <TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
        <TBODY>
          <TR>
            <TD>
              <TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
                <TBODY >
                  <tr>
                    <TD class="submenu_title" colspan=3>Department Services</TD>
                  </tr>
                  <?php echo $this->_tpl_vars['LDPharmacy']; ?>

                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                  <?php echo $this->_tpl_vars['LDCashier']; ?>

                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                  <?php echo $this->_tpl_vars['LDSocialService']; ?>

                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                  <?php echo $this->_tpl_vars['LDBilling']; ?>

                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                </TBODY>
              </TABLE>
            </TD>
          </TR>
        </TBODY>
      </TABLE> -->
      <TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
        <TBODY>
          <TR>
            <TD>
              <TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
                <TBODY >
                  <tr>
                    <TD class="submenu_title" colspan=3>Medical Records</TD>
                  </tr>
                  <?php echo $this->_tpl_vars['LDIcdIcpm']; ?>

                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                </TBODY>
              </TABLE>
            </TD>
          </TR>
        </TBODY>
      </TABLE>
	    <!--Added by Borj 2014-08-04 ISO-->
      <BR/>
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
								<TBODY>
									<tr>
										<TD class="submenu_title" colspan=3>Administration</TD>
									</tr>
									<!-- <?php echo $this->_tpl_vars['LDGenerateOPDReport']; ?>

                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                  added by: syboy 05/29/2015
                  <?php echo $this->_tpl_vars['LDDocSearch']; ?>

                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> -->
                  <?php echo $this->_tpl_vars['LDGenerateReport']; ?>

                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                 <!--  <?php echo $this->_tpl_vars['LDOpdUserManual']; ?>

									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> -->
                  <?php echo $this->_tpl_vars['LDIPBMUserManual']; ?>

                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			
			<BR/>
			<A href="<?php echo $this->_tpl_vars['breakfile']; ?>
"><img <?php echo $this->_tpl_vars['gifClose2']; ?>
 alt="<?php echo $this->_tpl_vars['LDCloseAlt']; ?>
" <?php echo $this->_tpl_vars['dhtml']; ?>
></a>
			<BR/>
