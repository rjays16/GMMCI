<!-- RETAIL DETAILS BLOCK -->
<center>
<br/>
	<h3 style="margin:4px">Select Laboratory Services</h3>
	<div id="listContainer" style="width:100%">
	<?php 
		$grp = $srvObj->getAllLabGroupInfo($parameterselect);
		#echo "name = ".$grp['name'];
		
		#echo "refno = ".$parameterselect;
		
		$sql = "SELECT sd.* 
				   FROM seg_lab_servdetails AS sd,
					     seg_lab_services AS ss,
     					  seg_lab_serv AS sl
					WHERE sd.service_code = ss.service_code
					AND sl.refno = sd.refno
					AND ss.group_code = '$parameterselect'
					AND sl.refno='$refno'";
	   
		$res=$db->Execute($sql); 
		$row=$res->RecordCount();
		#echo "count_selected = ".$row;
		
		
	?>	
	
	<span>
			Filter: <input type="text" id="searchserv" name="searchserv" style="width:120px" value="<?= $searchserv?>" onKeyUp="fetchServList(300)" onBlur="clearText();">&nbsp;<img src="../../gui/img/common/default/redpfeil_l.gif"><font size=1>&nbsp;Type here the Service Code to Filter</font>
			<br>
			Selected: <span id="selectedcount"><?= $row; ?></span>
	</span>	
	<table id="grp<?= $parameterselect; ?>" style="margin-bottom:5px" width="70%" border="0" cellpadding="0" cellspacing="1">
		<thead class="">
			<td colspan="4">
				<table width="100%" cellpadding="2" cellspacing="2" border="0"><tr>
					<td width="*" class="reg_header"><?= $grp['name'];?></td>
					<td width="1%" align="right" style="padding:2px;font-weight:normal" class="reg_header"><span class="reglink" onclick="toggleDisplay('grpBody<?= $parameterselect; ?>');">Show/Hide</span>
				</tr></table>
			</td>
		</thead>
		
		<thead id="grphead<?= $parameterselect; ?>" class="reg_list_titlebar" style="height:0;overflow:visible;font-weight:bold;padding:4px;">
		
			<td width="1"><input id="chk_all_<?= $parameterselect; ?>" name="chk_all" type="checkbox" onClick="checkAll(grp<?= $parameterselect; ?>,this.checked);countItem('<?= $parameterselect; ?>',1);"></td>
			<td width="20%" nowrap>Code</td>																		
			<td width="65%" nowrap>Description</td>
			<td width="15%">Price</td>
		</thead>
		<tbody id="grpBody<?= $parameterselect; ?>" style="height:0;overflow:visible"></tbody>
		</table>

	</div>
<br/></center>
<!-- END: RETAIL DETAILS BLOCK -->