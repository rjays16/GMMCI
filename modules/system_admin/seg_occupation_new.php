<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','edp.php');
$local_user='ck_edv_user';
require_once($root_path.'include/inc_front_chain_lang.php');
/* Load the insurance object */

require_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;


$breakfile='seg_occupation_list.php'.URL_APPEND;

if(!isset($mode)) $mode='';

$nr = $_GET['occupation_nr'];
#echo "roomtype_nr = ".$nr;
if(!empty($mode)){

	$is_img=false;
	#echo "mode = ".$mode;
	switch($mode)
	{	
		case 'create': 
		{	
			#print_r($HTTP_POST_VARS);
			#$HTTP_POST_VARS['history']='Create: '.date('Y-m-d H:i:s').' '.$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_date']=date('YmdHis');
			$HTTP_POST_VARS['modify_date']=date('YmdHis');
			
			$person_obj->setDataArray($HTTP_POST_VARS);
			if($person_obj->saveOccupation($HTTP_POST_VARS)){ 
				header("location:seg_occupation_list.php".URL_REDIRECT_APPEND."&edit=1&mode=update");
				exit;
			}else{
				echo "<br>$LDDbNoSave";
			}	
			
			break;
		}	
		case 'update':
		{ 
			$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['modify_date']=date('YmdHis');
			
			$person_obj->setDataArray($HTTP_POST_VARS);
			if($person_obj->updateOccupation($HTTP_POST_VARS['occupation_nr'], $HTTP_POST_VARS)){
				header("location:seg_occupation_list.php".URL_REDIRECT_APPEND."&edit=1&mode=update");
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
 $smarty->assign('sToolbarTitle','Occupation :: '.$LDCreate.'');

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_create.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle','Occupation :: '.$LDCreate.'');

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
	if(d.occupation_name.value==""){
		alert("Please type the occupation name");
		d.occupation_name.focus();
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

$occupation = $person_obj->getOccupationInfo($nr);
#echo "sql = ".$person_obj->sql;
# Buffer page output

ob_start();

?>

 <ul>
 <body onLoad="">
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?>
<form action="seg_occupation_new.php" method="post" name="occupation" ENCTYPE="multipart/form-data" onSubmit="return chkForm(this)">
<table border=0>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
			Occupation</font>: 
	 </td>
    <td class=pblock>
	     <input name="occupation_nr" id="occupation_nr" type="hidden" value="<?php echo $nr; ?>">
			 <input name="occupation_name" id="occupation_name" type="text" size=40 value="<?php echo trim($occupation['occupation_name']); ?>">
    </td>
  </tr>
 </table>

<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="edit" value="<?php echo $edit ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="nr" id="nr" value="<?=$nr;?>">
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
	   if ($nr){
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