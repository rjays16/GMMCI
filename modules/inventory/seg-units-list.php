<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/inventory/ajax/seg-units.common.php');
$xajax->printJavascript($root_path.'classes/xajax');

define('LANG_FILE','edp.php');
$local_user='ck_edv_user';
require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'include/care_api_classes/inventory/class_unit.php');
$unit_obj = new Unit();

$breakfile=$root_path.'/modules/system_admin/edv-system-admi-welcome.php'.URL_APPEND;

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in toolbar
 $smarty->assign('sToolbarTitle','Units :: '.$LDList.'');

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_list.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle','Units :: '.$LDList.'');

 # Buffer page output
 ob_start();
?>

<style type="text/css" name="formstyle">
td.pblock{ font-family: verdana,arial; font-size: 12}

div.box { border: solid; border-width: thin; width: 100% }

div.pcont{ margin-left: 3; }

</style>

<script type="text/javascript">
    function deleteUnit(unit_id, unit_name) {
        var answer = confirm("Are you sure you want to delete the unit "+(unit_name.toUpperCase())+"?");
        if (answer){
            xajax_deleteUnit(unit_id, unit_name);
        }            
    }
    
    function removeUnit(id) {
       var table = document.getElementById("units_list");
//        var rowno;
        var rmvRow=document.getElementById("row"+id);
        if (table && rmvRow) {
//            rowno = 'row'+id;
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

$units = $unit_obj->getAllUnits();
#echo "sql = ".$dept_obj->sql;

# Buffer page output
ob_start();

?>

<table width="90%" border=0 cellpadding=3 id="units_list">
  <tr class="wardlisttitlerow">
     <td class=pblock align=center width="4%"><?php echo $LDDelete ?></td>
     <td class=pblock align=center width="*">Unit Name</td>
     <td class=pblock align=center width="40%">Unit Description</td>
     <td class=pblock align=center width="4%">Smallest Unit?</td>
     <td class=pblock align=center width="4%">Default?</td>
     <td class=pblock align=center width="4%">Inactive?</td>
 </tr> 
  
<?php
#while(list($x,$dept)=each($deptarray)){
if (is_object($units)) {
    while($result = $units->FetchRow()) {
?>
  <tr id="row<?=$result['unit_id'];?>">
       <td class=pblock  bgColor="#eeeeee" align="center" valign="middle">
             <img name="delete<?=$result['unit_id'];?>" id="delete<?=$result['unit_id'];?>" src="../../images/btn_delitem.gif" style="cursor:pointer" border="0" onClick="deleteUnit('<?=$result['unit_id'];?>','<?=$result['unit_name'];?>');"/>
         </td>
        <td class=pblock  bgColor="#eeeeee">
            <a href="seg-unit-new.php<?php echo URL_APPEND."&unitid=". $result['unit_id']; ?>">
                 <?php echo $result['unit_name']; ?>
            </a> 
         </td>
         <td class=pblock  bgColor="#eeeeee">
            <a href="seg-unit-new.php<?php echo URL_APPEND."&unitid=". $result['unit_id']; ?>">
                 <?php echo $result['unit_desc']; ?>
            </a> 
         </td>
         <td class=pblock  bgColor="#eeeeee" align="center">             
             <?php echo ($result["is_unit_per_pc"] != 0) ? '<img name="status'.$result['unit_id'].'" id="status'.$result['unit_id'].'" src="../../images/check2.gif" border="0"/>' : ''; ?>
         </td>
         <td class=pblock  bgColor="#eeeeee" align="center">             
             <?php echo ($result["is_default"] != 0) ? '<img name="status'.$result['unit_id'].'" id="status'.$result['unit_id'].'" src="../../images/check2.gif" border="0"/>' : ''; ?>
         </td>
         <td class=pblock  bgColor="#eeeeee" align="center">             
             <?php echo ($result["is_deleted"] != 0) ? '<img name="status'.$result['unit_id'].'" id="status'.$result['unit_id'].'" src="../../images/check2.gif" border="0"/>' : ''; ?>
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
