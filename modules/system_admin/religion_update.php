<?php
/**
 * Segworks Technologies Corporation 2007 
 * GNU General Public License 
 * Copyright (C)2007 
 * MHLE  SELECT * FROM hisdb.care_icd10_en c LIMIT 0,1000
 */

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('../ICPM/roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','icd10icpm.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Load the insurance object
require_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;


switch($retpath)
{
	case 'list': $breakfile='religion_list.php'.URL_APPEND.'&target=religion'; break;
	case 'search': $breakfile='religion_search.php'.URL_APPEND.'&target=religion'; break;
	default: $breakfile='edv-system_manage.php'.URL_APPEND.'&target=religion'; 
}


//TODO: check the final format for icp code
$religion_nr = $_GET['religion_nr'];

if(isset($mode)&&$mode=='update'){
	$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
	$HTTP_POST_VARS['modify_date']=date('YmdHis');
}

if(isset($religion_nr)&&$religion_nr){
	#echo "<br>mode=".$mode;
	#echo "phic = ".$phic;
	if(isset($mode)&&$mode=='update'){
	
		if($person_obj->updateReligion($religion_nr, $HTTP_POST_VARS)){
			header("location:religion_info.php?sid=$sid$lang&religion_nr=$religion_nr&mode=show&save_ok=1&retpath=$retpath");
			exit;
			
		}else{
			echo $person_obj->getLastQuery();
		}	
	}elseif($row=$person_obj->getReligionInfo($religion_nr)){			
		if(is_object($row)){		
			$refcode=$row->FetchRow();
			extract($refcode);
		}
	}
}else{
	//Redirect to search function	
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
 $smarty->assign('sToolbarTitle',"Religion :: $LDUpdateData");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('icd10_update.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Religion :: $LDUpdateData");

# Buffer page output

ob_start();

?>

<ul>
<?php
if(!empty($mode)){ 
?>
<table border=0>
  <tr>
    <td><img <?php echo createMascot($root_path,'mascot1_r.gif','0','bottom') ?>></td>
    <td valign="bottom"><br><font class="warnprompt"><b>
	</b></font><p>
</td>
  </tr>
</table>
<?php 
} 
?>
<script language="javascript">
<!--
function chkfld(d){
	if(d.religion_name.value==""){
		alert("Please type the religion name");
		d.religion_name.focus();
		return false;
	}
	return true;
}

// -->
</script>

<form action="<?php echo $thisfile; ?>" method="post" name="icpm"  onSubmit="return chkfld(this);">
<table border=0> 
  <tr>
    <td align=right class="adm_item"><font color=#ff0000><b>*</b></font> Religion : </td>
    <td class="adm_input">
			<input type="text" id="religion_name" name="religion_name" size="50" value="<?php echo $religion_name ?>" />
	</td>     
  <tr>
    <td><input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>></td>
    <td  align=right><a href="<?php echo $breakfile;?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?>></a></td>
  </tr>
  </table>
  
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="mode" value="update">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="religion_nr" value="<?php echo $religion_nr ?>">
<input type="hidden" name="retpath" value="<?php echo $retpath ?>">
</form>
<p>

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