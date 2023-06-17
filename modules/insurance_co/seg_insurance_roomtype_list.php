<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/insurance_co/ajax/hcplan-admin.common.php');
$xajax->printJavascript($root_path.'classes/xajax');

define('LANG_FILE','edp.php');
$local_user='ck_edv_user';
require_once($root_path.'include/inc_front_chain_lang.php');
/* Load the insurance object */
require_once($root_path.'include/care_api_classes/class_insurance.php');
$ins_obj=new Insurance;

require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj=new Ward;

$breakfile='seg_insurance_roomtype_list.php'.URL_APPEND;

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in toolbar
 $smarty->assign('sToolbarTitle',''.$LDRoomType .':: '.$LDList.'');

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_list.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',''.$LDRoomType .':: '.$LDList.'');

 # Buffer page output
 ob_start();
?>

<style type="text/css" name="formstyle">
td.pblock{ font-family: verdana,arial; font-size: 12}

div.box { border: solid; border-width: thin; width: 100% }

div.pcont{ margin-left: 3; }

</style>

<script type="text/javascript">
	function deleteBenefit(roomtype_nr, roomtype_name){
		var answer = confirm("Are you sure you want to delete the room type item "+(roomtype_name.toUpperCase())+"?");
		if (answer){
			xajax_deleteRoomTypeItem(roomtype_nr, roomtype_name);
		}
	}
	
	function removeRoomType(id) {
	   var table = document.getElementById("roomtype_list");
		var rowno;
		var rmvRow=document.getElementById("row"+id);
		if (table && rmvRow) {
			rowno = 'row'+id;
			var rndx = rmvRow.rowIndex;
			table.deleteRow(rmvRow.rowIndex);
			//window.location.reload(); 
		}
	}
</script>

<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

$roomtype_obj = $ward_obj->getAllRoomType();

# Buffer page output
ob_start();

?>

<table border=0 cellpadding=3 id="roomtype_list">
  <tr class="wardlisttitlerow">
	 <td class=pblock align=center width="5%"><?php echo $LDDelete ?></td>
     <td class=pblock align=center width="30%">Room Type</td>
	 <!--<td class=pblock align=center width="30%">Room Type Name</td>-->
	 <td class=pblock align=center width="*">Description</td>
	 <td class=pblock align=center width="5%">Room Rate</td>
 </tr> 
  
<?php
#while(list($x,$dept)=each($deptarray)){
if(is_object($roomtype_obj)){
	while($result=$roomtype_obj->FetchRow()){
?>
  <tr id="row<?=$result['nr'];?>">
	   <td class=pblock  bgColor="#eeeeee" align="center" valign="middle" width="5%">
 			<img name="delete<?=$result['nr'];?>" id="delete<?=$result['nr'];?>" src="../../images/btn_delitem.gif" style="cursor:pointer" border="0" onClick="deleteBenefit('<?=$result['nr'];?>','<?=$result['name'];?>');"/>
		 </td>
		 <!--
		<td class=pblock  bgColor="#eeeeee" width="15%">
 			<?php echo $result['type']; ?>
		</td>
		-->
		<td class=pblock  bgColor="#eeeeee" width="30%">
 			<a href="seg_insurance_roomtype_new.php<?php echo URL_APPEND."&roomtype_nr=". $result['nr']; ?>">
 				<?php echo $result['name']; ?>
			</a> 
		 </td>
		 <td class=pblock  bgColor="#eeeeee" width="*">
 			<?php echo $result['description']; ?>
		 </td>
		 <td class=pblock  bgColor="#eeeeee" width="5%">
 			<?php echo number_format($result['room_rate'],2,".",","); ?>
		 </td>
  </tr> 
<?php
	}
}
 ?>
 
</table>

<p>

<a href="javascript:history.back()"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a>

<?php

$sTemp = ob_get_contents();
 ob_end_clean();

# Assign the data  to the main frame template

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
