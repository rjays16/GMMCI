<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/laboratory/ajax/lab-admin.common.php");
require($root_path.'include/inc_environment_global.php');

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();


/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_lab_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

$title=$LDLab;
$breakfile=$root_path."modules/laboratory/seg-close-window.php".URL_APPEND."&userck=$userck";
#$imgpath=$root_path."pharma/img/";
							
# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Laboratory::Laboratory Reagents Manager (Add/Edit)");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Laboratory::Laboratory Reagents Manager (Add/Edit)");

 # Assign Body Onload javascript code
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');

 $smarty->assign('sOnLoadJs','onLoad=""');	

 if (empty($_GET['id']))	
 	$reagent_code = $_POST['reagent_code'];
 else
	$reagent_code = $_GET['id'];

#echo "reagent_code = ".$_GET['id']." - ".$_POST['reagent_code'];	
 if (isset($_POST['mode'])){
 	if (($srvObj->count==0)&&($reagent_code!='none')){
 		#$reagent_code = str_replace("'","^",$reagent_code);
		$reagent_code = str_replace("'","",$reagent_code);
		#echo "mode = ".$_POST['mode'];
		
		if ($srvObj->saveLabServiceReagents($_POST['reagent_name'], $reagent_code, $_POST['reagent_oname'], $_POST['mode'], $_POST['xrcode'])) {
			if ($mode=='save')
				echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Service Reagent ".strtoupper(stripslashes($_POST['reagent_name']))." is successfully created!</div><br />";
			else
				echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Service Reagent ".strtoupper(stripslashes($_POST['reagent_name']))." is successfully updated!</div><br />";	
			
			echo "<script type=\"text/javascript\">window.parent.location.href=window.parent.location.href;</script>";
		}else{
			#echo $srvObj->error;
			echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Service Reagent ".strtoupper(stripslashes($_POST['reagent_name']))." already exists or the code is not accepted!</div><br />";
		}
	 }else{
 		echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Service Reagent ".strtoupper(stripslashes($_POST['reagent_name']))." already exists or the code is not accepted!</div><br />";
	 }	
 }
 
 ob_start();
 
 

?>
<script language="javascript" >
<!--
/*
function insertRow(id, groupName, groupOtherName){
	var list = window.parent.document.getElementById('labgrouplistTable');
	//alert("id, groupName, groupOtherName = "+id+" - "+groupName+" - "+groupOtherName)
	window.parent.addLabGroup(list, id, groupName, groupOtherName);
}
*/

function validateform(){
	if ($('reagent_code').value=='') {
		alert("Enter the reagent code.");
		$('reagent_code').focus();
		return false;
	}
	
	if ($('reagent_name').value=='') {
		alert("Enter the reagent name.");
		$('gname').focus();
		return false;
	}
	
	if ($('reagent_oname').value=='') {
		alert("Enter the reagent other name.");
		$('goname').focus();
		return false;
	}
	
	$('inputgroupform').submit();
	//refreshWindow();
	return true;
}

function clearText(){
	var mode = $('mode').value;
	
	if (mode == 'save'){
		$('reagent_code').value='';
		$('reagent_name').value='';
		$('reagent_oname').value='';
	}else{
		$('reagent_name').value='';
		$('reagent_oname').value='';
	}	
}

function resetForm(){
	$('xrcode').value='';
	$('reagent_code').value='';
	$('reagent_name').value='';
	$('reagent_oname').value='';
	$('mode').value='save';
}


// -->
</script> 
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>

<form ENCTYPE="multipart/form-data" action="<?=$thisfile?>" method="POST" name="inputgroupform" id="inputgroupform">
	<div style="background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden;">
	<?php 
			
			if (empty($_GET['id']))
 				$reagent_code = $_POST['reagent_code'];
			else{
				#$grpcode = $_GET['id'];
				$reagent_code = urlencode($_GET['id']);
				$checkchar = array("%");
				for ($i=0; $i<strlen($checkchar); $i++){
					$pos = strpos($reagent_code, $checkchar[$i], 0);
					if ($pos === false)
						$isfound = 0;
					else{
						$isfound = 1;
						break;	
					}	
				}
				
				if ($isfound)
					$reagent_code = $_GET['id'];
			}	
			
			$grpcode = strtoupper(str_replace("'","",stripslashes($reagent_code)));
			#echo "code = ".$grpcode;	
			$labreagentInfo = $srvObj->getAllLabReagentInfo($reagent_code);	
			#echo "<br>sql = ".$srvObj->sql;
	?>
	<table border="0" width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%; font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden">
		<tbody>
			<tr>
				<td>Code</td>
				<?php 
						$code = str_replace("^","'",stripslashes($labreagentInfo['reagent_code']));
				?>
				<td>
					<!--<input type="text" name="gcode" id="gcode" size="5" value="<?= $code ?>" <?= ($labreagentInfo['reagent_code'])?'readonly':''?>></td>-->
					<input type="hidden" name="xrcode" id="xrcode" size="5" value="<?= $code ?>" >
					<input type="text" name="reagent_code" id="reagent_code" size="5" value="<?= $code ?>" >
				
				</td>
			</tr>
			<tr>
				<td>Name</td>
				<td><input type="text" name="reagent_name" id="reagent_name" size="25" value="<?= stripslashes($labreagentInfo['reagent_name']);?>"></td>
			</tr>
			<tr>
				<td>Other Name</td>
				<td><input type="text" name="reagent_oname" id="reagent_oname" size="25" value="<?= stripslashes($labreagentInfo['other_name']);?>"></td>
			</tr>
			<tr>
				<td colspan="2">
					<?php if (($labreagentInfo['reagent_code'])){?>
						<img id="save" name="save" src="../../gui/img/control/default/en/en_update.gif" border=0 alt="Update" title="Update" style="cursor:pointer" onClick="return validateform();">
					<?php }else{ ?>
						<img id="save" name="save" src="../../gui/img/control/default/en/en_savedisc.gif" border=0 alt="Save" title="Save" style="cursor:pointer" onClick="return validateform();">
					<?php } ?>
					
					&nbsp;
					
					<img id="cancel" name="cancel" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" onclick="javascript:window.parent.cClick();" title="Cancel" style="cursor:pointer">
					&nbsp;&nbsp;
					
				</td>
				
				
			</tr>
		</tbody>
	</table>
	</div>
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" id="mode" value="<?= ($labreagentInfo['reagent_code'])?'update':'save'?>">
</form>
<?php

# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
	/**
 * LOAD Smarty
 * param 2 = FALSE = dont initialize
 * param 3 = FALSE = show no copyright
 * param 4 = FALSE = load no javascript code
 */
	include_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common',FALSE,FALSE,FALSE);
	
	# Set a flag to display this page as standalone
	$bShowThisForm=TRUE;
}

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
