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
-->
</script>

<div style="width:650px">
	<table border="0" cellspacing="0" cellpadding="2" width="70%" align="center" style="border-collapse:collapse; border:1px solid #cccccc">
		<tbody>
			<tr>
				<td class="jedPanelHeader" colspan="3">
					Search Services
				</td>
			</tr>
			<tr><td class="jedPanel" colspan="3" style="height:5px;border:0"></td><tr>
			<tr>
				<td class="jedPanel" align="right" valign="middle" width="20%"><strong>Service name</strong></td>
				<td class="jedPanel2" align="left" valign="middle" width="30%" style="">
					<input class="jedInput" type="text" name="name" id="name" size="20"/>
				</td>
				<td class="jedPanel2" align="left" valign="middle" width="*" style="">
					<strong>Search service by name</strong>
				</td>
			</tr>
			<tr>
				<td class="jedPanel" align="right" valign="middle"><strong>Account type</strong></td>
				<td class="jedPanel2" align="left" valign="middle" style="border-right:0">
					{{$sSelectAccountType}}
				</td>
				<td class="jedPanel2" align="left" valign="middle" style="border-left:0">
					<strong>Search for products under a specific category</strong>
				</td>
			</tr>
			<tr>
				<td class="jedPanel" align="right" valign="middle">&nbsp;</td>
				<td class="jedPanel" align="left" valign="middle" style="border-right:0" colspan="2">
					<input class="jedButton" type="submit" value="Search" style="color:#000080" />
				</td>
			</tr>
		</tbody>
	</table>
	<br />

	<div align="left">
		<div style="padding:2px 0px">
			{{$sCreateProduct}}
			{{$sCreateClassification}}
		</div>
		<table id="" class="jedList" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:10px">
			<thead>
				<tr class="nav">
{{$sNavControls}}
				</tr>
				<tr id="">
					<th align="center" width="10%" nowrap>Code</th>
					<th align="center" width="100%" nowrap>Name/description</th>
					<th align="center" width="8%" nowrap="nowrap">Price</th>
					<th align="center" width="12%" nowrap="nowrap">Account</th>
					<th align="center" width="12" nowrap="nowrap">Subtype</th>
					<th align="center" width="12" nowrap="nowrap">Department</th>
					<th align="center" width="5%" nowrap="nowrap">Locked</th>
					<th align="center" width="1%">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
{{$sSearchResults}}
			</tbody>
		</table>
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
