			<blockquote>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3>Test Requests</TD>
						</tr>
						<TR>
							<TD width="1%">{{$sRequestTestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDRequestTest}}</`nobr></TD>
							<TD>Fill out request for service</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sLabServicesRequestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDLabServicesRequest}}</nobr></TD>
							<TD>View, edit and delete service requests</TD>
						</tr>
						<!--{{include file="common/submenu_row_spacer.tpl"}}-->
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TBODY>
			</TABLE>

			<p>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3>{{$LDAdministration}}</TD>
						</tr>
						<TR>
							<TD width="1%">{{$sLabServicesAdminIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDLabServicesAdmin}}</nobr></TD>
							<TD>Manage services options</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sPrescriptionIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$sPrescriptionLink}}</nobr></TD>
							<TD>View, edit and delete patient prescription</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sStandardIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$sStandardLink}}</nobr></TD>
							<TD>Manage standard prescription templates</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sSoapIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$sSoapLink}}</nobr></TD>
							<TD>Entry for subjective, objective, assessment, and plan notes</TD>
						</tr>
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TBODY>
			</TABLE>
			<p>
			<a href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}}></a>
			<p>
			</blockquote>