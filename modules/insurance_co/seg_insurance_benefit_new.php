<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','edp.php');
$local_user='ck_edv_user';
require_once($root_path.'include/inc_front_chain_lang.php');
/* Load the insurance object */
require_once($root_path.'include/care_api_classes/class_insurance.php');
$ins_obj=new Insurance;

$breakfile='seg_insurance_benefit_list.php'.URL_APPEND;

if(!isset($mode)) $mode='';

$benefit_id = $_GET['benefit_id'];
if(!empty($mode)){

	$is_img=false;
	#echo "mode = ".$mode;
	switch($mode)
	{	
		case 'create': 
		{
			$ins_obj->setDataArray($HTTP_POST_VARS);
			if($ins_obj->saveBenefit($HTTP_POST_VARS)){ 
				header("location:seg_insurance_benefit_list.php".URL_REDIRECT_APPEND."&edit=1&mode=update");
				exit;
			}else{
				echo "<br>$LDDbNoSave";
			}	
			
			break;
		}	
		case 'update':
		{ 
			$ins_obj->setDataArray($HTTP_POST_VARS);
			if($ins_obj->updateBenefitFromInternalArray($HTTP_POST_VARS['benefit_id'], $HTTP_POST_VARS['benefit_desc'],$HTTP_POST_VARS['bill_area'])){
				header("location:seg_insurance_benefit_list.php".URL_REDIRECT_APPEND."&edit=1&mode=update");
				exit;
			}else{
				 echo "<br>$LDDbNoSave";
			}
			
			break;
		}
			
	}// end of switch
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',''.$LDBenefit .':: '.$LDCreate.'');

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_create.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',''.$LDBenefit .':: '.$LDCreate.'');

# Buffer page output

ob_start();
?>

<style type="text/css" name="formstyle">

td.pblock{ font-family: verdana,arial; font-size: 12}
div.box { border: solid; border-width: thin; width: 100% }
div.pcont{ margin-left: 3; }

</style>

<script language="javascript">
<!-- 

function chkForm(d){
	if(d.benefit_desc.value==""){
		alert("<?php echo $LDPlsBenefit ?>");
		d.benefit_desc.focus();
		return false;
	}else if(d.bill_area.value==""){
		alert("<?php echo $LDPlsBillArea ?>");
		d.bill_area.focus();
		return false;
	}
		return true;
	
}

//---------------------------------
// -->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

$benefit = $ins_obj->getBenefitInfo($benefit_id);

# Buffer page output

ob_start();

?>

 <ul>
 <body onLoad="">
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?>
<form action="seg_insurance_benefit_new.php" method="post" name="benefit_info" ENCTYPE="multipart/form-data" onSubmit="return chkForm(this)">
<table border=0>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
			<?php echo $LDBenefitName ?></font>: 
	 </td>
    <td class=pblock>
	      <input type="text" name="benefit_desc" id="benefit_desc" size=40 maxlength=40 value="<?php echo trim($benefit['benefit_desc']); ?>">
    </td>
  </tr>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b><?php echo $LDBenefitArea ?></font>: </td>
    <td class=pblock>
	 		<select name="bill_area" id="bill_area">
				<option value=0>-- Select an option --</option>
				<?php 
						if ($benefit['bill_area']=="AC")
							echo "<option value='AC' selected>Accomodation</option>";
						else
							echo "<option value='AC'>Accomodation</option>";		 
						
						if ($benefit['bill_area']=="MS")		  
							echo "<option value='MS' selected>Drugs, Medicines and Supplies</option>";
						else	
							echo "<option value='MS'>Drugs, Medicines and Supplies</option>";
						
						if ($benefit['bill_area']=="HS")		
   						echo "<option value='HS' selected>Hospital Services</option>";
						else
							echo "<option value='HS'>Hospital Services</option>";	
						
						if ($benefit['bill_area']=="OR")		
						   echo "<option value='OR' selected>Operating Room or Procedures</option>";
						else
							echo "<option value='OR'>Operating Room or Procedures</option>";	
						
						if ($benefit['bill_area']=="D1")		
						   echo "<option value='D1' selected>General Practitioner</option>";
						else
							echo "<option value='D1'>General Practitioner</option>";	
						
						if ($benefit['bill_area']=="D2")		
						   echo "<option value='D2' selected>Specialist</option>";
						else
							echo "<option value='D2'>Specialist</option>";
						
						if ($benefit['bill_area']=="D3")		
						   echo "<option value='D3' selected>Surgeon</option>";
						else
							echo "<option value='D3'>Surgeon</option>";	
							
						if ($benefit['bill_area']=="D4")		
						   echo "<option value='D4' selected>Anesthesiologist</option>";
						else
							echo "<option value='D4'>Anesthesiologist</option>";			
				?>
			 </select> 
	 </td>
  </tr> 
  
</table>

<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="edit" value="<?php echo $edit ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="benefit_id" id="benefit_id" value="<?=$benefit_id;?>">
<!--
<?php
 if($mode=='select') {
?>
<input type="hidden" name="mode" value="update">

<input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>>
<?php
}
else
{
?>
<input type="hidden" name="mode" value="create">
 
<input type="submit" value="<?php echo $LDCreate ?>">
<?php
}
?>
-->
<?php
	   if ($benefit_id){
?>
			<input type="hidden" name="mode" id="mode" value="update">
<?php }else{ ?>	
			<input type="hidden" name="mode" id="mode" value="create">
<?php } ?>			

<input type="submit" value="<?php echo $LDSave ?>">

</form>
<p>

<a href="javascript:history.back()"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a>

</ul>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign page output to the mainframe template

$smarty->assign('sMainFrameBlockData',$sTemp);
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
</body>