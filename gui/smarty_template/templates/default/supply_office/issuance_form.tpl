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

    <div style="width:800px" align="center">
        <br><br>
        <table border="0" cellspacing="2" cellpadding="2" align="center" width="100%">
            <tbody>
                <tr>
                    <td class="jedPanelHeader" width="*">
                        Issuance
                    </td>
                </tr>
                <tr>
                    <td class="jedPanel">
                    <br>
                        <table width="95%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Arial" >
                            <tr>
                                <td colspan="33%">
                                    <table border="0" cellpadding="0" cellspacing="0" style="font:normal 12px Arial" >
                                        <tr>
                                            <td align="left" nowrap="nowrap" width="50"><strong>Ref No:</strong></td>
                                            <td align="center" nowrap="nowrap" width="180">
                                                {{$sRefno}}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td colspan="33%">
                                    <table border="0" cellpadding="0" cellspacing="0" style="font:normal 12px Arial" >
                                        <tr>
                                            <td align="right" nowrap="nowrap"><strong>Date of Issue:</strong></td>
                                            <td align="center" nowrap="nowrap" width="100">
                                                {{$sIssueDate}}
                                            </td>
                                            <td align="left" nowrap="nowrap" width="20">
                                                {{$sIssueCalendar}}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td colspan="33%">
                                    <table border="0" cellpadding="0" cellspacing="0" style="font:normal 12px Arial" >
                                        <tr>
                                            <td align="right" nowrap="nowrap"><strong>Dept:</strong></td>
                                            <td align="center" nowrap="nowrap" width="70">
                                                {{$sDepartmentIssued}}
                                            </td>  
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="33%">
                                    <table border="0" cellpadding="0" cellspacing="0" style="font:normal 12px Arial" >
                                        <tr>
                                           <td align="right" nowrap="nowrap"><strong>Authorized By:</strong></td>
                                            <td align="center" nowrap="nowrap" width="140">
                                                {{$sAuthorizedId}}
                                            </td>
                                            <td align="center" nowrap="nowrap" width="20" >
                                                {{$sAuthorizedButton}}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td colspan="33%">
                                    <table border="0" cellpadding="0" cellspacing="0" style="font:normal 12px Arial" >
                                        <tr>
                                            <td align="right" nowrap="nowrap"><strong>Issued By:</strong></td>
                                            <td align="center" nowrap="nowrap" width="140">
                                                {{$sIssuingId}}
                                            </td>
                                            <td align="center" nowrap="nowrap" width="20">
                                                {{$sIssueButton}}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td colspan="33%">
                                </td>
                            </tr>
                        </table>
                        <br>
                    </td>
                </tr>     
            </tbody>
        </table>
        
    </div>

    <br />

    <div style="width:760px" align="center">

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
        <table id="order-list" class="jedList" border="0" cellpadding="0" cellspacing="0" width="100%">
            <thead>
                <tr id="order-list-header">
                    <th width="1%" nowrap="nowrap">&nbsp;</th>
                    <th width="10%" nowrap="nowrap" align="left">Item No.</th>
                    <th width="*" nowrap="nowrap" align="left">Item Description</th>
                    <th width="4%" nowrap="nowrap" align="center">Consigned</th>
                    <th width="10%" align="center" nowrap="nowrap">Quantity</th>
                    <th width="10%" align="right" nowrap="nowrap">Price(Orig)</th>
                    <th width="10%" align="right" nowrap="nowrap">Price(Adj)</th>
                    <th width="10%" align="right" nowrap="nowrap">Acc. Total</th>
                </tr>
            </thead>
            <tbody>
{{$sIssueItems}}
            </tbody>
        </table>  
        
    </div>

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

