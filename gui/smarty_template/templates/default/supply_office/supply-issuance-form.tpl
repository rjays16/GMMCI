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

 <!--
<table border="0" cellspacing="0" cellpadding="0" align="center" width="800px">
<tr>
    <td>
    -->
    <div style="width:740px" align="center">
    <table border="0" cellspacing="0" cellpadding="2" align="center" width="100%" >
            <tr>
                <td align="left"><strong style="white-space:nowrap">Issuing Area:</strong><span id="sourceIss_area">{{$sAreaIssued}}</span></td>
                <td align="right"><strong style="white-space:nowrap">Requesting Area:</strong><span id="destinationIss_area">{{$sAreaDest}}</span></td>
            </tr>
        </table>
    </div>
    <div style="width:740px" align="center">
        
        <table border="0" cellspacing="1" cellpadding="1" align="" width="100%">
            <tbody>
                <tr>
                    <td class="submenu_title" width="50%">
                        Issuance Details 
                    </td>
                    <td class="submenu_title" >
                        Authorization
                    </td>
                </tr>
                <tr>
                    <td class="jedPanel" nowrap valign="top" >
                        <table width="95%" border="0" cellpadding="3" cellspacing="0" style="font:normal 12px Arial;" valign="top">
                            <tr>
                                <td align="right"  width="30%"><strong>Ref No:</strong></td>
                                <td width="120"  align="left">
                                    <input type="text" class="jedInput" id = "refno" name= "refno" size="10" style="font:bold 12px Arial" value ='{{$sRefNo}}' disabled />
                                </td>
                            </tr>
                            <tr>
                                <td align="right"  width="30%"><strong>Type:</strong></td>
                                <td width="120"  align="left">
                                    {{$sIssuanceType}}  
                                </td>
                            </tr> 
                            <tr>
                                <td align="right"  width="30%"><strong>Date:</strong></td>
                                <td width="120"  align="left">
                                    {{$sIssueDate}} {{$sIssueCalendar}}
                                </td>
                            </tr>                             
                        </table>
                           
                    </td>
                    <td class="jedPanel" nowrap valign="top">
                        <table width="100%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Arial;margin-top:3px;" valign="top">
                            <tr>
                                <td align="right" width="30%" valign="middle"><strong>Authorized By:</strong></td>
                                <td align="left" width="60%" valign="bottom">{{$sAuthorizedId}}</td>
                                <td align="left">{{$sAuthorizedButton}}</td>
<!--                                    <table>
                                        <tr>
                                        <td>
                                             {{$sAuthorizedId}}
                                        </td>
                                        <td>
                                             {{$sAuthorizedButton}}
                                        </td>
                                        </tr>
                                    </table>  -->
<!--                                </td>      -->                                
                            </tr>
                            <tr>
                                 <td align="right"><strong>Issued By:</strong></td>
                                 <td align="left" colspan="2">{{$sIssuingId}}</td>
<!--                                    <table>
                                        <tr>
                                        <td>
                                             {{$sIssuingId}} 
                                        </td>
                                        <td>
                                             {{$sIssueButton}}
                                        </td>
                                        </tr>
                                    </table> -->
<!--                                 </td>-->
                            </tr>                        
                        </table>
                    </td>
                </tr> 
            </tbody>
        </table>
    </div>        
    <br />
    <div style="width:740px" align="center">

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
                    <th width="5%" nowrap="nowrap">&nbsp;</th>
                    <th width="5%" nowrap="nowrap" align="left">Item No.</th>
                    <th width="*" nowrap="nowrap" align="left">Item Name</th>
                    <th width="10%" nowrap="nowrap" align="center">Available</th>
                    <th width="10%" nowrap="nowrap" align="center">Requested</th> 
                    <th width="5%" nowrap="nowrap" align="center">Unit</th>
                    <th width="10%" nowrap="nowrap" align="center">Unit Cost</th>
                    <th width="10%" nowrap="nowrap" align="center">Total Cost</th>
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

