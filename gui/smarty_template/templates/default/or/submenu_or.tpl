			<blockquote>
			<!--
			<div class="prompt">{{$LDOrDocs}}</div>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD width="1%">{{$sOrDocumentIcon}}</TD>
							<TD class="submenu_item" width=35%>{{$LDOrDocument}}</TD>
							<TD>{{$LDOrDocumentTxt}}</TD>
						</TR>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sQviewDocsIcon}}</TD>
							<TD class="submenu_item" width=35%>{{$LDQviewDocs}}</TD>
							<TD>{{$LDQviewTxtDocs}}</TD>
						</TR>

					</TBODY>
					</TABLE>
				</TD>
			</TR>

			</TABLE>

			<p>
			</p>
			-->
			<!--<div class="prompt">{{$LDOrNursing}}</div>-->
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3>OR Main Request</TD>
						</TR>
						<TR>
												<TD align="center" class="submenu_icon" >{{$main_new_request_icon}}</TD>
																<TD class="submenu_item">{{$main_new_request_link}}</TD>
																<TD class="submenu_text">{{$main_new_request_desc}}</TD>
												</TR>
												{{include file="common/submenu_row_spacer.tpl"}}
												<TR>
												<TD align="center" class="submenu_icon" >{{$list_main_icon}}</TD>
																<TD class="submenu_item">{{$list_main_link}}</TD>
																<TD class="submenu_text">{{$list_main_desc}}</TD>
												</TR>
												{{include file="common/submenu_row_spacer.tpl"}}
												<TR>
												<TD align="center" class="submenu_icon" >{{$schedule_main_icon}}</TD>
																<TD class="submenu_item">{{$schedule_main_link}}</TD>
																<TD class="submenu_text">{{$schedule_main_desc}}</TD>
												</TR>
												{{include file="common/submenu_row_spacer.tpl"}}
												<TR>
												<TD align="center" class="submenu_icon" >{{$pre_operation_main_icon}}</TD>
																<TD class="submenu_item">{{$pre_operation_main_link}}</TD>
																<TD class="submenu_text">{{$pre_operation_main_desc}}</TD>
												</TR>
												{{include file="common/submenu_row_spacer.tpl"}}
												<TR>
												<TD align="center" class="submenu_icon" >{{$post_operation_main_icon}}</TD>
																<TD class="submenu_item">{{$post_operation_main_link}}</TD>
																<TD class="submenu_text">{{$post_operation_main_desc}}</TD>
												</TR>
												<TR>
													<TD align="center" class="submenu_icon" >{{$resched_main_icon}}</TD>
													<TD class="submenu_item">{{$resched_main_link}}</TD>
													<TD class="submenu_text">{{$resched_main_desc}}</TD>
												</TR>
												{{include file="common/submenu_row_spacer.tpl"}}
												<TR>
												<TD align="center" class="submenu_icon" >{{$register_newborn_icon}}</TD>
																<TD class="submenu_item">{{$register_newborn_link}}</TD>
																<TD class="submenu_text">{{$register_newborn_desc}}</TD>
												</TR>
												{{include file="common/submenu_row_spacer.tpl"}}
												<TR>
												<TD align="center" class="submenu_icon" >{{$or_delivery_record_icon}}</TD>
																<TD class="submenu_item">{{$or_delivery_record_link}}</TD>
																<TD class="submenu_text">{{$or_delivery_record_desc}}</TD>
												</TR>
												{{include file="common/submenu_row_spacer.tpl"}}
												<TR>
												<TD align="center" class="submenu_icon" >{{$or_deaths_icon}}</TD>
																<TD class="submenu_item">{{$or_deaths_link}}</TD>
																<TD class="submenu_text">{{$or_deaths_desc}}</TD>
												</TR>
												{{include file="common/submenu_row_spacer.tpl"}}
												<tr>
													<td align="center" class="submenu_icon">{{$or_main_charges_icon}}</td>
													<td class="submenu_item" width="35%0">{{$or_main_charges_link}}</td>
													<td class="submenu_text">{{$or_main_charges_desc}}</td>
												</tr>
												{{include file="common/submenu_row_spacer.tpl"}}
												<tr>
													<td align="center" class="submenu_icon">{{$or_main_calendar_icon}}</td>
													<td class="submenu_item" width="35%0">{{$or_main_calendar_link}}</td>
													<td class="submenu_text">{{$or_main_calendar_text}}</td>
												</tr>
																								<!--<TR>
														<TD width="1%">{{$sORServicesReportIcon}}</TD>
														<TD class="submenu_item" width=35%>{{$LDORServicesReport}}</TD>
														<TD>{{$LDServicesReportTxt}}</TD>
												</TR>
												{{include file="common/submenu_row_spacer.tpl"}}-->
										</TBODY>
										</TABLE>
								</TD>
						</TR>

						</TABLE>

						<!--commented by VAN 01-28-08 -->
						<!--
						<TR>
							<TD class="submenu_item" width="35%">{{$sgOprequest}} </TD>
							<TD>test Or request</TD>
						</TR>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD class="submenu_item" width=35%>{{$sgOrRequest}}</TD>
							<TD>OR Request</TD>
						</TR>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sNewORRequestIcon}}</TD>
							<TD class="submenu_item" width=35%>{{$segNewORRequest}}</TD>
							<TD>Create new OR request</TD>
						</TR>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sListORCasesIcon}}</TD>
							<TD class="submenu_item" width=35%>{{$segListORCases}}</TD>
							<TD>List of OR Cases</TD>
						</TR>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sOrLogBookIcon}}</TD>
							<TD class="submenu_item" width=35%>{{$LDOrLogBook}}</TD>
							<TD>{{$LDOrLogBookTxt}}</TD>
						</TR>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sORNOCQuickViewIcon}}</TD>
							<TD class="submenu_item" width=35%>{{$LDORNOCQuickView}}</TD>
							<TD>{{$LDQviewTxtNurse}}</TD>
						</TR>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sORNOCSchedulerIcon}}</TD>
							<TD class="submenu_item" width=35%>{{$LDORNOCScheduler}}</TD>
							<TD>{{$LDDutyPlanTxt}}</TD>
						</TR>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sOnCallDutyIcon}}</TD>
							<TD class="submenu_item" width=35%>{{$LDOnCallDuty}}</TD>
							<TD>{{$LDOnCallDutyTxt}}</TD>
						</TR>

					</TBODY>
					</TABLE>
				</TD>
			</TR>

			</TABLE>
			<!--
			<p>
			</p><div class="prompt">{{$LDORAnesthesia}}</div>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">

						<TR>
							<TD width="1%">{{$sORAnaQuickViewIcon}}</TD>
							<TD class="submenu_item" width=35%>{{$LDORAnaQuickView}}</TD>
							<TD>{{$LDQviewTxtAna}}</TD>
						</TR>
						{{include file="common/submenu_row_spacer.tpl"}}

						<TR>
							<TD width="1%">{{$sORAnaNOCSchedulerIcon}}</TD>
							<TD class="submenu_item" width=35%>{{$LDORAnaNOCScheduler}}</TD>
							<TD>{{$LDDutyPlanTxt}}</TD>
						</tr>

					</TBODY>
					</TABLE>
				</TD>
			</TR>

			</TABLE>
			-->
						<p>
						</p>
						<!-- Added by Cherry 02-15-10 -->
						<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
						<TBODY>
						<TR>
								<TD>
										<TABLE cellSpacing=1 cellPadding=3 width=600>
										<TBODY class="submenu">
												<TR>
														<TD class="submenu_title" colspan=3>ASU Request</TD>
												</TR>
												<TR>
												<TD align="center" class="submenu_icon" >{{$asu_new_request_icon}}</TD>
																<TD class="submenu_item">{{$asu_new_request_link}}</TD>
																<TD class="submenu_text">{{$asu_new_request_desc}}</TD>
												</TR>
												{{include file="common/submenu_row_spacer.tpl"}}
												<TR>
												<TD align="center" class="submenu_icon" >{{$list_asu_icon}}</TD>
																<TD class="submenu_item">{{$list_asu_link}}</TD>
																<TD class="submenu_text">{{$list_asu_desc}}</TD>
												</TR>
												{{include file="common/submenu_row_spacer.tpl"}}
												<TR>
												<TD align="center" class="submenu_icon" >{{$approve_asu_icon}}</TD>
																<TD class="submenu_item">{{$approve_asu_link}}</TD>
																<TD class="submenu_text">{{$approve_asu_desc}}</TD>
												</TR>
												{{include file="common/submenu_row_spacer.tpl"}}
												<tr>
												<TD align="center" class="submenu_icon" >{{$pre_operation_icon}}</TD>
																<TD class="submenu_item">{{$pre_operation_link}}</TD>
																<TD class="submenu_text">{{$pre_operation_desc}}</TD>
												</tr>
												{{include file="common/submenu_row_spacer.tpl"}}
												<TR>
													<TD align="center" class="submenu_icon" >{{$post_operation_icon}}</TD>
													<TD class="submenu_item">{{$post_operation_link}}</TD>
													<TD class="submenu_text">{{$post_operation_desc}}</TD>
												</TR>
												{{include file="common/submenu_row_spacer.tpl"}}
												<TR>
													<TD align="center" class="submenu_icon" >{{$resched_asu_icon}}</TD>
													<TD class="submenu_item">{{$resched_asu_link}}</TD>
													<TD class="submenu_text">{{$resched_asu_desc}}</TD>
												</TR>
												{{include file="common/submenu_row_spacer.tpl"}}
												<tr>
													<td align="center" class="submenu_icon">{{$or_asu_calendar_icon}}</td>
													<td class="submenu_item" width="35%0">{{$or_asu_calendar_link}}</td>
													<td class="submenu_text">{{$or_asu_calendar_text}}</td>
												</tr>
																								<!--<TR>
														<TD width="1%">{{$sORServicesReportIcon}}</TD>
														<TD class="submenu_item" width=35%>{{$LDORServicesReport}}</TD>
														<TD>{{$LDServicesReportTxt}}</TD>
												</TR>
												{{include file="common/submenu_row_spacer.tpl"}}-->
										</TBODY>
										</TABLE>
								</TD>
						</TR>

						</TABLE>

			<!--added by VAN 04-22-08-->
			<p>
			</p>
			<!--<div class="prompt">{{$LDORServicesReportDiv}}</div>-->
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<TR>
							<TD class="submenu_title" colspan=3>Operation Service Management</TD>
						</TR>
						<tr>
							<TD align="center" class="submenu_icon" >{{$package_icon}}</TD>
							<TD class="submenu_item">{{$package_link}}</TD>
							<TD class="submenu_text">{{$package_desc}}</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$sORServicesReportIcon}}</TD>
							<TD class="submenu_item" width=35%>{{$LDORServicesReport}}</TD>
							<TD>{{$LDServicesReportTxt}}</TD>
						</TR>
						{{include file="common/submenu_row_spacer.tpl"}}
						<!-----added by CELSY 06-26-10-->
						<!--<TR>
							<TD width="1%">{{$sORScheduleIcon}}</TD>
							<TD class="submenu_item" width=35%>{{$LDORSchedule}}</TD>
							<TD>{{$LDScheduleTxt}}</TD>
						</TR>
						{{include file="common/submenu_row_spacer.tpl"}} -->
						<!-----added by CELSY 07-13-10-->
						<TR>
							<TD width="1%">{{$sORChecklistIcon}}</TD>
							<TD class="submenu_item" width=35%>{{$LDORChecklistMgr}}</TD>
							<TD>{{$LDChecklistMgrTxt}}</TD>
						</TR>
						<!--end CELSY-->
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$or_anesthesia_mgr_icon}}</TD>
							<TD class="submenu_item" width=35%>{{$or_anesthesia_mgr_link}}</TD>
							<TD>{{$or_anesthesia_mgr_desc}}</TD>
						</TR>
						<!-- Added by Cherry 11-10-10-->
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$or_sutures_icon}}</TD>
							<TD class="submenu_item" width=35%>{{$or_sutures_link}}</TD>
							<TD>{{$or_sutures_desc}}</TD>
						</TR>
						<!-- End Cherry -->
						<!--added by angelo m. 09.17.2010-->
						<!--start-->
						{{include file="common/submenu_row_spacer.tpl"}}
						<TR>
							<TD width="1%">{{$Sor_room_icon}}</TD>
							<TD class="submenu_item" width=35%>{{$Sor_room_mgr_link}}</TD>
							<TD>{{$Sor_room_mgr_desc}}</TD>
						</TR>
						<!--end-->
					</TBODY>
					</TABLE>
				</TD>
			</TR>

			</TABLE>

			{{$sOnHoverMenu}}

			<p>
			<a href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}} /></a>
			</p><p>
			</p></blockquote>
