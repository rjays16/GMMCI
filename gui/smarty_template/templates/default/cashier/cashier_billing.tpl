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


<div style="width:80%">
	{{include file="cashier/gui_cashier_info.tpl"}}
	<div style="width:100%; text-align:right; margin-top:5px">
		{{$sContinueButton}}
		{{$sBreakButton}}
	</div>
</div><br />
<div style="width:80%">
	{{include file="cashier/gui_totals.tpl"}}
</div><br />

{{$sHiddenInputs}}
{{$jsCalendarSetup}}

<div style="width:80%">
	<table id="" class="jedList" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:10px">
		<thead>
			<tr id="">
				<th align="center" width="*" nowrap="nowrap">Particulars</th>
				<th align="center" width="15%" nowrap="nowrap">Total Charges</th>
				<th align="center" width="15%" nowrap="nowrap">Discount</th>
				<th align="center" width="15%" nowrap="nowrap">Total (Discounted)</th>
				<th align="center" width="15%" nowrap="nowrap">Insurance/PHIC</th>
				<th align="center" width="15%">Excess</th>
			</tr>
		</thead>
		<tbody>
{{$sBillDetails}}
		</tbody>
	</table>

	<table id="" class="jedList" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:10px">
		<tbody>
			<tr>
				<td width="*"><span style="margin:2px;font:bold 12px Tahoma">Previous Payments</span></td>
				<td align="center" width="15%" nowrap="nowrap"></td>
				<td align="center" width="15%" nowrap="nowrap"></td>
				<td align="center" width="15%" nowrap="nowrap"></td>
				<td align="center" width="15%" nowrap="nowrap"></td>
				<td align="right" width="15%" nowrap="nowrap" style="color:#000060;font:bold 14px Arial" >{{$sPrevPayments}}</td>
			</tr>
		</tbody>
	</table>
	
	<table id="" class="jedList" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:10px">
		<tbody>
			<tr class="alt">
				<td width="*"><span style="color:#000060;margin:2px;font:bold 12px Tahoma">TOTAL PAYMENT</span></td>
				<td align="center" width="15%" nowrap="nowrap"></td>
				<td align="center" width="15%" nowrap="nowrap"></td>
				<td align="center" width="15%" nowrap="nowrap"></td>
				<td align="center" width="15%" nowrap="nowrap"></td>
				<td align="right" width="15%" nowrap="nowrap" style="color:#000060;font:bold 14px Arial" >{{$sTotalPayment}}</td>
			</tr>
		</tbody>
	</table>
</div>


</div>
<span style="font:bold 15px Arial">{{$sDebug}}</span>
{{$sFormEnd}}
{{$sTailScripts}} 	
