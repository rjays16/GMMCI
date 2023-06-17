{{* form.tpl  Form template for orders module (pharmacy & meddepot) 2004-07-04 Elpidio Latorilla *}}
<script type="text/javascript" language="javascript">
<!--
	function openWindow(url) {
		window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}
-->
</script>
{{if $bShowQuickKeys}}
<style type="text/css">
<!--
	table.quickKey td.qkimg{
		font:bold 11px Tahoma;
		vertical-align:middle;
	}
	
	table.quickKey td.qktxt {
		width:70px;
		padding:2px 4px;
		font:bold 11px Tahoma;
		vertical-align:middle;
		color:#007000;
	}
-->
</style>

<div style="width:80%">
	<table border="0" cellspacing="1" cellpadding="2">
		<tr>
			<td class="segPanelHeader">Quick keys</td>
		</tr>
		<tr>
			<td style="background-color:#fffeed; border:1px solid #ebeac4">
				<table class="quickKey" cellpadding="0" cellspacing="1" border="0">
					<tr>

						<td class="qkimg" nowrap="nowrap" ><img src="{{$sRootPath}}images/shortcut-f2.png" /></td>
						<td class="qktxt">Add items</td>
						
						<td	class="quickKey" nowrap="nowrap"><img src="{{$sRootPath}}images/shortcut-f3.png" /></td>
						<td class="qktxt">Clear list</td>
						
						<td	class="quickKey" nowrap="nowrap"><img src="{{$sRootPath}}images/shortcut-f9.png" /></td>
						<td class="qktxt">Person select</td>
						
						<td	class="quickKey" nowrap="nowrap"><img src="{{$sRootPath}}images/shortcut-f12.png" /></td>
						<td class="qktxt">Save/Submit</td>

					</tr>
				</table>	
			</td>
		</tr>
	</table>
</div>
{{/if}}
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sWarning}}</div><br />
<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sPrintOption}}</div><br /> 
{{$sFormStart}}

	<div style="width:740px" align="center">
	<table border="0" cellspacing="0" cellpadding="2" align="center" width="100%" >
			<tr>
				<td align="left"><strong style="white-space:nowrap">Requesting Area:</strong><span id="requesting_area">{{$sSelectArea}}</span></td>
				<td align="right"><strong style="white-space:nowrap">Requested Area:</strong><span id="requested_area">{{$sDestArea}}</span></td>
			</tr>
		</table>
	</div>
	<div style="width:740px" align="center">
		
		<table border="0" cellspacing="1" cellpadding="1" align="center" width="100%">
			<tbody>
				<tr>
					<td class="jedPanelHeader" nowrap >
						Request Details 
					</td>
					<td class="jedPanelHeader" >
						Reference No.
					</td>
					<td class="jedPanelHeader" nowrap >
						Request Date
					</td>
				</tr>
				<tr>
					<!--<td rowspan="3" class="segPanel" align="center" valign="top">
						<table width="95%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Arial" >
							<tr>
								<td align="right" valign="top"><strong>Name</strong></td>
								<td width="1" valign="middle">
									{{$sReqNr}}
									{{$sReqId}}
									{{$sReqName}}
								</td>
							</tr>
														
						</table>
					</td>-->
					<td class="segPanel" nowrap valign="middle" >
						<strong>Name</strong>
						{{$sReqNr}}
						{{$sReqId}}
						{{$sReqName}}
					</td>
					<td class="segPanel" nowrap align="center">
						<input type="text" class="jedInput" id = "refno" name= "refno" size="10" style="font:bold 12px Arial" value ='{{$sRefNo}}' disabled />
					</td>
					<td class="segPanel" align="center"  nowrap valign="middle">
						{{$sReqDate}}{{$sCalendarIcon}}
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<br />

	<div style="width:760px" align="center">
		<table width="98%">
			<tr>
				<td width="50%" align="left">
					{{$sBtnAddItem}}
					{{$sBtnEmptyList}}
					{{$sBtnPDF}}
				</td>
				<td align="right">
					{{$sContinueButton}}
					{{$sBreakButton}}
				</td>
			</tr>
		</table>
		<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="97%">
			<thead>
				<tr id="order-list-header">
					<!-- <th width="4%" nowrap="nowrap">&nbsp;</th> -->
					<th colspan="2" width="10%" nowrap="nowrap" align="center">Item No.</th>
					<th width="*" nowrap="nowrap" align="left">Item Name</th>
                    <th width="5%" nowrap="nowrap" align="left">Unit Price</th>
					<th width="4%" nowrap="nowrap" align="center">Quantity</th>
					<th width="4%" nowrap="nowrap" align="center">Unit</th>
                    <th width="5%" nowrap="nowrap" align="left">Total Price</th>
				</tr>
			</thead>
			<tbody>
{{$sRequestItems}}
			</tbody>
		</table>
	</div>

{{$sHiddenInputs}}
{{$jsCalendarSetup}}
<br/>
<img src="" vspace="2" width="1" height="1"><br/>
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
