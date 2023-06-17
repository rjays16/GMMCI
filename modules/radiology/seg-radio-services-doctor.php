<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/radiology/ajax/radio-admin.common.php");
require($root_path.'include/inc_environment_global.php');

require_once($root_path.'include/care_api_classes/class_radiology.php');
$srvObj=new SegRadio();

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

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
 $smarty->assign('sToolbarTitle',"Radiology::Radiology Co-reader Physician Manager (Add/Edit)");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Radiology::Radiology Co-reader Physician Manager (Add/Edit)");

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
	switch($mode){
		case 'save':   
		
							$list_doctors = array();
							foreach ($doctor as $i=>$v) {
								if ($v) $list_doctors[] = array($v);
							}
							
							if ($srvObj->saveDoctorPartner($HTTP_POST_VARS)){
								$group_nr = $srvObj->LastInsertPK('group_nr',$db->Insert_ID());
								if ($srvObj->saveDoctorMember($group_nr,$list_doctors))
									echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Co-readers are successfully created!</div><br />";
								else
									echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Co-readers are not successfully created!</div><br />";
							}else{
								echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Co-readers are not successfully created!</div><br />";
							}
							
							#echo "error = ".$db->ErrorMsg();
							#echo "<br>sql  = ".$srvObj->sql;
							break;
		case 'update':
							if ($srvObj->updateDoctorPartner($HTTP_POST_VARS)){
								echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Co-readers are successfully updated!</div><br />";
							}else{
								echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Co-readers are not successfully updated!</div><br />";
							}
							break;
	}	
	
	
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
	if ($('group_name').value=='') {
		alert("Enter the group name of the doctors.");
		$('group_name').focus();
		return false;
	}
	/*
	if ($('desc').value=='') {
		alert("Select at least two members from the doctor's list.");
		$('desc').focus();
		return false;
	}
	*/
	
	$('inputgroupform').submit();
	//refreshWindow();
	return true;
}
/*
function clearText(){
	var mode = $('mode').value;
	
	if (mode == 'save'){
		$('code').value='';
		$('desc').value='';
	}else{
		$('code').value='';
		$('desc').value='';
	}	
}
*/
/*
function jsGetDeptnr(){
	document.getElementById('dept_nr').value = document.getElementById('dept_nr2').value;
}
*/

function pSearchClose() {
	cClick();  //function in 'overlibmws.js'
}

function appendPersonnel(list,pers_type,details) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var src, toolTipText;
			var lastRowNum = null,
					pers_list = document.getElementsByName(pers_type),
					dRows = dBody.getElementsByTagName("tr");
			var alt = (dRows.length%2)+1;
//alert("appendPersonnel :: pers_list.length = '"+pers_list.length+"'");
			if (details) {
				var id = details.id;
				if (pers_list) {
					for (var i=0;i<pers_list.length;i++) {
//alert("appendPersonnel :: pers_list[i].value ='"+pers_list[i].value+"' == details.id ='"+details.id+"'");
						if (pers_list[i].value == details.id) {
//							alert('"'+details.id+'" is already in the list & has been UPDATED!');
							alert('"'+details.name_pers+'" is already in the list!');
							return true;
						}
					}
					if (pers_list.length == 0)
	 					clearOrder(list);
				}
					src = '<tr class="wardlistrow'+alt+'" id="row'+id+'">'+
								'<td class="center"><a href="javascript:removeItem(\''+id+'\',$(\''+list.id+'\'),\''+pers_type+'\')">'+
								'	<img src="../../images/btn_delitem.gif" border="0"/></a>'+
								'</td>'+
								'<td width="1%">&nbsp;</td>'+
								'<td align="left">'+
								'	<span style="font:bold 12px Arial;color:#660000">'+details.name_pers+'</span>'+
								'	<input name="'+pers_type+'" id="rowID'+id+'" type="hidden" value="'+id+'">'+
								'</td>'+									
							'</tr>';
			}
			else {
				src = '<tr><td colspan="3" style="">List is currently empty...</td></tr>';
			}
//alert("appendOrder : src : \n"+src);
			dBody.innerHTML += src;
//			alert("appendOrder : $('row"+details.id+"').title = '"+$('row'+details.id).title+"'");
			return true;
		}
	}
	return false;
}

