			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
								<TBODY >
									<tr>
										<TD class="submenu_title" colspan=3>Patient Services</TD>
									</tr>
                 <!-- {{$LDRegPatient}}
                  {{include file="common/submenu_row_spacer.tpl"}} -->
									{{$LDSearch}}
                  {{include file="common/submenu_row_spacer.tpl"}}
                  {{$LDAdvSearch}}
                  {{include file="common/submenu_row_spacer.tpl"}}
                  {{$LDComprehensive}}
									{{include file="common/submenu_row_footer.tpl"}}
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
                  {{$LDConsultation}}
                  {{include file="common/submenu_row_footer.tpl"}}
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
                  {{$LDIcdIcpm}}
                  {{$LDIcdMedCert}}
                  {{include file="common/submenu_row_footer.tpl"}}
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
									{{$LDGenerateOPDReport}}
									{{include file="common/submenu_row_footer.tpl"}}
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			
			<BR/>
			<A href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}}></a>
			<BR/>

