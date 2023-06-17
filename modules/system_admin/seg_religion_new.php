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


$breakfile='seg_religion_list.php'.URL_APPEND;

if(!isset($mode)) $mode='';

$nr = $_GET['religion_nr'];

if(!empty($mode)){

	$is_img=false;
	#echo "mode = ".$mode;
	switch($mode)
	{	
		case 'create': 
		{	
			$HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_date']=date('YmdHis');
			$HTTP_POST_VARS['modify_date']=date('YmdHis');
			
			$person_obj->setDataArray($HTTP_POST_VARS);
			if($person_obj->saveReligion($HTTP_POST_VARS)){ 
				header("location:seg_religion_list.php".URL_REDIRECT_APPEND."&edit=1&mode=update");
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
			if($person_obj->updateReligion($HTTP_POST_VARS['religion_nr'], $HTTP_POST_VARS)){
				header("location:seg_religion_list.php".URL_REDIRECT_APPEND."&edit=1&mode=update");
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
 $smarty->assign('sToolbarTitle','Religion :: '.$LDCreate.'');

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_create.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle','Religion :: '.$LDCreate.'');

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
	if(d.religion_name.value==""){
		alert("Please type the religion name");
		d.religion_name.focus();
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

$religion = $person_obj->getReligionInfo($nr);
#echo "sql = ".$person_obj->sql;
# Buffer page output

ob_start();

?>

 <ul>
 <body onLoad="">
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?>
<form action="seg_religion_new.php" method="post" name="religion" ENCTYPE="multipart/form-data" onSubmit="return chkForm(this)">
<table border=0>
  <tr>
    <td class=pblock align=right bgColor="#eeeeee"><font color=#ff0000><b>*</b>
			Religion</font>: 
	 </td>
    <td class=pblock>
	     <input name="religion_nr" id="religion_nr" type="hidden" value="<?php echo $nr; ?>">
			 <input name="religion_name" id="religion_name" type="text" size=40 value="<?php echo trim($religion['religion_name']); ?>">
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