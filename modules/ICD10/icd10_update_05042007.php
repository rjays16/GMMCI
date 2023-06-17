<?php
/**
 * Segworks Technologies Corporation 2007 
 * GNU General Public License 
 * Copyright (C)2007 
 * MHLE  SELECT * FROM hisdb.care_icd10_en c LIMIT 0,1000
 */

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','icd10icpm.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');	
# Load the insurance object
require_once($root_path.'include/care_api_classes/class_icd10.php');
$icd_obj=new Icd;

switch($retpath)
{
	case 'list': $breakfile='icd10_list.php'.URL_APPEND; break;
	case 'search': $breakfile='icd10_search.php'.URL_APPEND; break;
	default: $breakfile='icd10_manage.php'.URL_APPEND; 
}
/*
if(isset($nr)&&$nr){
	if(isset($mode)&&$mode=='update'){
		if($address_obj->updateCityTownInfoFromArray($nr,$HTTP_POST_VARS)){
    		header("location:citytown_info.php?sid=$sid&lang=$lang&nr=$nr&mode=show&save_ok=1&retpath=$retpath");
			exit;
		}else{
			echo $address_obj->getLastQuery();
			$mode='bad_data';
		}	
	}elseif($row=$address_obj->getCityTownInfo($nr)){
		if(is_object($row)){
			$address=$row->FetchRow();
			# Globalize the array values
			extract($address);
		}
	}
}else{
	// Redirect to search function
}
*/

//echo '$diagnosis_code='.$diagnosis_code.'==>';
//echo "refCode1->".subStr($diagnosis_code,0,3);
//echo "-refCode1->".subStr($diagnosis_code,4);
//echo "<br>_POST = "; print_r($_POST); echo "\n";
//echo "<br>HTTP_POST_VARS = "; print_r($HTTP_POST_VARS); echo "\n";
// Y89.6,H89.1
if(isset($diagnosis_code)&&$diagnosis_code){
	$icdcode1=subStr($diagnosis_code,0,3);
	$icdcode11=subStr($diagnosis_code,4,5);
	
	$icdcode2=subStr($diagnosis_code,6,9);
	$icdcode22=subStr($diagnosis_code,10,11);
	
}
if(isset($mode)&&$mode=='update'){
	$code1=$HTTP_POST_VARS['icdcode1'];
	$code11=$HTTP_POST_VARS['icdcode11'];
	$code2=$HTTP_POST_VARS['icdcode2'];
	$code22=$HTTP_POST_VARS['icdcode22'];
	
	$xcode="";
	if ($code2!="")$xcode=",".$code2.".".$code22;  
	$HTTP_POST_VARS['diagnosis_code']=$code1.".".$code11.$xcode;
	$HTTP_POST_VARS['description'] = $_POST['description_new'];
	//echo "<br>HTTP_POST_VARS['description_new'] = ".$HTTP_POST_VARS['description_new'];
	//echo "<br>_POST['description'] = ".$_POST['description'];
}


