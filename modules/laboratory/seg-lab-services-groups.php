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
 $smarty->assign('sToolbarTitle',"Laboratory::Laboratory Group Manager (Add/Edit)");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Laboratory::Laboratory Group Manager (Add/Edit)");

 # Assign Body Onload javascript code
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');

 $smarty->assign('sOnLoadJs','onLoad=""');	

 if (empty($_GET['id']))	
 	$grpcode = $_POST['gcode'];
 else
	$grpcode = $_GET['id'];

#echo "grpcode = ".$_GET['id']." - ".$_POST['gcode'];	
 #$grpcode = str_replace("\\","",$grpcode);
 #echo "grpcode = ".$grpcode." - ".addslashes(urlencode($grpcode));	
 #echo "mode , post = ".$mode." - ".$_POST['mode'];
 if (isset($_POST['mode'])){
 	if (($srvObj->count==0)&&($grpcode!='none')){
 		#$grpcode = str_replace("'","^",$grpcode);
		$grpcode = str_replace("'","",$grpcode);
		
		if ($srvObj->saveLabServiceGroup(strtoupper($_POST['gname']), strtoupper($grpcode), $_POST['goname'], $_POST['mode'], $_POST['xgcode'])) {
			if ($mode=='save')
				echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Service Group ".strtoupper(stripslashes($_POST['gname']))." is successfully created!</div><br />";
			else
				echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Service Group ".strtoupper(stripslashes($_POST['gname']))." is successfully updated!</div><br />";	
			/*echo "<script type=\"text/javascript\">function insertRow(){
																		var list = window.parent.document.getElementById('labgrouplistTable');
																		window.parent.addLabGroup(list, '".strtoupper($grpcode)."', '".strtoupper($_POST['gname'])."', '".strtoupper($_POST['goname'])."');}</script>";
			*/
			/*echo "<script type=\"text/javascript\">insertRow('".strtoupper($grpcode)."', '".strtoupper($_POST['gname'])."', '".strtoupper($_POST['goname'])."');</script>";*/
			echo "<script type=\"text/javascript\">window.parent.location.href=window.parent.location.href;</script>";
		}else{
			#echo $srvObj->error;
			echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Service Group ".strtoupper(stripslashes($_POST['gname']))." already exists or the code is not accepted!</div><br />";
		}
	 }else{
 		echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Service Group ".strtoupper(stripslashes($_POST['gname']))." already exists or the code is not accepted!</div><br />";
	 }	
 }
 
 ob_start();
 
 

?>
<script language="javascript" >
<!--
/*
function saveGroup(mode){
	xajax_saveLabGroup($('gcode').value, $('gname').value, $('goname').value, mode);
}

function closeWindow(){
	window.parent.cClick();
}
*/
/*
function insertRow(){
	var rowlength = document.getElementById('labgrouplistTable').rows.length;
	var x=document.getElementById('labgrouplistTable').insertRow(0);
	var y=x.insertCell(0);
	var z=x.insertCell(1);
	y.innerHTML="NEW CELL1";
	z.innerHTML="NEW CELL2";
}
*/

function insertRow(id, groupName, groupOtherName){
	var list = window.parent.document.getElementById('labgrouplistTable');
	//alert("id, groupName, groupOtherName = "+id+" - "+groupName+" - "+groupOtherName)
	window.parent.addLabGroup(list, id, groupName, groupOtherName);
}

function validateform(){
	if ($('gcode').value=='') {
		alert("Enter the group code.");
		$('gcode').focus();
		return false;
	}
	
	if ($('gname').value=='') {
		alert("Enter the group name.");
		$('gname').focus();
		return false;
	}
	
	if ($('goname').value=='') {
		alert("Enter the group other name.");
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
		$('gcode').value='';
		$('gname').value='';
		$('goname').value='';
	}else{
		$('gname').value='';
		$('goname').value='';
	}	
}

function resetForm(){
	$('xgcode').value='';
	$('gcode').value='';
	$('gname').value='';
	$('goname').value='';
	$('mode').value='save';
}

