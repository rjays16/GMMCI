<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/supply_office/ajax/seg-item-types.common.php');
$xajax->printJavascript($root_path.'classes/xajax_0.5');

define('LANG_FILE','edp.php');
$local_user='ck_edv_user';
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/care_api_classes/class_pharma_product.php');

$itmobj = new SegPharmaProduct;

$breakfile=$root_path.'/modules/system_admin/edv-system-admi-welcome.php'.URL_APPEND;  

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in toolbar
 $smarty->assign('sToolbarTitle','Item Types :: '.$LDList.'');

 # href for help button
 #$smarty->assign('pbHelp',"javascript:gethelp('dept_list.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle','Item Types :: '.$LDList.'');

 # Buffer page output
 ob_start();
?>

<style type="text/css" name="formstyle">
td.pblock{ font-family: verdana,arial; font-size: 12}

div.box { border: solid; border-width: thin; width: 100% }

div.pcont{ margin-left: 3; }

</style>

<script type="text/javascript">
    function deleteItemType(type_nr, type_name) {
        var answer = confirm("Are you sure you want to delete item type "+(type_name.toUpperCase())+"?");
        if (answer){
            xajax_deleteItemType(type_nr, type_name);
        }        
    }
    
    function removeItemType(id) {
       var table = document.getElementById("type_list");
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

$typobj = $itmobj->getTypes(true);
#echo "sql = ".$dept_obj->sql;

# Buffer page output
ob_start();

?>

<table border=0 cellpadding=3 id="type_list">
  <tr class="wardlisttitlerow">
     <td class=pblock align=center width="5%"><?php echo $LDDelete ?></td>
     <td class=pblock align=center width="25%">Name</td>
     <td class=pblock align=center width="*">Description</td>  
     <td class=pblock align=center width="10%">Class</td>
     <td class=pblock align=center width="10%">Active?</td>     
 </tr> 
  
<?php
if(is_object($typobj)) {
    while($result=$typobj->FetchRow()) {
?>
  <tr id="row<?=$result['nr'];?>">
       <td class=pblock  bgColor="#eeeeee" align="center" valign="middle">
             <img name="delete<?=$result['nr'];?>" id="delete<?=$result['nr'];?>" src="../../images/btn_delitem.gif" style="cursor:pointer" border="0" onClick="deleteItemType('<?=$result['nr'];?>','<?=$result['name'];?>');"/>
         </td>
         <td class=pblock  bgColor="#eeeeee">
            <a href="seg_itemtype_new.php<?php echo URL_APPEND."&nr=". $result['nr']; ?>">
                <?php echo $result['name']; ?>
            </a> 
         </td>
         <td class=pblock  bgColor="#eeeeee">
            <a href="seg_itemtype_new.php<?php echo URL_APPEND."&nr=". $result['nr']; ?>">
                <?php echo $result['description']; ?>
            </a>
         </td>
         <td class=pblock  bgColor="#eeeeee">
             <?php echo $itmobj->getProdClassName($result['prod_class']); ?>
         </td>
         <td class=pblock  bgColor="#eeeeee" align="center">
             <?php echo ($result['is_inactive'] == 0) ? '<img name="status'.$result['nr'].'" id="status'.$result['nr'].'" src="../../images/check2.gif" border="0"/>' : ''; ?>
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
