<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/radiology/ajax/radio-admin.common.php");
require($root_path.'include/inc_environment_global.php');

require_once($root_path.'include/care_api_classes/class_radiology.php');
$srvObj=new SegRadio();

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

include($root_path.'js/fckeditor/fckeditor.php');
$sBasePath = $root_path.'js/fckeditor/';
$oFCKeditor = new FCKeditor('FCKeditor1') ;
$oFCKeditor->BasePath	= $sBasePath ;
$oFCKeditor->Value		= "" ;
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
$local_user='ck_radio_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

$title='Radiology';
$breakfile=$root_path."modules/radiology/seg-close-window.php".URL_APPEND."&userck=$userck";
														 
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
 $smarty->assign('sToolbarTitle',"Radiology::Radiology Finding's Code Manager (Add/Edit)");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Radiology::Radiology Finding's Code Manager (Add/Edit)");

 # Assign Body Onload javascript code
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');

 $smarty->assign('sOnLoadJs','onLoad=""');	             
 global $db;

 if (empty($_GET['id'])){	
	$id = $_POST['id'];
 }else
	$id = $_GET['id'];

#echo "id = ".$id;	
	
 if (isset($_POST['mode'])){
	#if (($srvObj->count==0)&&($grpcode!='none')){
	if ($mode=='save')
		$codeexist = $srvObj->codeExists($_POST['code']);
	else
		$codeexist = 0;    
		
	if (!$codeexist){
		#$grpcode = str_replace("'","",$grpcode);    
		if ($srvObj->saveRadioServiceFindings(addslashes($_POST['desc']), $_POST['code'], $_POST['impression'], $id, $_POST['department_nr'], $_POST['mode'])) {
			if ($mode=='save')
				echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Service Finding's ".stripslashes($_POST['code'])." is successfully created!</div><br />";
			else
				echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Service Finding's ".stripslashes($_POST['code'])." is successfully updated!</div><br />";	
		}else{
			echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Service Finding's ".stripslashes($_POST['code'])." already exists or the code is not accepted!</div><br />";
		}
	 }else{
		echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Service Finding's ".stripslashes($_POST['code'])." already exists or the code is not accepted!</div><br />";
	 }
	 #echo $srvObj->sql;	
 }
 
 ob_start();
 
 

?>

<!--added by VAN 07-07-08 -->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	background-color:#ffffff;
	border:1px outset #3d3d3d;
}
.olcg {
	background-color:#ffffff; 
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffff; 
	text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
	font-family:Arial; font-size:13px; 
	font-weight:bold; 
	color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}