/*
function refreshWindow(){
	var grpname = $('gname').value;
	var mode = $('mode').value;
	//Service Group ".strtoupper(stripslashes($_POST['gname']))." is successfully updated!
	
	if (mode == 'save')
		alert('Service Group '+grpname+' is successfully created!');
	else
		alert('Service Group '+grpname+' is successfully updated!');	
	
	//alert('insertLabGroup');
	//window.parent.location.href=window.parent.location.href;
	//alert(window.location);
	//window.parent.location.reload();
}
*/
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
<!--<form ENCTYPE="multipart/form-data" action="<?=$thisfile?>" method="POST" name="inputgroupform" id="inputgroupform" onSubmit="return validateform();">-->
<form ENCTYPE="multipart/form-data" action="<?=$thisfile?>" method="POST" name="inputgroupform" id="inputgroupform">
	<div style="background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden;">
	<?php 
			
			/*
			if (($_GET['id'])&&(empty($_POST['id'])))	{
				$char = array("+", "-", "*","/","%");
			   #echo "grpcode 1 = ".$grpcode;
				#if (stristr($grpcode,'+')===FALSE){
				if (stristr($grpcode,$char)===FALSE){
					echo "<br>sulod<br>";
				}else{
					echo "<br>sulod2<br>";
					$grpcode = urlencode($grpcode);
				}
			}		
			*/	
			if (empty($_GET['id']))
 				$grpcode = $_POST['gcode'];
			else{
				#$grpcode = $_GET['id'];
				$grpcode = urlencode($_GET['id']);
				$checkchar = array("%");
				for ($i=0; $i<strlen($checkchar); $i++){
					$pos = strpos($grpcode, $checkchar[$i], 0);
					if ($pos === false)
						$isfound = 0;
					else{
						$isfound = 1;
						break;	
					}	
				}
				
				if ($isfound)
					$grpcode = $_GET['id'];
			}	
			
			$grpcode = strtoupper(str_replace("'","",stripslashes($grpcode)));
			#$grpcode = utf8_decode(strtoupper(str_replace("'","",stripslashes($grpcode))));
			#echo "code = ".$grpcode;	
			$labgroupInfo = $srvObj->getAllLabGroupInfo($grpcode);	
			#echo "<br>sql = ".$srvObj->sql;
	?>
	<table border="0" width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%; font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden">
		<tbody>
			<tr>
				<td>Code</td>
				<?php 
						$code = str_replace("^","'",stripslashes($labgroupInfo['group_code']));
				?>
				<td>
					<!--<input type="text" name="gcode" id="gcode" size="5" value="<?= $code ?>" <?= ($labgroupInfo['group_code'])?'readonly':''?>></td>-->
					<input type="hidden" name="xgcode" id="xgcode" size="5" value="<?= $code ?>" >
					<input type="text" name="gcode" id="gcode" size="5" value="<?= $code ?>" >
				
				</td>
			</tr>
			<tr>
				<td>Name</td>
				<td><input type="text" name="gname" id="gname" size="25" value="<?= stripslashes($labgroupInfo['name']);?>"></td>
			</tr>
			<tr>
				<td>Other Name</td>
				<td><input type="text" name="goname" id="goname" size="25" value="<?= stripslashes($labgroupInfo['other_name']);?>"></td>
			</tr>
			<tr>
				<td colspan="2">
					<?php if (($labgroupInfo['group_code'])){?>
						<img id="save" name="save" src="../../gui/img/control/default/en/en_update.gif" border=0 alt="Update" title="Update" style="cursor:pointer" onClick="return validateform();">
					<?php }else{ ?>
						<img id="save" name="save" src="../../gui/img/control/default/en/en_savedisc.gif" border=0 alt="Save" title="Save" style="cursor:pointer" onClick="return validateform();">
					<?php } ?>
					<!-- <img id="cancel" name="cancel" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" title="Cancel" style="cursor:pointer" onClick="clearText();"> -->
					&nbsp;
					<!--
					<a onclick="document.inputgroupform.reset(); return false;" href="#">
						<img id="cancel" name="cancel" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" title="Cancel" style="cursor:pointer">
					</a>
					-->
					<img id="cancel" name="cancel" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" onclick="javascript:window.parent.cClick();" title="Cancel" style="cursor:pointer">
					&nbsp;&nbsp;
					<!--<img id="cancel" name="cancel" onclick="resetForm();" src="../../gui/img/control/default/en/en_add_new.gif" border=0 alt="Cancel" title="Cancel" style="cursor:pointer">-->
				</td>
				
				<!--
				<td><img id="save" name="save" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" title="Cancel" onClick="javascript:window.parent.cClick();"></td>
				-->
			</tr>
		</tbody>
	</table>
	</div>
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" id="mode" value="<?= ($labgroupInfo['group_code'])?'update':'save'?>">
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
