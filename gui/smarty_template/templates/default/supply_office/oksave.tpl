<script type="text/javascript" language="javascript">
<!--
    function openWindow(url) {
        window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
    }
-->
</script>

<br/>
<div align="center">
    <table width="60%" border="0" cellpadding="0" cellspacing="0" class="jedDialog">
        <thead>
            <tr>
                <th width="*">{{$sMsgTitle}}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">
                    <div align="left" style="width:95%;padding:0;margin:0">
                        {{$sPrintButton}}
                        {{$sBreakButton}}
                    </div>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <style type="text/css" media="all">
                        .detailstb tr td {
                        }

                        .detailstb tr td span {
                            font:bold 11px Tahoma;
                            color:#00006d;
                        }
                    </style>
                    <table class="detailstb" align="center" width="95%" border="1" cellpadding="2" cellspacing="0" style="border:1px solid #cad3e8;border-collapse:collapse; font:bold 12px Arial">
                        <tr>
                            <td><b>Issue Date</b></td>
                            <td><span>{{$sIssueDate}}</span></td>
                        </tr>
                        <tr>
                            <td width="20%"><b>Reference no.</b></td>
                            <td><span>{{$sRefNo}}</span></td>
                        </tr>
                        <tr>
                            <td><b>Source Area</b></td>
                            <td><span>{{$sSrcArea}}</span></td>
                        </tr>
                        <tr>
                            <td><b>Destination Area</b></td>
                            <td><span>{{$sArea}}</span></td>
                        </tr>
                        <tr>
                            <td><b>Authorized by</b></td>
                            <td><span>{{$sAuthBy}}</span></td>
                        </tr>
                        <tr>
                            <td><b>Issued by</b></td>
                            <td><span>{{$sIssBy}}</span></td>
                        </tr>
                        <tr>
                            <td><b>Items</b></td>
                            <td align="left">
                                <table border="0" width="100%" cellpadding="1" cellspacing="1" style="margin:4px;border:1px solid #006699">
                                    <tbody>
                                        <tr>
                                            <td width="18%" class="jedPanelHeader">Code</td>
                                            <td width="35%" class="jedPanelHeader">Particular</td>
                                            <td width="5%" class="jedPanelHeader" align="center">Quantity</td>
                                            <td width="15%" class="jedPanelHeader" align="center">Unit</td>
                                            <td width="10%" class="jedPanelHeader" align="center">Serial no.</td>
                                            <td width="12%" class="jedPanelHeader" align="center">Expiry</td>
                                        </tr>
                                        {{$sItems}}
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="center"></td>
            </tr>
        </tbody>
    </table>
</div>
<br>
<br>
<br style="list-style:disc">
