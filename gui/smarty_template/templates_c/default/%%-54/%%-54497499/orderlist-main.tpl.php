<?php /* Smarty version 2.6.0, created on 2017-11-21 15:08:40
         compiled from pharmacy/orderlist-main.tpl */ ?>
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
			CLOSETEXT, '<img src=<?php echo $this->_tpl_vars['sRootPath']; ?>
/images/close_red.gif border=0 >',
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
			CLOSETEXT, '<img src=<?php echo $this->_tpl_vars['sRootPath']; ?>
/images/close_red.gif border=0 >',
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
		o['inpatient'] = $('inpatient').value; // arco
		o['case_no'] = $('case_no').value; // arco
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
							<?php echo $this->_tpl_vars['sPayorCheckbox']; ?>

							</td>
							<td width="5%" align="right" nowrap="nowrap"><label for="chkpayor" class="segInput">Select payor</label></td>
							<td>
							<?php echo $this->_tpl_vars['sPayor']; ?>

							</td>
						</tr>
						<tr>
							<td width="50" align="right">
							<?php echo $this->_tpl_vars['sDateCheckbox']; ?>

							</td>
							<td width="5%" nowrap="nowrap" align="right"><label for="chkdate" class="segInput">Select date</label></td>
							<td>
							<?php echo $this->_tpl_vars['sDate']; ?>

							</td>
						</tr>
						<tr>
							<td width="50" align="right">
							<?php echo $this->_tpl_vars['sAreaCheckbox']; ?>

							</td>
							<td width="5%" nowrap="nowrap" align="right"><label for="chkarea" class="segInput">Select area</label></td>
							<td>
							<?php echo $this->_tpl_vars['sArea']; ?>

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
<?php echo $this->_tpl_vars['sOrderList']; ?>

</div>


<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<img src="" vspace="2" width="1" height="1"><br/>
<?php echo $this->_tpl_vars['sDiscountControls']; ?>

<span id="tdShowWarnings" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:normal;"></span>

<div style="width:80%">
<?php echo $this->_tpl_vars['sUpdateControlsHorizRule']; ?>

<?php echo $this->_tpl_vars['sUpdateOrder']; ?>

<?php echo $this->_tpl_vars['sCancelUpdate']; ?>

</div>


</div>
<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sTailScripts']; ?>
 	