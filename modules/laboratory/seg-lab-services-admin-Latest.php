<?php

define('ROW_MAX',15); # define here the maximum number of rows for displaying the parameters

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/laboratory/ajax/lab-admin.common.php");
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables=array('chemlab_groups.php','chemlab_params.php');
define('LANG_FILE','lab.php');
$local_user='ck_lab_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

//$db->debug=true;
# Create lab object
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();
#require_once($root_path.'include/care_api_classes/class_lab.php');
#$srvObj=new SegLab;

#---added by VAS
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

# Check for the department nr., else show department selector
#commented by VAS
/*
if(!isset($dept_nr)||!$dept_nr){
	if($cfg['thispc_dept_nr']){
		$dept_nr=$cfg['thispc_dept_nr'];
	}else{
		#echo "url = ".URL_REDIRECT_APPEND;
		header('Location:seg-lab-select-dept.php'.URL_REDIRECT_APPEND.'&target=labservadmin&retpath='.$retpath);
		#header('Location:labservices_manage.php?sid=df14a43af1e29c802de756aa4a5980b7&lang=en');
		exit;
	}
}
*/
include_once('seg-lab-services-admin.action.php');
require($root_path.'include/inc_labor_param_group.php');

/*						
if(!isset($parameterselect)||$parameterselect=='') $parameterselect='priority';

$parameters=$paralistarray[$parameterselect];					
$paramname=$parametergruppe[$parameterselect];

$pitems=array('msr_unit','median','lo_bound','hi_bound','lo_critical','hi_critical','lo_toxic','hi_toxic');
*/

# Load the date formatter */
include_once($root_path.'include/inc_date_format_functions.php');
    
//echo $lab_obj->getLastQuery();

# Get the test test groups
#$tgroups=&$lab_obj->TestGroups();

# Get the test parameter values
#$tparams=&$lab_obj->TestParams($parameterselect);

$breakfile="labor.php".URL_APPEND;

// echo "from table ".$linecount;
# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in toolbar
 #$smarty->assign('sToolbarTitle',"Laboratory::Services admin::".$dept_name);
 $smarty->assign('sToolbarTitle',"Laboratory::Services admin");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('lab_param_config.php')");

 # hide return  button
 $smarty->assign('pbBack',FALSE);

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 #$smarty->assign('sWindowTitle',"Laboratory::Services admin::".$dept_name);
 $smarty->assign('sWindowTitle',"Laboratory::Services admin");

 # collect extra javascript code
 ob_start();
echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'modules/laboratory/js/lab-admin-gui.js"></script>'."\r\n";
#$xajax->printJavascript($root_path.'classes/xajax');

if ($xajax) {
	$xajax->printJavascript($root_path.'classes/xajax');
}
?>
<script language="javascript" name="j1">
<!--  
//var dept_nr=-1, group_id=-1;
var group_id=-1;

function chkselect(d){
 	if(d.parameterselect.value=="<?php echo $parameterselect ?>"){
		return false;
	}
}

function editService(nr,rowno){
	var grpid = document.forms["paramselect"].parameterselect.value;
	//alert("grpid = "+grpid);
	urlholder="<?php echo $root_path ?>modules/laboratory/seg-lab-services-edit.php?sid=<?php echo "$sid&lang=$lang" ?>&nr="+nr+"&grpid="+grpid+"&row="+rowno;
	editsrv_<?php echo $sid ?>=window.open(urlholder,"editsrv_<?php echo $sid ?>","width=500,height=400,menubar=no,resizable=yes,scrollbars=yes");
}

function chkform(d) {
	
	//alert("chkform = '"+d.gname.value+"'");
	if(d.gname.value==""){
		alert("Please enter a Laboratory Service Group name.");
		d.gname.focus();
	}else if(d.gcode.value==""){
		alert("Please enter a Laboratory Service Group code.");
		d.gname.focus();
	}else{
		$('action').value='addgrp';
		d.submit();
		$('newgroup').disabled = false;
	}	
}

