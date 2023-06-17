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
	<!--added by bryan on Sept 18,2008-->
	<div align="left" style="width:85%">
		<div class="dashlet">
		<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="*">

					<h1>Your Ward Stocks for this Shift: </h1>
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
<!--
		<table id="" class="jedList" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:10px">
			<thead>
				{{$sListNav}}
				<tr id="">
					<th align="center" width="1%">Type</th>
					<th align="left" width="5%" nowrap>Item code</th>
					<th align="left" width="*" nowrap>Item name/Generic name</th>
					<th align="center" width="1%"></th>
				</tr>
			</thead>
			<tbody>
{{$sSearchResults}}
			</tbody>
		<tfoot>
			<tr>
				<th colspan="3" align="left"><span class="segLink" style="font-size:10px" onclick="toggleTBody('list_hs0000000000')">Hide/Show details</span></th>
				<th align="left">&nbsp;</th>
				<th align="right">SUBTOTAL</th>
				<th id="subtotal_hs0000000000" colspan="2" align="right">0.00</th>
			</tr>
		</tfoot>
		</table>
-->
		
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
