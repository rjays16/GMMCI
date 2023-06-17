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
 $smarty->assign('sToolbarTitle',''.$LDBenefit .':: '.$LDList.'');

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_list.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',''.$LDBenefit .':: '.$LDList.'');

 # Buffer page output
 ob_start();
?>

<style type="text/css" name="formstyle">
td.pblock{ font-family: verdana,arial; font-size: 12}

div.box { border: solid; border-width: thin; width: 100% }

div.pcont{ margin-left: 3; }

</style>

<script type="text/javascript">
	function deleteBenefit(benefit_id, benefit_name){
		//alert("deleteBenefit = "+benefit_id);
		var answer = confirm("Are you sure you want to delete the benefit item "+(benefit_name.toUpperCase())+"?");
		if (answer){
			xajax_deleteBenefitItem(benefit_id, benefit_name);
		}
	}
	
	function removeBenefit(id) {
		var table = document.getElementById("benefit_list");
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

$benefit_obj = $ins_obj->getAllBenefits();

# Buffer page output
ob_start();

?>

<table border=0 cellpadding=3 id="benefit_list">
  <tr class="wardlisttitlerow">
	 <td class=pblock align=center><?php echo $LDDelete ?></td>
    <td class=pblock align=center><?php echo $LDBenefit ?></td>
    <td class=pblock align=center><?php echo $LDBenefitArea ?></td>
 </tr> 
  
<?php
#while(list($x,$dept)=each($deptarray)){
if(is_object($benefit_obj)){
	while($result=$benefit_obj->FetchRow()){
?>
  <tr id="row<?=$result['benefit_id'];?>">
	   <td class=pblock  bgColor="#eeeeee" align="center" valign="middle">
 			<img name="delete<?=$result['benefit_id'];?>" id="delete<?=$result['benefit_id'];?>" src="../../images/btn_delitem.gif" style="cursor:pointer" border="0" onClick="deleteBenefit('<?=$result['benefit_id'];?>','<?=$result['benefit_desc'];?>');"/>
		 </td>
		<td class=pblock  bgColor="#eeeeee">
 			<a href="seg_insurance_benefit_new.php<?php echo URL_APPEND."&benefit_id=". $result['benefit_id']; ?>">
 				<?php echo $result['benefit_desc']; ?>
			</a> 
		 </td>
       <td class=pblock  bgColor="#eeeeee">
	 		<?php 
				if ($result['bill_area']=='AC')
					echo "Accomodation";
				elseif ($result['bill_area']=='MS')
					echo "Drugs, Medicines and Supplies";	
				elseif ($result['bill_area']=='HS')
					echo "Hospital Services";	
				elseif ($result['bill_area']=='OR')
					echo "Operating Room or Procedures";	
				elseif ($result['bill_area']=='D1')
					echo "General Practitioner";	
				elseif ($result['bill_area']=='D2')
					echo "Specialist";	
				elseif ($result['bill_area']=='D3')
					echo "Surgeon";	
				elseif ($result['bill_area']=='D4')
					echo "Anesthesiologist";	
	 	   ?> 
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