function clearOrder(list) {	
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			trayops_code = 0;
			dBody.innerHTML = "";
			return true;
		}
	}
	return false;
}

function removeItem(id,list,pers_type) {
	var destTable, destRows;
	var table = list;//$('order-list')
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	}
		//burn added : September 13, 2007
	var ops_code = document.getElementsByName(pers_type);
	if (ops_code.length == 0){
		emptyIntialList(list);
	}
//	refreshTotal();
}

function reclassRows(list,startIndex) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var dRows = dBody.getElementsByTagName("tr");
			if (dRows) {
				for (i=startIndex;i<dRows.length;i++) {
					dRows[i].className = "wardlistrow"+(i%2+1);
				}
			}
		}
	}
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
			
            $group = $srvObj->getGroupName($id);
			#$doctorsInfo = $srvObj->getAllGroupMembers($id);
			#echo $srvObj->sql;
	?>
	<table border="0" width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%; font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden">
		<tbody>
			
			<tr>
				<td width="25%">Group Name</td>
				<td>
					<input type="text" name="group_name" id="group_name" value="<?= $group['group_name'] ?>">
					<input type="hidden" id="id" name="id" value="<?=($id)?$id:$_POST['id']?>">
				</td>
			</tr>
			<tr>
				<td class="segPanel" align="left" valign="top" colspan="2"> 
					<table id="doctor-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
						<?php
							function listPersonnel($position, $pers_info, $position_title){

								if (is_array($pers_info) && !empty($pers_info)){
									$i=1;
									foreach($pers_info as $pers_nr=>$pers_pidName){
										$list.='<input type="hidden" name="'.$position.'[]" id="'.$position.$pers_nr.'" value="'.$pers_nr.'">';
										$list.=	'['.$i.'] '.$pers_pidName['name']."<br>\n";
										$i++;
									}
									$list = '<span style="text-align:justify;color:#000000;">'."\n".$list.'</span>';	
								}else{
									$list = '<span style="text-align:center;color:#FF0000;font-weight:bold;">No '.$position_title.'</span>';
								}
								return $list;
							}

							$segDoctors = listPersonnel('doctor',$operator_info,'Doctor');
						?>
						<thead>
							
							<tr id="doctor-list-header">
								<th width="*" align="center" colspan="3">&nbsp;&nbsp;Name of Co-reader Physician(s)</th>
							</tr>
						</thead>
						<tbody>
							<!-- list of doctors -->
						</tbody>
					</table>
					<?php
							$onClickDoctor='onclick="overlib(
        						OLiframeContent(\'seg-radio-service-select-personnel.php?personnel_type=doctor&dept_nr=158&table_name=doctor-list\', 400, 300, \'fSelBatchNr\', 1, \'auto\'),
								  WIDTH,400, TEXTPADDING,0, BORDER,0, 
								  STICKY, SCROLL, CLOSECLICK, MODAL, 
								  CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
								  CAPTIONPADDING,4, 
								  CAPTION,\'Select Doctor\',
								  MIDX,0, MIDY,0, 
			  					STATUS,\'Select Doctor\'); return false;"
						      onmouseout="nd();"';
					?>
					<div align="center"> <img <?=createLDImgSrc($root_path,'add.gif','0','center')?> alt="Add Doctor" name="doctorButton" id="doctorButton" onsubmit="return false;" style="cursor:pointer" <?=$onClickDoctor?>> </div>
				</td>
			</tr>
			
			<tr>
				<td colspan="2">
					<?php if (($doctorsInfo['id'])){?>
						<img id="save" name="save" src="../../gui/img/control/default/en/en_update.gif" border=0 alt="Update" title="Update" style="cursor:pointer" onClick="return validateform();">
					<?php }else{ ?>
						<img id="save" name="save" src="../../gui/img/control/default/en/en_savedisc.gif" border=0 alt="Save" title="Save" style="cursor:pointer" onClick="return validateform();">
					<?php } ?>
					<!-- <img id="cancel" name="cancel" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" title="Cancel" style="cursor:pointer" onClick="clearText();"> -->
					&nbsp;
					<a onclick="document.inputgroupform.reset(); return false;" href="#">
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
	<input type="hidden" name="mode" id="mode" value="<?= ($doctorsInfo['id'])?'update':'save'?>">
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