.olfgright {text-align: right;}
.olfgjustify {background-color:#cceecc; text-align: justify;}
.olfgleft {background-color:#cceecc; text-align: left;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
#hiddendiv{display: none;}
-->
</style> 

<script language="javascript" >
<!--
/*
function insertRow(id, groupName, groupOtherName){
	var list = window.parent.document.getElementById('labgrouplistTable');
	//alert("id, groupName, groupOtherName = "+id+" - "+groupName+" - "+groupOtherName)
	window.parent.addLabGroup(list, id, groupName, groupOtherName);
}
*/
/*
function preSet(){
	var dept_nr = document.getElementById('dept_nr').value;
	//alert('dept_nr = '+dept_nr);
	if (dept_nr==0)
		document.getElementById('deptrow').style.display='';     //display
	else
		document.getElementById('deptrow').style.display='none';		//hide
}
*/

function validateform(){
	
	var oEditor = FCKeditorAPI.GetInstance('FCKeditor1') ;
	var str = oEditor.GetHTML() ;  
	//alert(str);
	$('desc').value=str;
	if (str==''){
			alert("Enter the finding's description.");  
			return false;       
	}
	if ($('code').value=='') {
		alert("Enter the finding's code.");
		$('code').focus();
		return false;
	}
	
	/*if ($('desc').value=='') {
		alert("Enter the finding's section.");
		$('desc').focus();
		return false;
	} */
	
	$('inputgroupform').submit();
	//refreshWindow();
	return true;
}
							
function clearText(){
	var mode = $('mode').value;    
	var oEditor = FCKeditorAPI.GetInstance('FCKeditor1') ;  
	
	if (mode == 'save'){
		$('code').value='';
		oEditor.SetHTML('');
		//$('desc').value='';
	}else{
		$('code').value=''; 		
		oEditor.SetHTML('');
		//$('desc').value='';
	}	
}

//added by celsy 08/10/10
function setImpression(deptNR){         									 
	var sel = document.getElementById('impression');                                
	sel.length=0;                                      
	xajax_setImpression(deptNR);              
}
//-----------------------

/*
function jsGetDeptnr(){
	document.getElementById('dept_nr').value = document.getElementById('dept_nr2').value;
}
*/

//added by VAN 07-10-08
function mouseOverImp(tagId, id){
	//alert(objID);
	var elTarget = $(tagId);
	if(elTarget){
		
		idname = "impcode"+id;
		desc = $(idname).value;
			
			
		return overlib( desc, CAPTION,"Impression's Code Description",  
							 TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, 'oltxt', CAPTIONFONTCLASS, 'olcap', 
						WIDTH, 390,FGCLASS,'olfgjustify',FGCOLOR, '#bbddff',FIXX, 5,FIXY, 5);	
			
	}
}

//added by celsy 08/16/10  
function resetDesc(){  
		var oEditor = FCKeditorAPI.GetInstance('FCKeditor1') ;   
		var str=$('desc').value;                               
		oEditor.SetHTML(str);
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
<!--<form ENCTYPE="multipart/form-data" action="<?=$thisfile?>" method="POST" name="inputgroupform" id="inputgroupform" onSubmit="return validateform();">-->
<form ENCTYPE="multipart/form-data" action="<?=$thisfile?>" method="POST" name="inputgroupform" id="inputgroupform">
	<div style="background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden;">
	<?php 
			if (empty($_GET['id'])){
				if ($_POST['id'])
					$id = $_POST['id'];
				else	
					$id = $db->Insert_ID();
			}else	
				$id = $_GET['id'];
			
			if (!$id){
				if ($mode=='update'){
					$row = $srvObj->getAllRadioFindingsInfo($id);
					$findingsInfo = $row->FetchRow();
				}	
			}else
				$findingsInfo = $srvObj->getAllRadioFindingsInfo($id);	
			#echo $srvObj->sql;
			
			if (empty($_GET['id']))
				$dept_nr = $_GET['dept_nr'];
			elseif ($findingsInfo['department_nr'])
				$dept_nr = $findingsInfo['department_nr'];
			else
				$dept_nr = $_POST['department_nr'];
	?>
	<table border="0" width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%; font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden">
		<tbody>
		<!--added by celsy 08/10/10-->
			<tr>
						<td width="28%"><span><strong>Radiology Department</strong></span></td>
						<td>
							<?php 
									$all_meds=&$dept_obj->getAllRadiologyDept();
							?>
							<select name="department_nr" id="department_nr" onchange="setImpression(this.value);">
								<option value="0">-Select a Department-</option>
								<?php 
									if(is_object($all_meds)){
										while ($deptrow=$all_meds->FetchRow()){
											if ($dept_nr==$deptrow['nr'] || $_POST['department_nr']==$deptrow['nr'])         
												echo '<option value="'.$deptrow['nr'].'" selected >'.$deptrow['name_formal'].'</option>';
											else
												echo '<option value="'.$deptrow['nr'].'" >'.$deptrow['name_formal'].'</option>';	
										}
									}

								?>
							</select>
						</td>
					</tr>
			
			<tr>
				<td width="25%">Code</td>
				<td>
					<input type="text" name="code" id="code" value="<?= $findingsInfo['codename'] ?>">
					<input type="hidden" id="id" name="id" value="<?=($id)?$id:$_POST['id']?>">
				</td>
			</tr>
			
			<tr>  
				<td>Description</td>
				<td>   
				<div class="container">
					<?php 
						$oFCKeditor->Value = stripslashes($findingsInfo['description']);
						$oFCKeditor->Create() ; # this will create the FCKEditor 
					?>                                                                                       
				</div>                                                                                           
					<!--<textarea id="desc" name="desc" cols="43" rows="6"><=$findingsInfo['description']?></textarea>-->
				</td>
			</tr>	
			<?php
			//added by CELSY 08/16/10
				echo "<input type='hidden' id='desc' name='desc' value='".stripslashes($findingsInfo['description'])."'>";	
			?>                                                                                              
			
			<tr>
				<td>Impression</td>
				<td>
					<select id="impression" name="impression">
						<option value="0">-Select Impression's Code-</option>
							<?php                                                               
								if ($mode=='update'||$mode=='save'){ 								                             
									$impressionInfo = $srvObj->getAllRadioImpressionInfo(0,$dept_nr);
									if ($impressionInfo){
										while ($row = $impressionInfo->FetchRow()){ 
																 
										if ($findingsInfo['impression']==$row["id"] || $_POST['impression']==$row["id"])
											echo '<option id=" impid'.$row["id"].'" value="'.$row["id"].'" selected onMouseover="mouseOverImp(this,\''.$row["id"].'\');" onMouseout="return nd();">'.$row["codename"].'</option>';
										else
											echo '<option id=" impid'.$row["id"].'" value="'.$row["id"].'" onMouseover="mouseOverImp(this,\''.$row["id"].'\');" onMouseout="return nd();">'.$row["codename"].'</option>';	
										}
									}
								}
							?>
						</select>
						<?php
							$impression2 = $srvObj->getAllRadioImpressionInfo(0);
							if ($impression2){
								while ($row2 = $impression2->FetchRow()){
									//commented by celsy 08/16/10
									//echo '<input type="hidden" id="impcode'.$row2["id"].'" name="impcode'.$row2['id'].'" value="'.$row2["description"].'">';
									echo "<input type='hidden' id='impcode".$row2['id']."' name='impcode".$row2['id']."' value='".$row2['description']."'>";
								}
							}
						?>
				</td>
			</tr>
			
			<tr>
				<td colspan="2">
					<?php if (($findingsInfo['id'])){?>
						<img id="save" name="save" src="../../gui/img/control/default/en/en_update.gif" border=0 alt="Update" title="Update" style="cursor:pointer" onClick="return validateform();">
					<?php }else{ ?>
						<img id="save" name="save" src="../../gui/img/control/default/en/en_savedisc.gif" border=0 alt="Save" title="Save" style="cursor:pointer" onClick="return validateform();">
					<?php } ?>
					<!-- <img id="cancel" name="cancel" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" title="Cancel" style="cursor:pointer" onClick="clearText();"> -->
					&nbsp;
					<a onclick="document.inputgroupform.reset(); resetDesc(); return false;" href="#">
						<img id="cancel" name="cancel" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" title="Cancel" style="cursor:pointer">
					</a>
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
	<input type="hidden" name="mode" id="mode" value="<?= ($findingsInfo['id'])?'update':'save'?>">
	<!--<input type="hidden" name="dept_nr" id="dept_nr" value="<?= ($dept_nr)?$dept_nr:$_POST['dept_nr']?>">-->
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
