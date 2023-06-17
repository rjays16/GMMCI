<?php /* Smarty version 2.6.0, created on 2018-04-24 13:17:37
         compiled from phs/submenu_phs.tpl */ ?>
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
								<TBODY >
									<tr>
										<TD class="submenu_title" colspan=3>Patient Services</TD>
									</tr>
                 <!-- <?php echo $this->_tpl_vars['LDRegPatient']; ?>

                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/submenu_row_spacer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> -->
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
                    <TD class="submenu_title" colspan=3>Department Services</TD>
                  </tr>
                  <?php echo $this->_tpl_vars['LDConsultation']; ?>

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
      <!--
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

                  <?php echo $this->_tpl_vars['LDIcdMedCert']; ?>

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
	   -->
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
									<?php echo $this->_tpl_vars['LDGenerateOPDReport']; ?>

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
