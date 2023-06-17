		 <blockquote>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
	<!-- 				<td>
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
				</td>-->
				</TR>
			</tbody>
			</table>
			<p>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
					<tr>
						<TD class="submenu_title" colspan=3>Test Request</TD>
					</tr>							
		
					{{$LDTestRequestRadio}}

					{{include file="common/submenu_row_spacer.tpl"}}
					
					{{$LDViewAssignRequest}}
					
					{{include file="common/submenu_row_spacer.tpl"}}

					{{$LDNurseTestRequestRadio}}
					{{*$LDTestReception*}}

					{{*include file="common/submenu_row_spacer.tpl"*}}

					{{*$LDDicomImages*}}

					{{*include file="common/submenu_row_spacer.tpl"*}}

					{{*$LDUploadDicom*}}

					{{*include file="common/submenu_row_spacer.tpl"*}}

					{{*$LDSelectViewer*}}
					{{*include file="common/submenu_row_spacer.tpl"*}}

					{{*$LDNews*}}
					
					</TBODY>
					</TABLE>
				</TD>
			</TR>
			</TBODY>
			</TABLE>

			<p>
			<p>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
			<TBODY>
			<TR>
				<TD>
					<TABLE cellSpacing=1 cellPadding=3 width=600>
					<TBODY class="submenu">
						<tr>
							<TD class="submenu_title" colspan=2>Administration</TD>
						</tr>
						<tr>
							<TD class="submenu_item" width=35%><nobr>{{$LDRadioServices}}</nobr></TD>
							<TD>Manage radiology services options</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<tr>
							<TD class="submenu_item" width=35%><nobr>{{$LDRadioRequestList}}</nobr></TD>
							<TD>View, edit and delete Radiology service requests</TD>
						</tr>
						{{include file="common/submenu_row_spacer.tpl"}}
						<tr>
							<td class="submenu_item" width=35%><nobr>{{$LDRadioDOCScheduler}}</nobr></td>
							<td>Resident in-charge scheduler, plan, view, update, edit, etc.</td>
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
