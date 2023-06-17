
			<blockquote>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3>Billing Service</TD>
						</tr>
						<!-- <TR>
							<TD width="1%">{{$sRequestTestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDViewBill}}</`nobr></TD>
							<TD>Process billing of admitted patient or ER patient</TD>
						</tr> -->
						{{* Added by Francis *}}
						<!-- comment by: shandy <TR>
							<TD width="1%">{{$sRequestTestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDViewBillnPHIC}}</`nobr></TD>
							<TD>Process billing of patients without PHIC</TD>
						</tr> -->
						<!-- added by poliam 01/05/2014 -->
						<!-- {{include file="common/submenu_row_spacer.tpl"}} -->
						<!-- ended by poliam 01/05/2014 -->
						<TR>
							<TD width="1%">{{$sRequestTestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDViewBillPHIC}}</nobr></TD>
							<!-- edited by:ian1-6-2014 -->
							<TD>Process billing of admitted patient or ER patient</TD>
						</tr>
						{{* end - Francis *}}
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sLabServicesRequestIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDListOfBilling}}</nobr></TD>
							<TD>List of patients billed.</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sPromissoryNoteIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDPromissoryNote}}</nobr></TD>
							<TD>Create Promissory note for billed patients.</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sPNListIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDListPromi}}</nobr></TD>
							<TD>List of Promissory Note.</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<tr>
							<TD width="1%">{{$sPfExcessIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDListPfExcess}}</nobr></TD>
							<TD>List of PF Excess.</TD>
						</tr>
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TABLE>
			<p>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
						<TBODY class="submenu">
							<TR>
							<TD class="submenu_title" colspan=3>Dialysis</TD>
							</tr>
							<TR>
	                            <TD width="1%">{{$sLDNewDialysisIcon}}</TD>
                            	<TD class="submenu_item" width=35%><nobr>{{$LDNewDialysis}}</nobr></TD>
                            	<TD>New Dialysis</TD>
	                        </tr>
	                        {{include file="common/submenu_row_spacer.tpl"}}
	                        <TR>
	                            <TD width="1%">{{$sLDListDialysisIcon}}</TD>
                            	<TD class="submenu_item" width=35%><nobr>{{$LDListDialysis}}</nobr></TD>
                            	<TD>List of Dialysis Patients</TD>
	                        </tr>
	                        {{include file="common/submenu_row_spacer.tpl"}}
						</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TABLE>
			<p>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3>Billing Management</TD>
						</tr>
<!--						<TR>                                                                                       
							<TD width="1%">{{$sManagePackageIcon}}</TD>                                            
							<TD class="submenu_item" width=35%><nobr>{{$LDManageClassification}}</nobr></TD>       
							<TD>Manage Packages </TD>                                                              
						</tr>   -->                                                                           
{{*						{{include file="common/submenu_row_spacer.tpl"}}				*}}	
						<TR>
							<TD width="1%">{{$sLDOtherServicesIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDOtherServices}}</nobr></TD>
							<TD>Manager for Miscellaneous Services</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sLDSocialReportsIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDBillReports}}</nobr></TD>
							<TD>Process transmittals to health insurances.</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
                        <TR>
                            <TD width="1%">{{$sLDTransmittalsHistIcon}}</TD>
                            <TD class="submenu_item" width=35%><nobr>{{$LDTransmittalsHistory}}</nobr></TD>
                            <TD>History of Transmittals.</TD>
                        </tr>
{{*                        {{include file="common/submenu_row_spacer.tpl"}}              *}}
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TABLE>
            <p>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
						<TBODY class="submenu">
							<TR>
	                            <TD class="submenu_title" colspan=3>Claims</TD>
							</tr>
							<TR>
	                            <TD width="1%">{{$sLDPostClaimIcon}}</TD>
	                            <TD class="submenu_item" width=35%><nobr>{{$LDClaimsPost}}</nobr></TD>
	                            <TD>Add/Edit Insurance Claims</TD>
	                        </tr>
	                        {{include file="common/submenu_row_spacer.tpl"}}
	                        <TR>
	                            <TD width="1%">{{$sLDClaimsHistIcon}}</TD>
	                            <TD class="submenu_item" width=35%><nobr>{{$LDClaimsHistory}}</nobr></TD>
	                            <TD>History of posted claims.</TD>
	                        </tr>
	{{*                        {{include file="common/submenu_row_spacer.tpl"}}              *}}
						</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TABLE>
			<p>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
						<TBODY class="submenu">
							<TR>
							<TD class="submenu_title" colspan=3>Administration</TD>
							</tr>
							<!-- <TR>
	                            <TD width="1%">{{$sLDBillingReportsIcon}}</TD>
                            	<TD class="submenu_item" width=35%><nobr>{{$LDBillingReports}}</nobr></TD>
                            	<TD>Reports of Billing</TD>
	                        </tr> -->
	                        <!-- {{include file="common/submenu_row_spacer.tpl"}} -->
	                        <TR>
	                            <TD width="1%">{{$sLDBillingReportsIcon_jasper}}</TD>
                            	<TD class="submenu_item" width=35%><nobr>{{$LDBillingReports_jasper}}</nobr></TD>
                            	<TD>Reports of Billing</TD>
	                        </tr>
	{{*                        {{include file="common/submenu_row_spacer.tpl"}}              *}}
						</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TABLE>
			<p>
			<a href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}}></a>
			<p>
			</blockquote>
