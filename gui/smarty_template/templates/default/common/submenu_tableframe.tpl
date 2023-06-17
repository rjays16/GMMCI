<TABLE cellSpacing=0 cellPadding=0 border=0 class="submenu_frame">
	<TBODY>
	<TR>
		<TD>
			<TABLE cellSpacing=0 cellPadding=0  class="submenu">
 				<TBODY>
					{{if $sSubMenuRows}}
						{{$sSubMenuRows}}
					{{/if}}

					{{if $sSubMenuRowsIncludeFile}}
						{{include file=$sSubMenuRowsIncludeFile}}
					{{/if}}
				</TBODY>
			</TABLE>
		</TD>
	</TR>
	</TBODY>
</TABLE>
<a href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}}></a>
