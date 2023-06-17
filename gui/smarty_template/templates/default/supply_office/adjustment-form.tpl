{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<script type="text/javascript" language="javascript">
<!--
    function openWindow(url) {
        window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
    }
-->
</script>
{{if $bShowQuickKeys}}
<style type="text/css">
<!--
    table.quickKey td.qkimg{
        font:bold 11px Tahoma;
        vertical-align:middle;
    }
    
    table.quickKey td.qktxt {
        width:70px;
        padding:2px 4px;
        font:bold 11px Tahoma;
        vertical-align:middle;
        color:#007000;
    }
-->
</style>

<div style="width:80%">
    <table border="0" cellspacing="1" cellpadding="2">
        <tr>
            <td class="jedPanelHeader">Quick keys</td>
        </tr>
        <tr>
            <td style="background-color:#fffeed; border:1px solid #ebeac4">
                <table class="quickKey" cellpadding="0" cellspacing="1" border="0">
                    <tr>

                        <td class="qkimg" nowrap="nowrap" ><img src="{{$sRootPath}}images/shortcut-f2.png" /></td>
                        <td class="qktxt">Add items</td>
                        
                        <td    class="quickKey" nowrap="nowrap"><img src="{{$sRootPath}}images/shortcut-f3.png" /></td>
                        <td class="qktxt">Clear list</td>
                        
                        <td    class="quickKey" nowrap="nowrap"><img src="{{$sRootPath}}images/shortcut-f9.png" /></td>
                        <td class="qktxt">Person select</td>
                        
                        <td    class="quickKey" nowrap="nowrap"><img src="{{$sRootPath}}images/shortcut-f12.png" /></td>
                        <td class="qktxt">Save/Submit</td>

                    </tr>
                </table>    
            </td>
        </tr>
    </table>
</div>
{{/if}}
{{$sFormStart}}

    <!--<div style="width:800px" align="center">-->
<div style="width:85% ;position: relative" align="center">        
    <table border="0" cellspacing="1" cellpadding="1" align="center" width="100%" >
        <tbody>        
            <tr>
                <td>
                    <table border="0" cellspacing="1" cellpadding="1" align="center" width="100%" >
                        <tr>
                            <td align="left"><strong style="white-space:nowrap">Area:</strong><span id="sourceIss_area">{{$sAdjArea}}</span></td>
                            <td align="right"><strong style="white-space:nowrap">Date Adjusted:</strong><span id="destinationIss_area">{{$sAdjDate}} {{$sAdjCalendar}}</span></td>
                        </tr>   
                    </table>
                </td>
            </tr>
            <tr><td>        
                <table border="0" cellspacing="1" cellpadding="1" align="center" width="100%">
                    <tbody>
                        <tr>
                            <td class="jedPanelHeader" width="50%">
                                Request Details 
                            </td>
                            <td class="jedPanelHeader" >
                                Authorization
                            </td>
                        </tr>
                        <tr>
                            <td class="jedPanel" nowrap valign="middle" >
                                <table width="95%" border="0" cellpadding="3" cellspacing="0" style="font:normal 12px Arial;" >
                                    <tr>
                                        <td align="right"  width="30%"><strong>Ref No:</strong></td>
                                        <td width="120"  align="left">
                                            {{$sAdjRefno}}  
                                            {{$sResetRefNo}} 
                                        </td>
                                    </tr>                           
                                </table>
                            </td>
                            <td class="jedPanel" nowrap align="middle">
                                <table width="95%" border="0" cellpadding="3" cellspacing="0" style="font:normal 12px Arial;" >
                                    <tr>
                                        <td align="right"  width="30%"><strong>Authorized By:</strong></td>
                                        <td align="left"  width="120">
                                            {{$sAdjId}} 
                                        </td>                                      
                                    </tr>                        
                                </table>
                            </td>
                        </tr> 
                    </tbody>
                </table>
            </td>
         </tr>
         <tr><td>
            <table border="0" cellspacing="1" cellpadding="1" align="center" width="100%">
               <tbody>
                   <tr>
                       <td class="jedPanel" width="10%">
                           Remarks:
                       </td>
                       <td class="jedPanel" nowrap align="left">{{$sRemarks}}</td>                                
                   </tr>
               </tbody>
           </table>
        </td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td>
            <table width="100%">
                <tr>
                    <td width="50%" align="left">
                        {{$sBtnAddItem}}
                        {{$sBtnEmptyList}}
                        {{$sBtnPDF}}
                    </td>
                    <td align="right">
                        {{$sContinueButton}}
                        {{$sBreakButton}}
                    </td>
                </tr>
            </table>
        </td></tr>
        <tr><td>
            <table id="order-list" class="jedList" border="0" cellpadding="0" cellspacing="0" width="100%">
                <thead>
                    <tr id="order-list-header">
                        <th width="3%" nowrap="nowrap">&nbsp;</th>
                        <th width="14%" nowrap="nowrap" align="left">Item No.</th>
                        <th width="15%" nowrap="nowrap" align="left">Item Name</th>
                        <th width="5%" nowrap="nowrap" align="center">Qty</th>
                        <th width="5%" nowrap="nowrap" align="center">Unit</th>
                        <th width="11%" colspan="2" nowrap="nowrap" align="center">Serial</th>
                        <th width="11%" colspan="1" nowrap="nowrap" align="center">Lot No</th>
                        <th width="11%" colspan="2" nowrap="nowrap" align="center">Expiry</th>
                        <th width="11%" colspan="1" nowrap="nowrap" align="center">Unit Cost</th>
                        <th width="8%" nowrap="nowrap" align="center">Adj Qty</th>
                        <th width="8%" nowrap="nowrap" align="center">+/- Qty</th>                    
                        <th width="20%" nowrap="nowrap" align="center">Reason</th>
                    </tr>
                </thead>
                <tbody>
                    {{$sAdjItems}}
                </tbody>
            </table>  
        </td></tr>
        </tbody>
    </table>               

{{$sHiddenInputs}}
{{$jsCalendarSetup}}
<br/>
<img src="" vspace="2" width="1" height="1"><br/>
{{$sDiscountControls}}
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>
<br/>

<div style="width:80%">
{{$sUpdateControlsHorizRule}}
{{$sUpdateOrder}}
{{$sCancelUpdate}}
</div>


</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}     

