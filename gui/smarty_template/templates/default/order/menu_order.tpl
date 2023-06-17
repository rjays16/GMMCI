		<BLOCKQUOTE>
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
								<TBODY>
<tr>
	<TD class="submenu_title" colspan=3>Pharmacy Orders{{if $sCurrentArea}} (Current Area: {{$sCurrentArea}}){{else}}&nbsp;<span style="font-size:11px; font-weight:normal">(click on {{$sSetAreaLink}} to select default Area)</span>{{/if}}</TD>
</tr>
{{$LDSegPharmaNewOrder}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaOrderManage}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaOrderServe}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaSetArea}}
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
<tr>
	<TD class="submenu_title" colspan=3>Ward Stocks</TD>
</tr>
{{$LDSegPharmaNewStock}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaRecentStocks}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaStocksManage}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaWardManage}}
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
<tr>
	<TD class="submenu_title" colspan=3>Pharmacy Returns</TD>
</tr>
{{$LDSegPharmaNewReturn}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaNewRefund}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDSegPharmaReturnList}}
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
<tr>
	<TD class="submenu_title" colspan=3>Administration</TD>
</tr>
{{$LDPharmaDb}}
{{include file="common/submenu_row_spacer.tpl"}}
{{$LDPharmaReports}}
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
