{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}
    <table border="0" cellspacing="2" cellpadding="2" width="95%" align="center">
        <tbody>
            <tr>
                <td class="segPanelHeader" width="*">
                    Refer/Transfer Details
                </td>
            </tr>
            <tr>
                <td rowspan="3" class="segPanel" align="center" valign="top">
                    <table width="95%" border="0" cellpadding="1" cellspacing="0" style="font-size:11px" >
                        <tr>
                            <td colspan="2"><strong>Transaction type</strong>{{$sRefer}}</td><td colspan="1" align=right><strong>Date: </strong>{{$sDate}}{{$miniCalendar}}</td>
                        </tr>
                        <tr>
                            
                        </tr>
                    </table>
                    <table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
                        <tr>
                            <td valign="top" width="28%"><strong>&nbsp;&nbsp;Referral Number</strong></td>
                            <td>{{$sRefNo}}</td>
                        </tr>
                        <tr>
                            <td valign="top"><strong>&nbsp;&nbsp;Referring Doctor</strong></td>
                            <td>{{$sDoctor}}</td>
                        </tr>
                        <tr>
                            <td valign="top"><strong>&nbsp;&nbsp;Transfer to</strong></td>
                            <td>{{$sDept}}</td>
                        </tr>
                        <tr>
                           <td valign="top"><strong>&nbsp;&nbsp;Diagnosis</strong></td>
                            <td>{{$sDiagnosis}}</td>
                        </tr>
                        <tr>
                            <td valign="top"><strong>&nbsp;&nbsp;Notes</strong></td>
                            <td>{{$sNotes}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

<br>
    <div align="left" style="width:95%">
        <table width="100%">
            <tr>
                <td align="right">
                    {{$sContinueButton}}
                </td>
            </tr>
        </table>
    </div>
    
{{$sHiddenInputs}}
{{$jsCalendarSetup}}
{{$sIntialRequestList}}
<br/>

<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}     
<hr/>