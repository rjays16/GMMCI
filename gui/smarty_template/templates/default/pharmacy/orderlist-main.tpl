<style type="text/css">
.tabFrame {
	padding:5px;
	min-height:150px;
}
</style>
<script type="text/javascript">
function editProduct(nr) { 
	return overlib(
			OLiframeContent('seg-pharma-products-edit.php?nr='+nr, 670, 420, 'fProduct', 0, 'no'),
			WIDTH,670, TEXTPADDING,0, BORDER,0, 
			STICKY, SCROLL, CLOSECLICK, MODAL,
			MODALSCROLL,
			CLOSETEXT, '<img src={{$sRootPath}}/images/close_red.gif border=0 >',
			CAPTIONPADDING,2, 
			CAPTION,'Product Editor',
			MIDX,0, MIDY,0, 
			STATUS,'Product editor');
}

function editOrder(ref) { 
	return overlib(
			OLiframeContent('seg-pharma-order-edit.php?ref='+ref, 670, 420, 'fOrder', 0, 'no'),
			WIDTH,670, TEXTPADDING,0, BORDER,0, 
			STICKY, SCROLL, CLOSECLICK, MODAL,
			MODALSCROLL,
			CLOSETEXT, '<img src={{$sRootPath}}/images/close_red.gif border=0 >',
			CAPTIONPADDING,2, 
			CAPTION,'Product Editor',
			MIDX,0, MIDY,0, 
			STATUS,'Product editor');
}

function search() {
	var o = new Object();
	if ($('chkpayor').checked) 
	{
		o['selpayor'] = $('selpayor').value;
		o['name'] = $('name').value; 
		o['pid'] = $('pid').value; o['patientname'] = $('patientname').value;
		o['inpatientname'] = $('inpatientname').value;
	}
	if ($('chkdate').checked) 
	{
		o['seldate'] = $('seldate').value; 
		o['specificdate'] = $('specificdate').value;
		o['between1'] = $('between1').value;
		o['between2'] =$('between2').value;
	}
	if ($('chkarea').checked) 
	{
		o['selarea'] = $('selarea').value;
	}
	olst.fetcherParams = o;
	olst.reload();
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
							{{$sPayorCheckbox}}
							</td>
							<td width="5%" align="right" nowrap="nowrap"><label for="chkpayor" class="segInput">Select payor</label></td>
							<td>
							{{$sPayor}}
							</td>
						</tr>
						<tr>
							<td width="50" align="right">
							{{$sDateCheckbox}}
							</td>
							<td width="5%" nowrap="nowrap" align="right"><label for="chkdate" class="segInput">Select date</label></td>
							<td>
							{{$sDate}}
							</td>
						</tr>
						<tr>
							<td width="50" align="right">
							{{$sAreaCheckbox}}
							</td>
							<td width="5%" nowrap="nowrap" align="right"><label for="chkarea" class="segInput">Select area</label></td>
							<td>
							{{$sArea}}
							</td>							
						</tr>
						<tr>
							<td></td>
							<td colspan="2">
							<button class="segButton" onclick="search(); return false"><img src="../../gui/img/common/default/magnifier.png"/>Search</button>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<br />
	<div align="left" style="width:90%">
		<div class="dashlet">
		<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="*">
					<h1>Order Search Result: </h1>
				</td>
			</tr>
		</table>
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
{{$sTailScripts}} 	