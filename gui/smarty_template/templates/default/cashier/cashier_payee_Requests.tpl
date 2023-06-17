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
	function flagCheckBoxesByName(name, flag) {
		var items = document.getElementsByName(name);
		for (var i=0; i<items.length; i++)
			if (items[i].type.toLowerCase()=='checkbox') {
				if (!items[i].disabled)	items[i].checked = flag;
			}
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
<div style="width:80%">
	<table border="0" cellpadding="2" cellspacing="2" width="100%">
		<tbody>
			<tr>
				<td class="segPanelHeader" colspan="3">Request Information</td>
			</tr>
			<tr>
				<td class="segPanel" id="hpid" width="1%" nowrap="nowrap"><strong>Health Record Number</strong></td>
				<td class="jedPanel3" id="spid" width="50%">{{$sPID}}</td>
				<td rowspan="8" class="photo_id">{{$img_source}}</td>
			</tr>
			<tr>
				<td class="segPanel" id="hcase_no"><strong>Case Number</strong></td>
				<td class="jedPanel3" id="scase_no">{{$sEncounterNr}}</td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Full Name</strong></td>
				<td class="jedPanel3">{{$sFullname}}</td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Date of Birth</strong></td>
				<td class="jedPanel3">{{$sBdayDate}} &nbsp; {{$sCrossImg}} &nbsp; <font color="black">{{$sDeathDate}}</font></td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Gender</strong></td>
				<td class="jedPanel3">{{$sSexType}}</td>
			</tr>	
			<tr>
				<td class="segPanel"><strong>Patient Type</strong></td>
				<td class="jedPanel3">{{$sPatientType}}</td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Classification</strong></td>
				<td class="jedPanel3">{{$sClassification}}</td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Address</strong></td>
				<td class="jedPanel3">{{$sAddress}}</td>
			</tr>
		</tbody>
	</table>
	
	<br />

	<table class="segRPanel" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>		
			<td class="TopLeft">&nbsp;</td>
			<td class="Top">&nbsp;</td>
			<td class="TopRight">&nbsp;</td>
		</tr>
		<tr>
			<td class="Left">&nbsp;</td>	
			<td class="Contents" align="center" style="padding:2px" valign="top">
				<h2 style="margin:0px;margin-bottom:4px">{{$sFullname}}</h2>
				<table width="100%" cellpadding="2" cellspacing="1" border="0" align="left">
					<tr>
						<td width="30%" valign="top">
							<table width="100%" cellpadding="1" cellspacing="1" border="0"  style="border:1px solid #006699;background-color:white">
								<tr>
									<td width="30%" class="jedPanelHeader"><strong>PID</strong></td>
									<td class="jedPanel3" width="*"><strong style="color:#000066">{{$sPID}}</strong></td>									
								</tr>
								<tr>
									<td class="jedPanelHeader"><strong>Case No.</strong></td>
									<td class="jedPanel3"><strong style="color:#000066">{{$sEncounterNr}}</strong></td>
								</tr>
								<tr>
									<td valign="top" colspan="2" class="jedPanelHeader"><strong>Address</strong></td>
								</tr>
								<tr>
									<td colspan="2" class="jedPanel3">
										<span>
											{{$sAddress}}
										</span>
									</td>
								</tr>
							</table>
						</td>
						<td width="*" valign="top">														
							<div style="width:100%;border:1px solid #006699;padding:1px">
							<table width="100%" cellpadding="1" cellspacing="1" border="0" style="font-size:11px;">
								<thead>
									<tr>
										<th class="jedPanelHeader" width="1%"><input type="checkbox" id="chk-all" onclick="flagCheckBoxesByName('reference[]',this.checked)" checked="checked" style="margin:0"/></th>
										<th class="jedPanelHeader" width="10%" align="center">Date</th>
										<th class="jedPanelHeader" width="10%" align="center">Dept</th>
										<th class="jedPanelHeader" width="15%" align="center">Ref no.</th>
										<th class="jedPanelHeader" width="*" align="left">Items</th>
									</tr>
								</thead>
								<tbody>
{{$sRequestRows}}
								</tbody>
							</table>
							</div>
						</td>
					</tr>
					<tr height="*">
						<td>&nbsp;</td>
						<td style="padding:4px">
							{{$sContinueButton}}
							{{$sBreakButton}}
						</td>
					</tr>
				</table>
			</td>
			<td class="Right">&nbsp;</td>
		</tr>
		<tr>
			<td class="BottomRight">&nbsp;</td>
			<td class="Bottom">&nbsp;</td>
			<td class="BottomLeft">&nbsp;</td>
		</tr>
	</table>
</div>

{{$sHiddenInputs}}
{{$jsCalendarSetup}}
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
