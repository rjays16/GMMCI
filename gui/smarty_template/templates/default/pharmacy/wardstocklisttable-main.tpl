{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}

<style type="text/css">

    .tabFrame {
        padding:5px;
        min-height:150px;
    }


</style>
<script language="javascript" type="text/javascript">

    function editWardstockRecent(nr,src,area) { 
        return overlib(
        OLiframeContent('seg-pharma-wardstock-edit.php?nr='+nr+'&from='+src+'&area='+area, 670, 420, 'fProduct', 0, 'no'),
        WIDTH,670, TEXTPADDING,0, BORDER,0, 
                STICKY, SCROLL, CLOSECLICK, MODAL,
                MODALSCROLL,
                CLOSETEXT, '<img src={{$sRootPath}}/images/close_red.gif border=0 >',
        CAPTIONPADDING,2, 
                CAPTION,'Wardstock Editor',
        MIDX,0, MIDY,0, 
        STATUS,'Wardstock editor');
    }
     
    
    function search() {
        var w = new Object();
        if ($('chkrefno').checked) 
        {
            w['refno'] = $('refno').value;
        }
      if ($('chkdate').checked) 
        {
            w['seldate'] = $('seldate').value; 
            w['specificdate'] = $('specificdate').value;
            w['between1'] = $('between1').value;
            w['between2'] =$('between2').value;
        }
        if ($('chkward').checked) 
        {
            w['selward'] = $('selward').value;
        }
        wslst.fetcherParams = w;
        wslst.reload();
    }

    function tabClick(listID, index) {
        var dList = $(listID);
        if (dList) {
            var listItems = dList.getElementsByTagName("LI");
            if (listItems[index]) {
                for (var i=0;i<listItems.length;i++) {
                    if (i!=index) {
                        listItems[i].className = "";
                        if ($("tab"+i)) $("tab"+i).style.display = "none";
                    }
                }
                if ($("tab"+index)) $("tab"+index).style.display = "block";
                listItems[index].className = "segActiveTab";
            }
        }
    }
    
    function toggleTBody(list) {
        var dTable = $(list);
        if (dTable) {
            var dBody = dTable.getElementsByTagName("TBODY")[0];
            if (dBody) dBody.style.display = (dBody.style.display=="none") ? "" : "none";
        }
    }    
    
    function enableInputChildren(id, enable) {
        var el=$(id);
        if (el) {
            var children = el.getElementsByTagName("INPUT");
            if (children) {
                for (i=0;i<children.length;i++) {
                    children[i].disabled = !enable;
                }
                return true;
            }
        }
        return false;
    }

</script>

<div style="width:100%">
  <br/>
    <!--added by bryan on Sept 18,2008-->
    <center>
        <table width="70%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="2" cellpadding="2">    
        <tbody>
            <tr>
                <td align="left" class="jedPanelHeader" ><strong>Search options</strong></td>
            </tr>    
            <tr>
                <td nowrap="nowrap" align="left" class="jedPanel">
                    <table width="" border="0" cellpadding="2" cellspacing="0">
                        <tr>
                            <td width="50" align="right">
                            {{$sStocknrCheckbox}}
                            <td nowrap="nowrap" align="left"><label for="chkrefno" class="jedInput">Stock #</label></td>
                            <td id="tdRefNo">
                            {{$sStocknr}}
                            </td>                            
                        </tr>
                        <tr>
                            <td align="right">
                            {{$sStockdateCheckbox}}
                            <td nowrap="nowrap" align="left"><label for="chkdate" class="jedInput">Stock date</label></td>
                            <td id="tdDate">
                            {{$sStockdate}}
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                            {{$sWardCheckbox}}
                            <td nowrap="nowrap" align="left"><label for="chkward" class="jedInput">Select ward</label></td>
                            <td id="tdWard">
                            {{$sWard}}
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
    </center>
    <div align="left" style="width:85%">
        <br/>
        <div class="dashlet">
        <table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td>
                    <h1>Search result: </h1>
                </td>
            </tr>
        </table>
        
    </div>
        <div style="padding:2px 0px">
            <!-- commented out by bryan on Sept 18,2008
            <input class="jedButton" type="button" value="Refresh!" onclick="wslst.reload()" /> 
            -->
        </div>
        {{$sWardstockList}}
        
</div>


{{$sHiddenInputs}}
{{$jsCalendarSetup}}
<img src="" vspace="2" width="1" height="1"><br/>
{{$sDiscountControls}}
<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>

<div style="width:80%">
{{$sUpdateControlsHorizRule}}
{{$sUpdateOrder}}
{{$sCancelUpdate}}
</div>


</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}}     