if(isset($diagnosis_code)&&$diagnosis_code){
	//echo "<br>mode=".$mode;
	if(isset($mode)&&$mode=='update'){
	
		//echo "<br>obj=".$icd_obj->updateIcdInfoFromArray($diagnosis_code,$HTTP_POST_VARS);
		
		if($icd_obj->updateIcdInfoFromArray($diagnosis_code,$HTTP_POST_VARS)){
			header("location:icd10_info.php?sid=$sid$lang&diagnosis_code=$diagnosis_code&mode=show&save_ok=1&retpath=$retpath");
			exit;
		}else{
			echo $icd_obj->getLastQuery();
			$mode='bad_data';
		}	
	}elseif($row=$icd_obj->getIcd10Info($diagnosis_code)){		
		if(is_object($row)){		
			$refCode=$row->FetchRow();
			extract($refCode);
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
 $smarty->assign('sToolbarTitle',"$LDicd10 :: $LDUpdateData");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('icd10_update.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDicd10 :: $LDUpdateData");

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
<?php 
	switch($mode)
	{
		case 'bad_data':
		{
			echo $LDalerticdChar1;
			break;
		}
		case 'icd10_exists':
		{
			echo "$LDicdCodeExists<br>$LDDataNoSave";
		}
	}
?>
	</b></font><p>
</td>
  </tr>
</table>
<?php 
} 
?>
<script language="javascript">
<!--
var flag=0;

function IsEmpty(){
	var c1=document.getElementById('icdcode1');
	var c11=document.getElementById('icdcode11');
	var d1=document.getElementById('description');
	var c2=document.getElementById('icdcode2');
	var c22=document.getElementById('icdcode22');
	var code2=/[0-9]|\-/;
	var code1=/^[A-Z0-9A-Z0-9][0-9][0-9]/; 
		
	if((c1.value=="")||(!c1.value.match(code1))){
		alert("<?php echo "1".$LDalertNoicd10Code.'\n'.$LDalertNoicd10Info; ?>");
		c1.focus();
		return false;
	}
	if((c11.value=="")||(!c11.value.match(code2))){
		alert("<?php echo "2".$LDalertNoicd10Code.'\n'.$LDalertNoicd10Info; ?>");
		c11.focus();
		return false;
	}
	if(flag && ((c2.value=="")||(!c2.value.match(code1)))){
		alert("<?php echo "3".$LDalertNoicd10Code.'\n'.$LDalertNoicd10Info; ?>");
		c2.focus();
		return false;
	}
	if(flag &&((c22.value=="")||(!c22.value.match(code2)))){
		alert("<?php echo "4".$LDalertNoicd10Code.'\n'.$LDalertNoicd10Info; ?>");
		c22.focus();
		return false;
	}

	if(d1.value==""){
		alert("<?php echo "Need code description";?>");
		d1.focus();
		return false;
	}
	return true;
}

function showEntry(){	
	var btn=document.getElementById("btnAdd");
	var d1=document.getElementById("icdcode2");
	var d2=document.getElementById("icdcode22");
		
	if(d1.style.display=='none' || d2.style.display=='none'){
		flag=1;
		d1.style.display='';
		d2.style.display='';
		document.getElementById("dot").style.display='';	
		btn.value="<<";
	}else{
		flag=0;
		d1.value="";
		d2.value="";
		d1.style.display='none';
		d2.style.display='none';
		document.getElementById("dot").style.display='none';
		btn.value=">>";
	}
}


// -->
</script>

<form action="<?php echo $thisfile; ?>" method="post" name="icd10"  onSubmit="return check(this)">
<table border=0> 
  <tr>
    <td align=right class="adm_item"><?php echo $LDicd10Code ?>: </td>
    <td class="adm_input">
    	<input type="text" name="icdcode1" size=2 maxlength=3 value="<?php echo $icdcode1 ?>"><b>.</b>
    	<input type="text" name="icdcode11" size=1 maxlength=1 value="<?php echo $icdcode11 ?>">
    	&nbsp;
		<input type="text" id="icdcode2" style="<?= ($icdcode2)? "":"display:none"?>" name="icdcode2" size=2 maxlength=3 value="<?php echo $icdcode2 ?>"><b id="dot" style="display:none">.</b>
		<input type="text" id="icdcode22" style="<?= ($icdcode22)? "":"display:none"?>" name="icdcode22" size=1 maxlength=1 value="<?php echo $icdcode22 ?>">
		<input type="button" id="btnAdd" onclick="showEntry();" value=">>"/>			 
		<br>
    </td>
  </tr> 
  <tr>
    <td align=right class="adm_item"><?php echo $LDdescriptionA ?>: </td>
    <td class="adm_input"><input type="text" name="description_new"  size=50 maxlength=60 value="<?php echo $description ?>"></td>
  </tr>
  <tr>
    <td><input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>></td>
    <td  align=right><a href="<?php echo $breakfile;?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?>></a></td>
  </tr>
  </table>
  
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="mode" value="update">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="diagnosis_code" value="<?php echo $diagnosis_code ?>">
<input type="hidden" name="description" value="<?php echo $description ?>">
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