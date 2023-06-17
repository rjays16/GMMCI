<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
 * Segworks Technologies Corporation (c)2007
 * Hospital Information System
 * MLHE
 */
define('LANG_FILE','icd10icpm.php');
$local_user='aufnahme_user';
$thisfile='icd10_new.php';

require_once($root_path.'include/inc_front_chain_lang.php');

# Load the address object
require_once($root_path.'include/care_api_classes/class_icd10.php');

$icd_obj= new Icd;

//$db->debug=1;
switch($retpath)
{
	case 'list': $breakfile='icd10_list.php'.URL_APPEND; break;
	case 'search': $breakfile='icd10_search.php'.URL_APPEND; break;
	default: $breakfile='icd10_manage.php'.URL_APPEND;
}


if(!isset($mode)){
	$mode='';
	$edit=true;
}else{
	switch($mode)
	{
		case 'save':
		{
			#
			# Validate important data
			#
			$refCode1=$HTTP_POST_VARS['icdcode1'];
			//$refCode2=$HTTP_POST_VARS['icdcode11'];  //comment by mark on May 02.2007

			//$refCode3=$_POST['icdcode2'];
			//$refCode4=$_POST['icdcode22'];

			//$code2="";
			//if ($refCode3!="") $code2=",".$refCode3.".".$refCode4;

			//$HTTP_POST_VARS['diagnosis_code'] = $refCode1.".".$refCode2.$code2;

			$HTTP_POST_VARS['diagnosis_code'] = $refCode1;
			$diagnosis_code = $HTTP_POST_VARS['diagnosis_code'];

			//$_SESSION['diagnosis_code']=$refCode1.".".$refCode2.$code2;
			$HTTP_POST_VARS['description'];

			if(!empty($HTTP_POST_VARS['diagnosis_code'])){
				#
				# Check if icd code exists
				#
				if($icd_obj->icdCodeExists($HTTP_POST_VARS['diagnosis_code'])){
					#
					# Do notification
					#
					$mode='icd_exists';
				}else{
					if($icd_obj->saveIcdInfoFromArray($HTTP_POST_VARS)){
						#
						# Get the last insert ID
						#
						$insid=$db->Insert_ID();
						#
						# Resolve the ID to the primary key
						#
						$icd_obj->LastInsertPK('diagnosis_code',$insid);

//    					header("location:icd10_info.php?sid=$sid&lang=$lang&diagnosis_code=".urlencode($diagnosis_code)."&mode=show&save_ok=1&retpath=$retpath");
                        // since header doesn't work, replaced with JS alternative ... by LST ... 03.22.2012
                        echo "<script>location.href='icd10_info.php?sid=$sid&lang=$lang&diagnosis_code=".urlencode($diagnosis_code)."&mode=show&save_ok=1&retpath=$retpath'</script>";
						exit;
					}else{echo "$sql<br>$LDDbNoSave";}
				}
			}else{
					$mode='bad_data';
			}
			break;
		}//case
	} // end of switch($mode)
}//else


# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"$LDicd10 :: $LDNewicd10");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('icd10_new.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDicd10 :: $LDNewicd10");

# Coller Javascript code


ob_start();
?>

<script type="text/javascript">
<!--
var flag=0;

function trim(str) {
    return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
}

function chkFld(){
	var icdcode1 = document.getElementById("icdcode1");
	var chkMorph = document.getElementById("chkMorph");
	var chkDual  = document.getElementById("chkDual");
	var description = document.getElementById("description");
    
    icdcode1.value = trim(icdcode1.value);
    description.value = trim(description.value);    

    // edited by LST ...
	var regularCode = /^[A-Z0-9A-Z0-9][0-9][0-9]($|\.(-|[0-9]+[\*\+†]*)$)/;		       
	var morphologyCode = /^[\m|\M]\d{4}\/\d($|\+[A-Z0-9A-Z0-9]\d{2}($|\.(-|\d+[\*\+†]*)$))|[A-Z0-9A-Z0-9]\d{2}((.)*?|\.(-|\d+[\*\+†]*)(.)*?)([\s|\+][\m|\M]\d{4}\/\d)$/;
	var dualCode = /^[A-Z0-9A-Z0-9][0-9][0-9]((.)*?|\.(-|[0-9]+[\*\+†]*)(.)*?)\+[A-Z0-9A-Z0-9][0-9][0-9]($|\.(-|[0-9]+[\*\+†]*)$)/;

	//single icd10
	if((chkMorph.checked == false) && (chkDual.checked == false)){
		if((icdcode1.value == "") /*|| (!icdcode1.value.match(regularCode))*/){            
			alert("<?php echo 'Regular Code: '.$LDalertNoicd10Code.'\n'.$LDalertNoicd10Info; ?>");
			icdcode1.focus();
			return false;
		}
	}
	//morphology
	if(chkMorph.checked==true){
		if((icdcode1.value == "") /*|| (!icdcode1.value.match(morphologyCode))*/){
			alert("<?php echo 'Morphology Code: '.$LDalertNoicd10Code.'\n'.$LDalertNoicd10Info; ?>");
			icdcode1.focus();
			return false;
		}
	}
	//dual icd10
	if(chkDual.checked == true){
		if((icdcode1.value == "") /*|| (!icdcode1.value.match(dualCode))*/){
			alert("<?php echo 'Dual Code: '.$LDalertNoicd10Code.'\n'.$LDalertNoicd10Info; ?>");
			icdcode1.focus();
			return false;
		}
	}
	if(description.value == ""){
		alert("<?php echo 'Need code description'; ?>");
		description.focus();
		return false;
	}

	return true;
}

