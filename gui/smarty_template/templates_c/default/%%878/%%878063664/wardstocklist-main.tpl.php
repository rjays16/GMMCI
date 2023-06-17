<?php /* Smarty version 2.6.0, created on 2017-12-13 14:46:57
         compiled from pharmacy/wardstocklist-main.tpl */ ?>
<div align="center" style="font:bold 12px Tahoma; color:#990000; "><?php echo $this->_tpl_vars['sWarning']; ?>
</div><br />

<?php echo $this->_tpl_vars['sFormStart']; ?>


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
				CLOSETEXT, '<img src=<?php echo $this->_tpl_vars['sRootPath']; ?>
/images/close_red.gif border=0 >',
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
		<?php echo $this->_tpl_vars['sWardstockList']; ?>

<!--
		<table id="" class="jedList" border="0" cellpadding="0" cellspacing="0" style="width:100%;margin-bottom:10px">
			<thead>
				<?php echo $this->_tpl_vars['sListNav']; ?>

				<tr id="">
					<th align="center" width="1%">Type</th>
					<th align="left" width="5%" nowrap>Item code</th>
					<th align="left" width="*" nowrap>Item name/Generic name</th>
					<th align="center" width="1%"></th>
				</tr>
			</thead>
			<tbody>
<?php echo $this->_tpl_vars['sSearchResults']; ?>

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
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>
 	