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
	
	function clearEncounter() {
		var iscash = $("iscash1").checked;
		$('ordername').value="";
		$('ordername').readOnly=!iscash;
		$('orderaddress').value="";
		$('orderaddress').readOnly=!iscash;
		$('pid').value="";
		$('encounter_nr').value="";
		$('clear-enc').disabled = true;
		$('clear-enc').disabled = true;
	}

	function refreshDiscount() {
	}	
	
	function pSearchClose() {
		cClick();
	}
-->
</script>

<div align="center" style="margin-bottom:10px; width:80%">
	<table border="0">
		<tr>
			<td width="1"><strong style="white-space:nowrap">Account type</strong></td>
			<td width="5"></td>
			<td width="*">{{$sSelectAccountType}}</td>
		</tr>
	</table>
</div>

<div style="width:80%">
	{{include file="cashier/gui_cashier_info.tpl"}}
	<div style="width:100%; text-align:right; margin-top:5px">
		{{$sContinueButton}}
		{{$sBreakButton}}
	</div>
</div><br />

<div style="width:80%;display:none">
	{{include file="cashier/gui_totals.tpl"}}
</div><br />

{{$sHiddenInputs}}
{{$jsCalendarSetup}}
<br/>
<img src="" vspace="2" width="1" height="1"><br/>
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
