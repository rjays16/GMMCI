<blockquote>
<TABLE cellSpacing=0 cellPadding=0 border=0 class="submenu_frame" style="    -moz=border-radius-bottomleft: 4px;    ">
    <TBODY>
    <TR>
        <TD>
            <TABLE cellSpacing=1 cellPadding=3 width=600>
                <TBODY class="submenu">
                    <tr>
                        <td class="submenu_title" colspan="3">{{$SubMenuTitle}}</td>
                    </tr>
                   <!-- {{$segRegionMngr}} -->
                      {{$AddressNew}}
                    {{include file='common/submenu_row_spacer.tpl'}}
                   <!-- {{$segProvinceMngr}} -->
                      {{$AddressList}}
                    {{include file='common/submenu_row_spacer.tpl'}}
                      {{$AddressSearch}}
<!-----no longer needed, conferred with BKC, 10-26-2007, fdp-----------
                    {{$segAddress}}
                    {{include file='common/submenu_row_spacer.tpl'}}
-----------until here only------------------fdp------------------------>
                </TBODY>
            </TABLE>
        </TD>
    </TR>
    </TBODY>
</TABLE>
<p>
<a href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}}></a>
</blockquote>