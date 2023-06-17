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
      wjweds
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

<div style="width:90%">
    <table width="90%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="2" cellpadding="2">    
        <tbody>    
            <tr>
                <td align="left" class="jedPanelHeader" ><strong>Search options</strong></td>
            </tr>
            <tr>
                <td nowrap="nowrap" align="right" class="jedPanel">
                    <table width="100%" border="0" cellpadding="2" cellspacing="0">
                        <tr>
                            <td width="50" align="right">
                            {{$sIssDateCheckbox}}
                            </td>
                            <td width="5%" nowrap="nowrap" align="right"><label class="jedInput" for="chkdate">Filter by date</label></td>
                            <td>
                            {{$sIssDate}}
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="2">
                            <input class="jedButton" type="button" value="Search" onclick="search()"/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<table border="0" cellspacing="0" cellpadding="0" align="center" width="800px">
<tr>
    <td>
    <br>
    <div style="width:832px" align="center">
        <div class="dashlet">
        <table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="*">

                    <h1>Your Pending Issuance(s) for Acknowledgment: </h1>
                </td>
            </tr>
        </table>
        </div>
        <table id="issue-list" class="jedList" border="0" cellpadding="0" cellspacing="0" width="100%">
            <thead>
                <tr id="issue-list-header">
                    <th width="12%" nowrap="nowrap" align="center">Ref No.here</th>
                    <th width="14%" nowrap="nowrap" align="center">Issue Date</th>
                    <th width="20%" nowrap="nowrap" align="center">Source</th>
                    <th width="20%" nowrap="nowrap" align="center">Destination</th>
                    <th width="12%" nowrap="nowrap" align="center">Auth. by</th>
                    <th width="12%" nowrap="nowrap" align="center">Issued by</th>
                    <th width="10%" nowrap="nowrap" align="center">Details</th> 
                </tr>
            </thead>
            <tbody>
{{$sIssueItems}}
            </tbody>
        </table>  
        
    </div>
    </td>
</tr>
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

