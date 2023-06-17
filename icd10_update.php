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
$icd_obj=new Icd();

switch($retpath)
{
	case 'list': $breakfile='icd10_list.php'.URL_APPEND; break;
	case 'search': $breakfile='icd10_search.php'.URL_APPEND; break;
	default: $breakfile='icd10_manage.php'.URL_APPEND;
}

if(isset($diagnosis_code)&&$diagnosis_code){
//	if(strlen($diagnosis_code) == 11){
//		//dual icd10 code
//		$icdcodeA = substr($diagnosis_code,0,5);
//		$icdcodeB = substr($diagnosis_code,6,10);
//
//		$icdcode1 = $icdcodeA."-".$icdcodeB;
//	} else {
		$icdcode1 = trim($diagnosis_code);
//	}
}
if(isset($mode)&&$mode=='update'){
	//$code1=$HTTP_POST_VARS['icdcode1'];
	//$code2=$HTTP_POST_VARS['icdcode2'];

	$code = trim($HTTP_POST_VARS['icdcode1']);
//	if(strlen($code)==11 ){
//		$icdcodeA = substr($code,0,5);
//		$icdcodeB = substr($code,6,10);
//		$HTTP_POST_VARS['diagnosis_code'] = $icdcodeA."-".$icdcodeB;
//	}else{
		$HTTP_POST_VARS['diagnosis_code'] = $code;
//	}
	$HTTP_POST_VARS['description'] = $_POST['description_new'];
#echo "<br>code = ".$HTTP_POST_VARS['diagnosis_code'];

	#added by VAN 02-25-08
	$_SESSION['diagnosis_code']=$HTTP_POST_VARS['diagnosis_code'];

	#sprint_r($HTTP_POST_VARS);
	//echo "<br>HTTP_POST_VARS['description_new'] = ".$HTTP_POST_VARS['description_new'];
	//echo "<br>_POST['description'] = ".$_POST['description'];
}

