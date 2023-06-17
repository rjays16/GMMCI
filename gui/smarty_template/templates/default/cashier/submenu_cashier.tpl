		<BLOCKQUOTE>
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE cellSpacing=0 cellPadding=0 width=600 class="submenu_group">
								<TBODY>
									<TR>
										<TD class="submenu_title" colspan=3>Process Payments</TD>
									</TR>
									{{$LDSegCashierRequests}}
									{{include file="common/submenu_row_spacer.tpl"}}
									<!--added by cha 11-06-2009 -->
<!--									{{$LDSegCashierWalkinRequest}}
									{{include file="common/submenu_row_spacer.tpl"}}-->
									<!--end cha -->
									{{$LDSegCashierOthers}}
									{{include file="common/submenu_row_spacer.tpl"}}
									{{$LDSegCashierBilling}}
									{{include file="common/submenu_row_spacer.tpl"}}
									{{$LDSegCashierNewDeposit}}
									{{include file="common/submenu_row_spacer.tpl"}}
									{{$LDSegCashierPanel}}
									{{include file="common/submenu_row_footer.tpl"}}
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
<!--			<BR/>
			<TABLE cellSpacing=0  width=600 class="submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE cellSpacing=1 cellPadding=3 width=600>
								<TBODY class="submenu">
									<TR><TD class="submenu_title" colspan=3>Cash Vouchers</TD></TR>
									{{$LDSegCashierListVoucher}}
									{{include file="common/submenu_row_spacer.tpl"}}
									{{$LDSegCashierRecentVoucher}}
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE> -->
			<BR/>
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
								<TBODY>
									<TR><TD class="submenu_title" colspan=3>Credit Memos</TD></TR>
									{{$LDSegCashierNewCM}}
									{{include file="common/submenu_row_spacer.tpl"}}
									{{$LDSegCashierArchivesCM}}
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
							<TABLE class="submenu_group" width=600>
								<TBODY>
									<TR><TD class="submenu_title" colspan=3>Process Deposits</TD></TR>
									{{$LDSegCashierNewPartialPayment}}
									{{include file="common/submenu_row_spacer.tpl"}}
									{{$LDSegCashierManageDeposit}}
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			<BR/>
-->
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
								<TBODY>
									<TR><TD class="submenu_title" colspan=3>Administration</TD></TR>
									{{$LDSegCashierArchives}}
									{{include file="common/submenu_row_spacer.tpl"}}
									{{$LDSegCashierServicesManager}}
									{{include file="common/submenu_row_spacer.tpl"}}
									{{$LDSegCashierORNoAssign}}
									{{include file="common/submenu_row_spacer.tpl"}}
									{{$LDSegCashierEditOR}}
									{{include file="common/submenu_row_spacer.tpl"}}
									{{$LDSegCashierReports}}
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