function jsGetServiceGroup(d){
	 //var aDepartment=d.dept_nr;
	 //var aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;
	 var aLabServ=d.parameterselect;
	 var aLabServ_grpid = aLabServ.options[aLabServ.selectedIndex].value;
	
	 //alert("aDepartment_nr = "+aDepartment_nr);	
	//alert("aDepartment_nr = "+aLabServ_grpid);	
	 //xajax_getServiceGroup(aDepartment_nr,aLabServ_grpid);	
	 xajax_getServiceGroup(aLabServ_grpid);	
}

function ajxClearOptions() {
		var optionsList;
		//alert(document.forms["paramselect"].parameterselect.value);	
		var el=document.forms["paramselect"].parameterselect;
		if (el) {
			optionsList = el.getElementsByTagName('OPTION');
			for (var i=optionsList.length-1;i>=0;i--) {
				optionsList[i].parentNode.removeChild(optionsList[i]);
			}
		}
	}/* end of function ajxClearOptions */
		 
	function ajxAddOption(text, value) {
		var grpEl = document.forms["paramselect"].parameterselect;
		if (grpEl) {
			var opt = new Option( text, value );
			opt.id = value;
			grpEl.appendChild(opt);
		}
		var optionsList = grpEl.getElementsByTagName('OPTION');
	}/* end of function ajxAddOption */
	
	function ajxSetServiceGroup(group_id) {
		//alert(document.forms["paramselect"].parameterselect.value);
		//alert("ajxSetServiceGroup = "+group_id);
		document.forms["paramselect"].parameterselect.value = group_id;
	}
	
	//function jsGetLabService(d, mod){
	function jsGetLabService(d, mod){
		var aLabServ=d.parameterselect;
	 	var aLabServ_grpid = aLabServ.options[aLabServ.selectedIndex].value;
		//alert("jsGetLabService = "+aLabServ_grpid);
	 	//alert("jsGetLabService = "+$F('parameterselect'));
		//alert("mod = "+mod);
		if ((mod)&&(aLabServ_grpid!=0)){
			//alert("true");
			xajax_psrv(aLabServ_grpid);
		}else{
			//alert("false");
			xajax_psrv(0);
		}	
	}
	
	function enablebutton(mod){
		if (mod)
			$('newgroup').disabled = false;
		else	
			$('newgroup').disabled = true;
	}
	
// -->
</script>

<?php

$script = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$script);

# Assign Body Onload javascript code

if (isset($parameterselect)) $gid=$parameterselect;
else $gid=$groups[0]['group_id'];
#echo "dept = ".$_POST['dept_nr']."<br>";
#echo "grp = ".$gid."<br>";
#$onLoadJS='onLoad="dept_nr='.$dept_nr.';group_id='.$gid.';xajax_psrv(group_id);"';
#if ((isset($_POST['dept_nr'])) && (isset($parameterselect))){ #jsGetServiceGroup(paramselect);
	#$onLoadJS='onLoad="dept_nr='.$_POST['dept_nr'].';group_id='.$gid.';xajax_psrv(group_id);"';
	$onLoadJS='onLoad="jsGetServiceGroup(paramselect);"';
   #$onLoadJS='onLoad="dept_nr='.$_POST['dept_nr'].';group_id='.$_POST['parameterselect'].';xajax_psrv(group_id);"';
#}	
#$onLoadJS='onLoad="group_id='.$gid.';xajax_psrv(group_id);"';
$onLoadJS.='"';
$smarty->assign('sOnLoadJs',$onLoadJS);


$smarty->assign('sParamGroup',$groupname);

# Create the parameter group select

#$sel=''; #VAS
#if ($parameterselect==$gr['group_id'] || empty($parameterselect)) $sel='selected'; #VAS
#$sTemp = '<select id="parameterselect" name="parameterselect" size="1" onChange="jsGetLabService(paramselect);">
#	       <option value="0">Select a Department first</option>';

# VAS: 06-07-2007  
#------------------------

