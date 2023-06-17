{{foreach name=menuFrames key=frameTitle item=menuFrame from=$aMenu}}
			<TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
				<TBODY>
					<TR>
						<TD>
							<TABLE cellSpacing=0 cellPadding=0 width=600 class="submenu_group">
								<TBODY>
									<TR>
										<TD class="submenu_title" colspan=3>{{$frameTitle}}</TD>
									</TR>
	{{foreach name=menuItems item=item from=$menuFrame}}
									<TR>
										<TD align="center" class="submenu_icon"><img {{$item.icon}} /></td>
										<TD class="submenu_item"><a href="{{$item.href}}">{{$item.label}}</a></TD>
										<TD class="submenu_text">{{$item.description}}</TD>
									</TR>
		{{if $smarty.foreach.menuItems.last}}
									{{include file="common/submenu_row_footer.tpl"}}
		{{else}}
									{{include file="common/submenu_row_spacer.tpl"}}
		{{/if}}
	{{/foreach}}
								</TBODY>
							</TABLE>
						</TD>
					</TR>
				</TBODY>
			</TABLE>
			<BR/>
{{/foreach}}
			<A href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}}></a>
			<BR/>
