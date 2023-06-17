<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/system_admin/ajax/edv-admin.common.php');
$xajax->printJavascript($root_path.'classes/xajax');

define('LANG_FILE','edp.php');
$local_user='ck_edv_user';
require_once($root_path.'include/inc_front_chain_lang.php');
/* Load the insurance object */
require_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;

$breakfile='seg_religion_list.php'.URL_APPEND;

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in toolbar
 $smarty->assign('sToolbarTitle','Religion :: '.$LDList.'');

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_list.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle','Religion :: '.$LDList.'');

 # Buffer page output
 ob_start();
?>

<style type="text/css" name="formstyle">
td.pblock{ font-family: verdana,arial; font-size: 12}

div.box { border: solid; border-width: thin; width: 100% }

div.pcont{ margin-left: 3; }

</style>

<script type="text/javascript">
	function deleteReligion(religion_nr, religion_name){
		var answer = confirm("Are you sure you want to delete the religion "+(religion_name.toUpperCase())+"?");
		if (answer){
			xajax_deleteReligionItem(religion_nr, religion_name);
		}
	}
	
	function removeReligion(id) {
	   var table = document.getElementById("religion_list");
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

$religion_obj = $person_obj->getReligionInfo();

# Buffer page output
ob_start();

?>

<table border=0 cellpadding=3 id="religion_list" width="50%">
  <tr class="wardlisttitlerow">
	 <td class=pblock align=center width="5%"><?php echo $LDDelete ?></td>
     <td class=pblock align=center width="30%">Religion</td>
 </tr> 
  
<?php
#while(list($x,$dept)=each($deptarray)){
if(is_object($religion_obj)){
	while($result=$religion_obj->FetchRow()){
?>
  <tr id="row<?=$result['religion_nr'];?>">
	   <td class=pblock  bgColor="#eeeeee" align="center" valign="middle" width="5%">
 			<img name="delete<?=$result['religion_nr'];?>" id="delete<?=$result['religion_nr'];?>" src="../../images/btn_delitem.gif" style="cursor:pointer" border="0" onClick="deleteReligion('<?=$result['religion_nr'];?>','<?=$result['religion_name'];?>');"/>
		 </td>
		<td class=pblock  bgColor="#eeeeee" width="30%">
 			<a href="seg_religion_new.php<?php echo URL_APPEND."&religion_nr=". $result['religion_nr']; ?>">
 				<?php echo $result['religion_name']; ?>
			</a> 
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
