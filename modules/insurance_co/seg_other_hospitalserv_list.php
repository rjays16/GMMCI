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

$breakfile='seg_insurance_benefit_list.php'.URL_APPEND;

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in toolbar
 $smarty->assign('sToolbarTitle',''.$LDOtherHospServ .':: '.$LDList.'');

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_list.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',''.$LDOtherHospServ .':: '.$LDList.'');

 # Buffer page output
 ob_start();
?>

<style type="text/css" name="formstyle">
td.pblock{ font-family: verdana,arial; font-size: 12}

div.box { border: solid; border-width: thin; width: 100% }

div.pcont{ margin-left: 3; }

</style>

<script type="text/javascript">
	function deleteBenefit(service_code, name){
		var answer = confirm("Are you sure you want to delete the confinement type item "+(name.toUpperCase())+"?");
		if (answer){
			xajax_deleteOtherHospServItem(service_code, name);
		}
	}
	
	function removeOtherHospServ(id) {
	   var table = document.getElementById("OtherHospServ_list");
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

$OtherHospServ_obj = $ins_obj->getAllOtherHospServ();

# Buffer page output
ob_start();

?>

<table border=0 cellpadding=3 id="OtherHospServ_list">
  <tr class="wardlisttitlerow">
	 <td class=pblock align=center><?php echo $LDDelete ?></td>
    <td class=pblock align=center><?php echo $LDOtherHospServ ?></td>
	 <td class=pblock align=center><?php echo $LDOtherHospServPrice ?></td>
 </tr> 
  
<?php
#while(list($x,$dept)=each($deptarray)){
if(is_object($OtherHospServ_obj)){
	while($result=$OtherHospServ_obj->FetchRow()){
?>
  <tr id="row<?=$result['service_code'];?>">
	   <td class=pblock  bgColor="#eeeeee" align="center" valign="middle">
 			<img name="delete<?=$result['service_code'];?>" id="delete<?=$result['service_code'];?>" src="../../images/btn_delitem.gif" style="cursor:pointer" border="0" onClick="deleteBenefit('<?=$result['service_code'];?>','<?=$result['name'];?>');"/>
		 </td>
		<td class=pblock  bgColor="#eeeeee">
 			<a href="seg_other_hospitalserv_new.php<?php echo URL_APPEND."&service_code=". $result['service_code']; ?>">
 				<?php echo $result['name']; ?>
			</a> 
		 </td>
		 <td class=pblock  bgColor="#eeeeee" align="right">
 			<?php echo number_format($result['price'],2); ?>
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
