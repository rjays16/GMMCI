<BLOCKQUOTE>
    <TABLE cellSpacing=0  width=600 class="seg_submenu_frame" cellpadding="0">
        <TBODY>
            <TR>
                <TD>
                    <TABLE class="submenu_group" cellSpacing=0 cellPadding=0 width=600>
                        <TBODY>
                            <tr>
                                <TD class="submenu_title" colspan=3>Inter-department Transactions</TD>
                            </tr>
                            {{$LDSegSupplyRequest}}
                            {{include file="common/submenu_row_footer.tpl"}}
                            {{$LDRequestsHistory}}
                            {{include file="common/submenu_row_footer.tpl"}}
                            {{$LDSegSupplyIssuance}}
                            {{include file="common/submenu_row_spacer.tpl"}}                  
                            {{$LDSegSupplyAcknowledge}}
                            {{include file="common/submenu_row_spacer.tpl"}}
                            {{$LDIssuanceHistory}}
                            {{include file="common/submenu_row_spacer.tpl"}}
                            {{$LDInvPostedRequests}}
                            {{include file="common/submenu_row_spacer.tpl"}}
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
                                <TD class="submenu_title" colspan=3>Delivery</TD>
                            </tr>
                            {{$LDSegSupplyDelivery}}
                            {{include file="common/submenu_row_spacer.tpl"}}                  
                            {{$LDSegSupplyDeliveries}}
                            {{include file="common/submenu_row_spacer.tpl"}}
                          <!--   {{$LDSegSupplyExtRequest}}
                            {{include file="common/submenu_row_spacer.tpl"}}                  
                            {{$LDSegSupplyExtRequestsHistory}}
                            {{include file="common/submenu_row_spacer.tpl"}}   -->        
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
                                <TD class="submenu_title" colspan=3>Adjustment</TD>
                            </tr>
                            {{$LDSegSupplyAdjustment}}
                            {{include file="common/submenu_row_spacer.tpl"}}                                  
                            <!-- {{$LDSegSupplyAdjustmentHistory}}
                            {{include file="common/submenu_row_spacer.tpl"}} -->  
                            {{$LDBulkAdjustments}}
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
                            {{$LDSegInvReport}}
                            {{include file="common/submenu_row_spacer.tpl"}}
                            {{$LDSegStockCard}}
                            {{include file="common/submenu_row_spacer.tpl"}}
                            {{$LDSegInvProdDBank}}
                            {{include file="common/submenu_row_spacer.tpl"}}                 
                        </TBODY>
                    </TABLE>                            
                </TD>
            </TR>
        </TBODY>
    </TABLE>        
    <A href="{{$breakfile}}"><img {{$gifClose2}} alt="{{$LDCloseAlt}}" {{$dhtml}}></a>
</BLOCKQUOTE>