//function IsEmpty(){
//	var c1=document.getElementById('icdcode1');
//	var c11=document.getElementById('icdcode11');
//	var d1=document.getElementById('description');
//	var c2=document.getElementById('icdcode2');
//	var c22=document.getElementById('icdcode22');
//	var code2=/[0-9]|\-/;
//	var code1=/^[A-Z0-9A-Z0-9][0-9][0-9]/;
//	var codex="";
//
//	if((c1.value=="")||(!c1.value.match(code1))){
//		alert("<?php echo "1".$LDalertNoicd10Code.'\n'.$LDalertNoicd10Info; ?>");
//		c1.focus();
//		return false;
//	}
//	if( (c11) && ((c11.value=="")||(!c11.value.match(code2)))){
//		alert("<?php echo "2".$LDalertNoicd10Code.'\n'.$LDalertNoicd10Info; ?>");
//		c11.focus();
//		return false;
//	}
//	if(flag && ((c2.value=="")||(!c2.value.match(code1)))){
//		alert("<?php echo "3".$LDalertNoicd10Code.'\n'.$LDalertNoicd10Info; ?>");
//		c2.focus();
//		return false;
//	}
//	if(flag &&((c22.value=="")||(!c22.value.match(code2)))){
//		alert("<?php echo "4".$LDalertNoicd10Code.'\n'.$LDalertNoicd10Info; ?>");
//		c22.focus();
//		return false;
//	}
//	if(d1.value==""){
//		alert("<?php echo "Need code description";?>");
//		d1.focus();
//		return false;
//	}
//
//	return true;
//}

//function showEntry(){
//	var btn=document.getElementById("btnAdd");
//	var d1=document.getElementById("icdcode2");
//	var d2=document.getElementById("icdcode22");
//
//	if(d1.style.display=='none' || d2.style.display=='none'){
//		flag=1;
//		d1.style.display='';
//		d2.style.display='';
//		document.getElementById("dot").style.display='';
//		btn.value="<<";
//	}else{
//		flag=0;
//		d1.value="";
//		d2.value="";
//		d1.style.display='none';
//		d2.style.display='none';
//		document.getElementById("dot").style.display='none';
//		btn.value=">>";
//	}
//}

function clrInput(){
	var d = document.getElementById("icdcode1");
	d.value = "";
}

function isCheckbox(){
	var chkMorph = document.getElementById("chkMorph");
	var chkDual = document.getElementById("chkDual");
	if(chkDual.checked == true && chkMorph.checked == true){
		alert("Please select only one checkbox.");
		chkDual.checked = false;
		chkMorph.checked = false;
		return false;
	}
}

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

//-->
</script>


<?php

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

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
		case 'icd_exists':
		{
			echo $LDicdCodeExists;
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
&nbsp;<br>

<form action="<?php echo $thisfile; ?>" method="post" name="icd10" onSubmit="return chkFld(this);">
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?></font>
<table border=0>
  <tr>
    <td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $LDicd10Code ?>: </td>
    <td class="adm_input">
		<input type="text" id="icdcode1" name="icdcode1" align="right" size=11 onkeypress="autoComplete()" value="<?php echo $icdcode1 ?>"><!--<b>.</b>-->
		<!--<input type="text" id="icdcode11" name="icdcode11" size=1 maxlength=1 value="<?php //echo $icdcode11 ?>">-->
		&nbsp;
		<!--<input type="text" id="icdcode2" style="display:none" name="icdcode2" size=2 maxlength=3 value="<?php //echo $icdcode2 ?>"><b id="dot" style="display:none">.</b>
		<input type="text" id="icdcode22" style="display:none" name="icdcode22" size=1 maxlength=1 value="<?php //echo $icdcode22 ?>">-->
		<!--<input type="button" id="btnAdd" onclick="showEntry();" value=">>"/>			 -->
		<input type="button" id="btnAdd" onclick="clrInput()" value="clear" />
		<!--edited by VAN 02-25-08 -->

		<input type="checkbox" id="chkMorph" name="chkMorph" onclick="isCheckbox();autoComplete();" value="" />Morphology
		<input type="checkbox" id="chkDual" name="chkDual" onclick="isCheckbox();autoComplete();" value="" />Dual code
		<!--
		<input type="radio" id="chkMorphDual" name="chkMorphDual" value=0 checked onClick="autoComplete()" />Morphology
		<input type="radio" id="chkMorphDual" name="chkMorphDual" value=1 onClick="autoComplete()" />Dual code
		-->
		<br>
	</td>
  </tr>
  <tr>
    <td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $LDdescriptionA ?> : </td>
    <td class="adm_input"><input type="text" id="description" name="description" size=50 value="<?php echo $description ?>"><br></td>
  </tr>
  <tr>
    <td class=pblock><input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>></td>
    <td  align=right><a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a></td>
  </tr>
</table>
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="mode" value="save">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="retpath" value="<?php echo $retpath ?>">



</form>

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
