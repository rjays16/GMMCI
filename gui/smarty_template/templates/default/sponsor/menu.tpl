		<BLOCKQUOTE>
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
								<TBODY>
                  <TR>
	                  <TD class="submenu_title" colspan=3>Lingap</TD>
                  </TR>
{{$sNewLingapGrant}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$sManageLingapAccount}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$sManageLingapGrants}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$sLingapReports}}
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
                <TBODY>
                  <TR>
                    <TD class="submenu_title" colspan=3>CMAP</TD>
                  </TR>
{{$sManagePatientAccount}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$sManageCMAPAccount}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$sCMAPManager}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$sCMAPReports}}
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
			</BLOCKQUOTE>
