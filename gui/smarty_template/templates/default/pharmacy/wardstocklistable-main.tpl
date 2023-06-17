{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />

{{$sFormStart}}

<style type="text/css">
<!--
    .tabFrame {
        padding:5px;
        min-height:150px;
    }

-->
</style>
<script language="javascript" type="text/javascript">
<!--  
    
    function search() {
        plst.fetcherParams = [$('codename').value, $('generic').value, $('prodclass').value];
        plst.reload();
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
-->
</script>

<div style="width:100%">
  <br/>
    <!--added by bryan on Oct 09,2008-->
    
    <table width="70%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="2" cellpadding="2">    
        <tbody>
            <tr>
                <td align="left" class="jedPanelHeader" ><strong>Search options</strong></td>
            </tr>
            <tr>
                <td nowrap="nowrap" align="right" class="jedPanel">
                    <table width="100%" border="0" cellpadding="2" cellspacing="0">
                        <tr>
                            <td width="50" align="right">
                            {{$sStocknrCheckbox}}
                            </td>
                            <td width="5%" align="right" nowrap="nowrap">Stock #</td>
                            <td>
                            {{$sStocknr}}
                            </td>
                        </tr>
                        <tr>
                            <td width="50" align="right">
                            {{$sStockdateCheckbox}}
                            </td>
                            <td width="5%" nowrap="nowrap" align="right">Stock date</td>
                            <td>
                            {{$sStockdate}}
                            </td>
                        </tr>
                        <tr>
                            <td width="50" align="right">
                            {{$sWardCheckbox}}
                            </td>
                            <td width="5%" nowrap="nowrap" align="right">Select ward</td>
                            <td>
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
    <br />
    <div align="left" style="width:85%">
        <div class="dashlet">
        <table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="*">

                    <h1>Order Search Result: </h1>
                </td>
            </tr>
        </table>
    </div>
        <div style="padding:2px 0px">
        </div>
        {{$sOrderList}}
        
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