$all_labgrp=&$srvObj->getLabServiceGroups2();
#echo "sql = ".$srvObj->sql;
#print_r($all_labgrp);
#$parameterselect = 1;
$sTemp = '';
#$sTemp = $sTemp.'<select name="parameterselect" id="parameterselect" onChange="jsGetLabService(paramselect,1);jsGetServiceGroup(paramselect);">
#								<option value="0">Select a Laboratory Service Group</option>';
$sTemp = $sTemp.'<select name="parameterselect" id="parameterselect" onChange="jsGetLabService(paramselect,1);">
								<option value="0">Select a Laboratory Service Group</option>';

					if(!empty($all_labgrp)&&$all_labgrp->RecordCount()){
						while($result=$all_labgrp->FetchRow()){
							$sTemp = $sTemp.'
								<option value="'.$result['group_id'].'" ';
							if(isset($parameterselect)&&($parameterselect==$result['group_id'])) $sTemp = $sTemp.'selected';
							$sTemp = $sTemp.'>'.$result['name'].'</option>';
						}
					}
					$sTemp = $sTemp.'</select>
							<font size=1><img '.createComIcon($root_path,'redpfeil_l.gif','0','',TRUE).'> Laboratory Service Group</font>';
$smarty->assign('sParamGroupSelect',$sTemp);

# AJMQ: Assign the controls for the Create New Group option
$smarty->assign('sNewGroupCode','<input type="text" name="gcode" id="gcode" size="5" value="" onFocus="enablebutton(1);">');
$smarty->assign('sNewGroupName','<input type="text" name="gname" id="gname" value="" onFocus="enablebutton(1);">');
$smarty->assign('sNewGroupOthername','<input type="text" name="goname" id="goname" size="10" value="" onFocus="enablebutton(1);">');

# VAS: 06-07-2007  for Department
#------------------------
/*
#$all_meds=&$dept_obj->getAllMedicalObject();
$all_meds=&$dept_obj->getAllCommonMedical();
#echo "sql = ".$dept_obj->sql;
#print_r($all_meds);
$sTemp = '';
$sTemp = $sTemp.'<select name="dept_nr" id="dept_nr" onChange="jsGetServiceGroup(paramselect);jsGetLabService(paramselect,0);">
					  <option value="0">Select a Department</option>';
#$sTemp = $sTemp.'<select name="dept_nr">';
if(is_object($all_meds)){
	while($deptrow=$all_meds->FetchRow()){
		$sTemp = $sTemp.'
								<option value="'.$deptrow['nr'].'" ';
			if(isset($dept_nr)&&($dept_nr==$deptrow['nr'])) $sTemp = $sTemp.'selected';
				$sTemp = $sTemp.'>';
				if($$deptrow['LD_var']!='') $sTemp = $sTemp.$$deptrow['LD_var'];
				else $sTemp = $sTemp.$deptrow['name_formal'];
		$sTemp = $sTemp.'</option>';
	}
}
$sTemp = $sTemp.'</select><font size=1><img '.createComIcon($root_path,'redpfeil_l.gif','0','',TRUE).'> Department</font>';
$smarty->assign('sNewGroupDept',$sTemp);
*/
#$smarty->assign('sNewGroupSubmit','<input type="button" name="newgroup" value="Create" onclick="$(\'action\').value=\'addgrp\';document.forms[0].submit()">');
$smarty->assign('sNewGroupSubmit','<input type="button" name="newgroup" id="newgroup" value="Create" disabled onclick="chkform(paramselect);enablebutton(0);">');
#----------------------------

# Assign the parameter group hidden and submit inputs
#commented by VAS
/*
$smarty->assign('sSubmitSelect','
	<input type="hidden" name="action" id="action" value="">
	<input type="hidden" name="sid" value="'.$sid.'">
	<input type="hidden" name="lang" value="'.$lang.'">
	<input type="image" '.createLDImgSrc($root_path,'auswahl2.gif','0').'>');
*/
$smarty->assign('sSubmitSelect','
	<input type="hidden" name="action" id="action" value="">
	<input type="hidden" name="sid" value="'.$sid.'">
	<input type="hidden" name="lang" value="'.$lang.'">');

$smarty->assign('sMainBlockIncludeFile','laboratory/lab_services.tpl');

/**
 * show Template
 */
$smarty->display('common/mainframe.tpl');
?>
