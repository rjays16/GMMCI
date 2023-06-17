		<blockquote>
<!--			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
				<TBODY>
						<TR>
 				<td>
					<table cellSpacing=1 cellPadding=3 width=600>
					<tbody class="submenu">
						<tr>
							<td class="submenu_title" colspan=2>Transactions</td>
						</tr>
						<tr>
							<td class="submenu_item" width=30%><nobr>{{$LDCreateTransaction}}</nobr></td>
							<td>Create Service Transaction</td>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<tr>
							<td class="submenu_item" witdth=30%><nobr>{{$LDManageTransactions}}</nobr></td>
							<td>View, edit and delete service transactions</td>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<tr>			
							<td class="submenu_item" widht=30%><nobr>{{$LDServicePrices}}</nobr></td>
							<td>Set the price for Radiology prices</td>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<tr>			
							<td class="submenu_item" widht=30%><nobr>{{$LDViewAssignRequest}}</nobr></td>
							<td>{{$LDViewAssignRequestTxt}}</td>
						</tr>
					</tbody>
					</table>
				</td>
					</TR>
				<TBODY>
			</TABLE>
			<p>
-->
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE cellSpacing=1 cellPadding=3 width=600>
								<TBODY class="submenu">
									<tr>
										<TD class="submenu_title" colspan=3>Test Request</TD>
									</tr>
									{{$LDCreateNewRadioServiceRequest}}
									{{include file="common/submenu_row_spacer.tpl"}}

									{{$LDRadioServiceRequestList}}
									{{include file="common/submenu_row_spacer.tpl"}}
									
									<!--<tr>
										<TD align="center" width="6%">{{$sRadioTechIcon}}</TD>
										<TD class="submenu_item" width=37%><nobr>{{$LDRadioTech}}</nobr></TD>
										<TD>Record served radiological (XRAY, CT-SCAN, MRI, ULTRASOUND and others) requests.</TD>
									</tr> -->
									
									<!-- {{$LDRadioScheduleRequestCalendar}}
									{{include file="common/submenu_row_spacer.tpl"}} -->
									
									{{$LDRadioScheduleRequestList}}
									{{include file="common/submenu_row_spacer.tpl"}}
									
									{{$LDUndoneRequest}}
									{{include file="common/submenu_row_spacer.tpl"}}

									{{$LDDoneRequest}}
                                    {{include file="common/submenu_row_spacer.tpl"}}
                                    
                                    {{$LDUnifiedResults}}
                                    
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			<p></p>
			<!--
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE cellSpacing=1 cellPadding=3 width=600>
								<TBODY class="submenu">
									<tr>
										<TD class="submenu_title" colspan=3>
										Radiology Borrowing System
										</TD>
									</tr>
									{{$LDRadioPatientList}}
									{{include file="common/submenu_row_spacer.tpl"}}

									{{$LDRadioBorrowList}}
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			-->
			<!-- <TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE cellSpacing=1 cellPadding=3 width=600>
								<TBODY class="submenu">
									<tr>
										<TD class="submenu_title" colspan=3>
										Radiology Borrowing System
										</TD>
									</tr>
									<tr>
										<TD align="center" width="6%">{{$sRadioPatientListIcon}}</TD>
										<TD class="submenu_item" width=37%><nobr>{{$LDRadioPatientList}}</nobr></TD>
										<TD>List of all radiology patients</TD>
									</tr>
									{{include file="common/submenu_row_spacer.tpl"}}
									<tr>
										<TD align="center">{{$sRadioBorrowListIcon}}</TD>
										<TD class="submenu_item" width=37%><nobr>{{$LDRadioBorrowList}}</nobr></TD>
										<TD>List of all borrowed films</TD>
									</tr>
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE> -->
			<p></p>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<tr>
							<TD class="submenu_title" colspan=3>Administration</TD>
						</tr>
						<!--edited by VAN 03-15-08 -->
						<!--
						<tr>
							<TD width="6%" align="center">{{$sRadioServicesIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDRadioServicesOLD}}</nobr></TD>
							<TD>Manage radiology services options</TD>
						</tr>
						
						{{include file="common/submenu_row_spacer.tpl"}}
						-->
						<tr>
							<TD align="center" width="6%">{{$sRadioServicesIcon}}</TD>
							<TD class="submenu_item" width=35%><nobr>{{$LDRadioServices}}</nobr></TD>
							<TD>Manage radiology services options</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<tr>
							<TD align="center">{{$sRadioServicesGroupIcon}}</TD>
							<TD class="submenu_item" width=37%><nobr>{{$LDRadioServicesGroups}}</nobr></TD>
							<TD>Manage radiology group options</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<!-- added by VAN 07-07-08 -->
						<tr>
							<TD align="center">{{$sRadioFindingCodeIcon}}</TD>
							<TD class="submenu_item" width=37%><nobr>{{$LDRadioFindingCode}}</nobr></TD>
							<TD>Manage radiology finding's code</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<tr>
							<TD align="center">{{$sRadioImpressionCodeIcon}}</TD>
							<TD class="submenu_item" width=37%><nobr>{{$LDRadioImpressionCode}}</nobr></TD>
							<TD>Manage radiology impression's code</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<!-- <tr>
							<TD align="center">{{$sRadioDoctorPartnerIcon}}</TD>
							<TD class="submenu_item" width=37%><nobr>{{$LDRadioDoctorPartner}}</nobr></TD>
							<TD>Manage radiology's co-reader physicians for film reading</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<tr>
							<TD align="center">{{$sRadioDOCSchedulerIcon}}</TD>
							<td class="submenu_item" width=35%><nobr>{{$LDRadioDOCScheduler}}</nobr></td>
							<td>Resident in-charge scheduler, plan, view, update, edit, etc.</td>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}} -->
						<tr>
							<TD align="center">{{$sRadioReportIcon}}</TD>
							<td class="submenu_item" width=35%><nobr>{{$LDRadioReport}}</nobr></td>
							<td>View and print specific status reports</td>
						</tr>
						<!--{{include file="common/submenu_row_spacer.tpl"}}-->
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