if(isset($diagnosis_code)&&$diagnosis_code){
	//echo "<br>mode=".$mode;
//	if(strlen($diagnosis_code) == 11){
//		$icdcodeA = substr($diagnosis_code, 0, 5);
//		$icdcodeB = substr($diagnosis_code, 6, 10);
		#edited by VAN 02-25-08
//		$diagnosis_code = $icdcodeA."-".$icdcodeB;
//	}
    $diagnosis_code = trim($diagnosis_code);
	if(isset($mode)&&$mode=='update') {
		if($icd_obj->updateIcdInfoFromArray($diagnosis_code,$HTTP_POST_VARS)) {                          
//			header("location:icd10_info.php?sid=$sid&lang=$lang&diagnosis_code=".urlencode($diagnosis_code)."&mode=show&save_ok=1&retpath=$retpath");
            // since header doesn't work, replaced with JS alternative ... by LST ... 03.22.2012.
            printf("<script>location.href='icd10_info.php?sid=$sid&lang=$lang&diagnosis_code=".urlencode($diagnosis_code)."&mode=show&save_ok=1&retpath=$retpath'</script>");
			exit;
		} else {
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
         
$onLoadJS="onload=\"shwChkbox()\"";
$smarty->assign('sOnLoadJs',$onLoadJS);         

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

function trim(str) {
    return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
}

function chkFld(){
	var icdcode1 = document.getElementById("icdcode1");
	var chkMorph = document.getElementById("chkMorph");
	var chkDual  = document.getElementById("chkDual");
	var description = document.getElementById("description_new");
    
    icdcode1.value = trim(icdcode1.value);
    description.value = trim(description.value);
            
    // edited by LST ...
	var regularCode = /^[a-zA-z][0-9][0-9]($|\.(-|[0-9]+[\*\+†]*)$)/;		       
	var morphologyCode = /^[\m|\M]\d{4}\/\d($|\+[a-zA-z]\d{2}($|\.(-|\d+[\*\+†]*)$))|[a-zA-z]\d{2}((.)*?|\.(-|\d+[\*\+†]*)(.)*?)([\s|\+][\m|\M]\d{4}\/\d)$/;
	var dualCode = /^[a-zA-z][0-9][0-9]((.)*?|\.(-|[0-9]+[\*\+†]*)(.)*?)\+[a-zA-z][0-9][0-9]($|\.(-|[0-9]+[\*\+†]*)$)/;

	//single icd10
	if((chkMorph.checked == false) && (chkDual.checked == false)) {
		if((icdcode1.value == "") || (!icdcode1.value.match(regularCode))) {
			alert("<?php echo 'Regular Code: '.$LDalertNoicd10Code.'\n'.$LDalertNoicd10Info; ?>");
			icdcode1.focus();
			return false;
		}
	}
	//morphology
	if(chkMorph.checked==true){
		if((icdcode1.value == "") || (!icdcode1.value.match(morphologyCode))){
			alert("<?php echo 'Morphology Code: '.$LDalertNoicd10Code.'\n'.$LDalertNoicd10Info; ?>");
			icdcode1.focus();
			return false;
		}
	}
	//dual icd10
	if(chkDual.checked == true){
		if((icdcode1.value == "") || (!icdcode1.value.match(dualCode))){
			alert("<?php echo 'Dual Code: '.$LDalertNoicd10Code.'\n'.$LDalertNoicd10Info; ?>");
			icdcode1.focus();
			return false;
		}
	}
	if(description.value == ""){
		alert("<?php echo 'Need code description'; ?>");
		description.focus
		return false;
	}

	return true;
}

//added by VAN 02-25-08
// icd
function autoComplete(){
	var c = document.getElementById("icdcode1");
	var m = document.getElementById("chkMorph");
	var d = document.getElementById("chkDual");
	var h;

	if(c.value != ""){
		h = c.value.toString();
		//alert('autoComplete = '+h.length);
		if(m.checked){
			//added by VAN 02-25-08
			if(h.length == 3) c.value +=".";

			if(h.length == 5) c.value +="/";
		}else if(d.checked){
			if(h.length == 3) c.value +=".";
			if(h.length == 5) c.value +="-";
			if(h.length == 9) c.value +=".";
		}else{
			if(h.length == 3) c.value +=".";
		}

	}
}

function shwChkbox(){
	var d = document.getElementById("chkDual");;
	var m = document.getElementById("chkMorph");
	var c = document.getElementById("icdcode1");
	var lm = document.getElementById("lblMorph");
	var ld = document.getElementById("lblDual");
    
	var morphologyCode = /^[\m|\M]\d{4}\/\d($|\+[a-zA-z]\d{2}($|\.(-|\d+[\*\+†]*)$))|[a-zA-z]\d{2}((.)*?|\.(-|\d+[\*\+†]*)(.)*?)([\s|\+][\m|\M]\d{4}\/\d)$/;
	var dualCode = /^[a-zA-z][0-9][0-9]((.)*?|\.(-|[0-9]+[\*\+†]*)(.)*?)\+[a-zA-z][0-9][0-9]($|\.(-|[0-9]+[\*\+†]*)$)/;    
    
	//alert('shwChkbox');
	if (c.value != "") {
        var diagcode = '<?= $icdcode1 ?>';                
        if (diagcode.match(dualCode)) {
			d.checked = true;
			d.style.display='';
			ld.style.display = '';
		}
        else if (diagcode.match(morphologyCode)) {
			m.checked = true;
			m.style.display = '';
			lm.style.display = '';
		}
	}
}

function clrInput(){
	var d = document.getElementById("icdcode1");
	d.value = "";
	d.value.selected();
}

//added by VAN 02-25-08

// -->
</script>
<form action="<?php echo $thisfile; ?>" method="post" name="icd10" onSubmit="return chkFld(this)">
<table border=0>
  <tr>
    <td align=right class="adm_item"><?php echo $LDicd10Code ?>: </td>
    <td class="adm_input">
    	<!--<input type="text" name="icdcode1" id = "icdcode1" onfocus="shwChkbox()" size=11 maxlength=11 value="<?php echo $icdcode1 ?>">-->
		<input type="text" name="icdcode1" id = "icdcode1" size=11 onKeyPress="autoComplete();" value="<?php echo $icdcode1 ?>">
    	<!-- <input type="text" name="icdcode11" size=1 maxlength=1 value="<?php // echo $icdcode11 ?>">  -->
    	&nbsp;
	<!--
		<input type="text" id="icdcode2" style="<?php // ($icdcode2)? "":"display:none" ?>" name="icdcode2" size=2 maxlength=3 value="<?php //echo $icdcode2 ?>"><b id="dot" style="display:none">.</b>
		<input type="text" id="icdcode22" style="<?php // ($icdcode22)? "":"display:none" ?>" name="icdcode22" size=1 maxlength=1 value="<?php //echo $icdcode22 ?>">
		<input type="button" id="btnAdd" onclick="showEntry();" value=">>"/>
		-->
		<input type="button" id="btnAdd" onclick="clrInput()" value="clear" />
		<!--
		<input type="checkbox" id="chkMorph" name="chkMorph" onclick="isCheckbox()" style ="display:none" value="" /><span id ="lblMorph" style ="display:none">Morphology</span>
		<input type="checkbox" id="chkDual" name="chkDual" onclick="isCheckbox()" style ="display:none" value="" /><span id="lblDual" style ="display:none">Dual code</span>
		-->
		<input type="checkbox" id="chkMorph" name="chkMorph" onclick="isCheckbox()" value="" /><span id ="lblMorph" >Morphology</span>
		<input type="checkbox" id="chkDual" name="chkDual" onclick="isCheckbox()" value="" /><span id="lblDual" >Dual code</span>

		<br>
    </td>
  </tr>
  <tr>
    <td align=right class="adm_item"><?php echo $LDdescriptionA ?>: </td>
    <td class="adm_input"><input type="text" name="description_new" id="description_new"  size=50 value="<?php echo $description ?>"></td>
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