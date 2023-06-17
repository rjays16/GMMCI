{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<script type="text/javascript" language="javascript">
<!--
    function openWindow(url) {
        window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
    }
-->
</script>
{{if $bShowQuickKeys}}

{{/if}}
{{$sFormStart}}

<table border="0" cellspacing="0" cellpadding="0" align="center" width="690px">
<tr>
    <td>
    <br>
    <div style="width:680px" align="center">
        <div class="dashlet">
        <table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="*">

                    <h1>Issuance Details - Refno: {{$sHeaderRef}}</h1>
                </td>
            </tr>
        </table>
        </div>
        <table width="100%">
            <tr>
                <td width="50%" align="left">
                    {{$sApproveAll}} {{$sDenyAll}}
                </td>
                <td align="right">
                    {{$sContinueButton}}
                </td>
            </tr>
        </table>
        <table id="issue-list" class="jedList" border="0" cellpadding="0" cellspacing="0" width="100%">
            <thead>
                <tr id="issue-list-header">
                   <th width="10%" nowrap="nowrap" align="center">Code</th>
                    <th width="*" nowrap="nowrap" align="left">Item</th>
                    <th width="5%" nowrap="nowrap" align="center">Quantity</th>
                    <th width="10%" nowrap="nowrap" align="center">Unit</th>
                    <th width="15%" nowrap="nowrap" align="center">Serial No</th>
                    <th width="15%" nowrap="nowrap" align="center">Expiry</th>
                    <th width="8%" nowrap="nowrap" align="center">Approve</th>
                    <th width="8%" nowrap="nowrap" align="center">Cancel</th>
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