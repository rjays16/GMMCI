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

$service_code = $_GET['service_code'];
#echo "confinetype_id = ".$confinetype_id;
if(!empty($mode)){

	$is_img=false;
	#echo "mode = ".$mode;
	switch($mode)
	{	
		case 'create': 
		{
			$HTTP_POST_VARS['history']='Create: '.date('Y-m-d H:i:s').' '.$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_dt']=date('YmdHis');
			$HTTP_POST_VARS['modify_dt']=date('YmdHis');
			
			$ins_obj->setDataArray($HTTP_POST_VARS);
			if($ins_obj->saveOtherHospServ($HTTP_POST_VARS)){ 
				header("location:seg_other_hospitalserv_list.php".URL_REDIRECT_APPEND."&edit=1&mode=update");
				exit;
			}else{
				echo "<br>$LDDbNoSave";
			}	
			
			break;
		}	
		case 'update':
		{ 
			#$HTTP_POST_VARS['history']=$ins_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." ".$_SESSION['sess_temp_userid']."\n");
			#$HTTP_POST_VARS['history']=$ins_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
			#$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
			#$HTTP_POST_VARS['modify_dt']=date('YmdHis');
			
			$ins_obj->setDataArray($HTTP_POST_VARS);
			if($ins_obj->updateOtherHospServFromInternalArray($HTTP_POST_VARS['service_code'], $HTTP_POST_VARS['name'],$HTTP_POST_VARS['price'])){
				header("location:seg_other_hospitalserv_list.php".URL_REDIRECT_APPEND."&edit=1&mode=update");
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
 $smarty->assign('sToolbarTitle',''.$LDOtherHospServ .':: '.$LDCreate.'');

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_create.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',''.$LDOtherHospServ .':: '.$LDCreate.'');

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
	if(d.name.value==""){
		alert("<?php echo $LDPlsHospServName ?>");
		d.name.focus();
		return false;
	}else if(d.service_code.value==""){
		alert("<?php echo $LDPlsHospServCode ?>");
		d.service_code.focus();
		return false;	
	}else if((d.price.value=="")||(d.price.value==0)){
		alert("<?php echo $LDPlsHospServPrice ?>");
		d.price.focus();
		return false;		
	}
		return true;
	
}

function formatAmount(obj){
	var objname = obj.id;
	var famount ;
	var amount = document.getElementById(objname).value;
	
	pamount = amount.replace(",","");
	if (isNaN(pamount))
		famount="N/A";
	else { 
		famount=pamount-0;
		famount=famount.toFixed(2);
	}

	document.getElementById(objname).value = famount;
}
//---------------------------------
// -->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

$other_hospserv = $ins_obj->getOtherHospServInfo($service_code);
$other_hospserv_count = $ins_obj->count;

# Buffer page output

ob_start();

?>

 <ul>
 <body onLoad="">
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?>
<form action="seg_other_hospitalserv_new.php" method="post" name="other_hospserv" ENCTYPE="multipart/form-data" onSubmit="return chkForm(this)">
<table border=0>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
			<?php echo $LDOtherHospServName ?></font>: 
	 </td>
    <td class=pblock>
	      <input type="text" name="name" id="name" size=40 maxlength=40 value="<?php echo trim($other_hospserv['name']); ?>">
    </td>
  </tr>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
			<?php echo $LDOtherHospServCode ?></font>: 
	 </td>
    <td class=pblock>
	      <?php 
					$readOnly = ($other_hospserv_count) ? 'readonly="readonly"' : "";
			?>
	      <input type="text" name="service_code" id="service_code" size=40 maxlength=40 value="<?php echo trim($other_hospserv['service_code']); ?>" <?=$readOnly;?> >
    </td>
  </tr> 
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
			<?php echo $LDOtherHospServPrice ?></font>: 
	 </td>
    <td class=pblock>
	      <input type="text" name="price" id="price" size=40 maxlength=40 style="text-align:right" onBlur="formatAmount(this);" value="<?php echo number_format(trim($other_hospserv['price']),2); ?>">
    </td>
  </tr> 
 </table>

<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="edit" value="<?php echo $edit ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<!--<input type="hidden" name="service_code" id="service_code" value="<?=$service_code;?>">-->
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
	   if ($service_code){
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