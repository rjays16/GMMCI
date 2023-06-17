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
require_once($root_path.'include/care_api_classes/class_icpm.php');
$icpm_obj=new Icpm;

$phic = $_GET['phic'];
if ($phic)
 	$phic_caption = " For PHIC";

switch($retpath)
{
	case 'list': $breakfile='icpm_list.php'.URL_APPEND.'&phic='.$phic; break;
	case 'search': $breakfile='icpm_search.php'.URL_APPEND.'&phic='.$phic; break;
	default: $breakfile='icpm_manage.php'.URL_APPEND.'&phic='.$phic; 
}


//TODO: check the final format for icp code

if(isset($ref_code)&&$ref_code){
	//$icpmcode1=subStr($ref_code,0,1);
	//$icpmcode2=subStr($ref_code,2,5);
	$icpmcode1 = $ref_code;
}
if(isset($mode)&&$mode=='update'){
	//$code1=$_POST['icpmcode1'];
	//$code2=$_POST['icpmcode1'];
	#Note: change procedure_code -> code : Modify by Mark on Mar 17, 2007  
	//$_POST['code']=$code1."-".$code2;
	
	#edited by VAN 02-25-08
	#$_POST['code']= $ref_code;
	$_POST['code']= $HTTP_POST_VARS['icpmcode1'];
	
	$_POST['rvu'] = $_POST['rvu_new'];
	$_POST['description'] = $_POST['description_new'];
	//echo "<br>HTTP_POST_VARS['description_new'] = ".$HTTP_POST_VARS['description_new'];
	//echo "<br>_POST['description'] = ".$_POST['description'];
	
	#echo "<br>code  = ".$HTTP_POST_VARS['icpmcode1'];
	$_SESSION['code']=$_POST['code'];
	
	if ($_POST['phic']){
		if (empty($_POST['is_active']))
			$_POST['is_active'] = '0';
	}		
}

if(isset($ref_code)&&$ref_code){
	//echo "<br>mode=".$mode;
	#echo "phic = ".$phic;
	if(isset($mode)&&$mode=='update'){
	
		//echo "<br>obj=".$icd_obj->updateIcdInfoFromArray($diagnosis_code,$HTTP_POST_VARS);
		
		if($icpm_obj->updateIcpmInfoFromArray($ref_code,$_POST,$_POST['phic'])){
			#echo $icpm_obj->sql;
			header("location:icpm_info.php?sid=$sid$lang&ref_code=$ref_code&mode=show&save_ok=1&retpath=$retpath&phic=".$_POST['phic']);
			exit;
		}else{
			echo $icpm_obj->getLastQuery();
			$mode='bad_data';
		}	
	}elseif($row=$icpm_obj->getIcpmInfo($ref_code, $phic)){			
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
 $smarty->assign('sToolbarTitle',"$segICPM :: $LDUpdateData $phic_caption");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('icd10_update.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$segICPM :: $LDUpdateData $phic_caption");

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
function chkfld(){
	var c = document.getElementById("icpmcode1");
	var desp = document.getElementById("description");
	//var code = /^[0-9]\-[0-9][0-9][0-9]$/;
	var code;
	var phic = document.getElementById("phic");
	if (phic.value==1)
		code = /^[0-9][0-9][0-9][0-9][0-9]$/;
	else
		code = /^[0-9]\-[0-9][0-9][0-9]$/;	
	
	if(c.value == ""){
		alert("Please enter icpm code");
		c.select();
		return false;
	}
	if(!c.value.match(code)){
		alert("Please enter correct format of icpm");
		c.select();
		return false;
	}
	if(desp.value == "" ){
		alert("Please fill up the description field.");
		desp.select();
		return false;
	}
	if(document.getElementById("multiplier").value=="");
		alert("Please fill the multiplier.");
		document.getElementById("multiplier").focus();
		document.getElementById("multiplier").select();
		return false;
	}
	return true;
}

function chkvalue(){
	var v = document.getElementById("rvu_new");
	var u = /^[0-9]$/;
	var a,i,c,k, IsNumber = true;
	
	c = v.value.toString();
	k = c.length;
	if(v.value !=""){
		i=0; a = ""; 
		while ( i < k){
			if(!c.charAt(i).match(u)){
			 	a += c.charAt(i);
				IsNumber = false;
			}
			i++;
		}
		if (!IsNumber){
			alert( "'"+a+"' Is not a number! \n Please enter a number.");
			v.focus();
			return false;	
		}	
	}
	return true;
}

//for icpm encoding
function autoComplate(){
	var i = document.getElementById("icpmcode1");
	var c;
	if( i.value != ""){
		c = i.value.toString();
		if(c.length == 1){
			i.value +="-";				
		}
	}	
}

// -->
</script>

<form action="<?php echo $thisfile; ?>?phic=<?=$_POST['phic']?>" method="post" name="icpm"  onSubmit="return chkfld();">
<table border=0> 
  <tr>
    <td align=right class="adm_item"><?php echo $segICPMCode ?>: </td>
    <td class="adm_input">
    	<input type="text" id="icpmcode1" name="icpmcode1" size=5 maxlength=5 value="<?php echo $icpmcode1 ?>">
		<!--<input type="text" id="icpmcode2" name="icpmcode2" size=3 maxlength=3 value="<?php //echo $icpmcode2 ?>"><br>-->
		<span style="font-style:normal">RVU:</span>
		<input type="text" id="rvu_new" name="rvu_new" size=4 maxlength="=5" onblur="chkvalue()" value="<?php echo $rvu ?>" />
		<?php 
		if ($phic==0){ ?>
			<span style="font:normal">Multiplier:</span>
			<!--<input type="text" id="multiplier" name="multiplier" size=5 maxlength=6 value="<?php echo $multiplier ?>">-->
			<input type="text" id="multiplier" name="multiplier" size=5 value="<?php echo $multiplier ?>">
		<?php } ?>
	</td>
  </tr> 
  <tr>
    <td align=right class="adm_item"><?php echo $LDdescriptionA ?>: </td>
    <td class="adm_input"><!--<input type="text" name="description_new"  size=50  value="<?php echo $description ?>">-->
	 			<textarea id="description_new" name="description_new" cols="35" rows="4"><?php echo $description ?></textarea>
	 </td>
  </tr>
  <!-- added by VAN 08-27-08-->
  <?php if ($phic){?>
  <tr>
    <td align=right class="adm_item"><font color=#ff0000><b>*</b></font> Is active? : </td>
    <td class="adm_input"><input type="checkbox" id="is_activen" name="is_active" value="1" '<?= ($is_active)?'checked=checked':''?>'><br></td>
  </tr> 
  <?php } ?>
  <!-- -->
  <tr>
    <td><input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>></td>
    <td  align=right><a href="<?php echo $breakfile;?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?>></a></td>
  </tr>
  </table>
  
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="mode" value="update">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="ref_code" value="<?php echo $ref_code ?>">
<input type="hidden" name="description" value="<?php echo $description ?>">
<input type="hidden" name="retpath" value="<?php echo $retpath ?>">
<input type="hidden" name="phic" id="phic" value="<?php echo $_GET['phic']?>">
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