<?php

define('ROW_MAX',15); # define here the maximum number of rows for displaying the parameters

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/radiology/ajax/radio-admin.common.php");
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
#$local_user='ck_lab_user';
$local_user='ck_radio_user';   # burn added : October 24, 2007
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

//$db->debug=true;
# Create lab object
require_once($root_path.'include/care_api_classes/class_radiology.php');
$srvObj=new SegRadio();

#---added by VAS
#require_once($root_path.'include/care_api_classes/class_department.php');
#$dept_obj=new Department;

include_once('seg-radio-services-admin.action.php');
#require($root_path.'include/inc_labor_param_group.php');


# Load the date formatter */
include_once($root_path.'include/inc_date_format_functions.php');
    

$breakfile="radiolog.php".URL_APPEND;

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
 $smarty->assign('sToolbarTitle',"Radiology :: Services Manager");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('lab_param_config.php')");

 # hide return  button
 $smarty->assign('pbBack',FALSE);

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Radiology :: Services Manager");

 # collect extra javascript code
 ob_start();
echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'modules/radiology/js/radio-admin-gui.js"></script>'."\r\n";
#$xajax->printJavascript($root_path.'classes/xajax');

if ($xajax) {
	$xajax->printJavascript($root_path.'classes/xajax');
}
?>
<script language="javascript" name="j1">
<!--  
//var dept_nr=-1, group_id=-1;
var group_id=-1;
var timeoutHandle=0;
popWindowEditRadio = "";

function chkselect(d){
 	if(d.parameterselect.value=="<?php echo $parameterselect ?>"){
		return false;
	}
}


function closechild(){
	try{
		if ((popWindowEditRadio!=null)&&(false == popWindowEditRadio.closed)){
			popWindowEditRadio.close();
		}
	}finally{
		alert('Deleted sucessfully!');
	}
}

function editService(nr,rowno){
	
	var grpcode = document.forms["paramselect"].parameterselect.value;
	var deptnr = document.forms["paramselect"].dept_nr.value;
	//alert("deptnr = "+deptnr);
	/*
	urlholder="<?php echo $root_path ?>modules/radiology/seg-radio-services-edit.php?sid=<?php echo "$sid&lang=$lang" ?>&nr="+nr+"&deptnr="+deptnr+"&grpcode="+grpcode+"&row="+rowno;
	editsrv_<?php echo $sid ?>=window.open(urlholder,"editsrv_<?php echo $sid ?>","width=500,height=400,menubar=no,resizable=yes,scrollbars=yes");
	*/
	
	var w=window.screen.width;
	var h=window.screen.height;
	var ww=500;
	var wh=500;
	urlholder="<?php echo $root_path ?>modules/radiology/seg-radio-services-edit.php?sid=<?php echo "$sid&lang=$lang" ?>&nr="+escape(nr)+"&deptnr="+deptnr+"&grpcode="+grpcode+"&row="+rowno;

	if (window.showModalDialog){  //for IE
		window.showModalDialog(urlholder,"width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes, center=yes");
	}else{
		popWindowEditRadio=window.open(urlholder,"editsrv_<?php echo $sid ?>","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes, left=300, top=100");
		//window.editsrv_<?php echo $sid ?>.moveTo((w/2)+80,(h/2)-(wh/2));
	}
}

function chkform(d) {
	
	//alert("chkform = '"+d.gname.value+"'");
	if(d.gname.value==""){
		alert("Please enter a Radiology Service Group name.");
		d.gname.focus();
	}else if(d.gcode.value==""){
		alert("Please enter a Radiology Service Group code.");
		d.gname.focus();
	}else{
		$('action').value='addgrp';
		d.submit();
		$('newgroup').disabled = false;
	}	
}

function jsGetServiceGroup(d){
	 //alert("jsGetServiceGroup = "+$F('dept_nr'));
	 //xajax_getServiceGroup("none");	
	 xajax_getServiceGroup($F('dept_nr'));	
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
	
	function ajxSetServiceGroup(group_code) {
		document.forms["paramselect"].parameterselect.value = group_code;
	}
	
	function jsGetRadioService(d, mod){
		var serv_code = $F('searchserv');
		var dept_nr = $F('dept_nr');
		var aRadioServ=d.parameterselect;
	 	var aRadioServ_grpid = aRadioServ.options[aRadioServ.selectedIndex].value;
		
		//alert("jsGetRadioService : grp = "+aRadioServ_grpid);
		if ((serv_code == "none")||(serv_code=="")){
			if ((mod)&&(aRadioServ_grpid!=0)){
				xajax_psrv(aRadioServ_grpid, "none",dept_nr);
			}else{
				xajax_psrv(0, "none",0);
			}
		}else{
			if (timeoutHandle) {
				clearTimeout(timeoutHandle);
				timeoutHandle=0;
			}
		
			if ((aRadioServ_grpid!="none")&&((serv_code!="")||(serv_code!="none"))){
				timeoutHandle=setTimeout("xajax_psrv('"+aRadioServ_grpid+"','"+serv_code+"','"+dept_nr+"')",300);
			}else{
				timeoutHandle=setTimeout("xajax_psrv(0,'none',0)",300);
			}
		}		
	}
	
	function enablebutton(mod){
		if (mod)
			$('newgroup').disabled = false;
		else	
			$('newgroup').disabled = true;
	}
	
	function fetchServList(ms) {

		var serv_code = $F('searchserv');
		var aLabServ_grpcode = $F('parameterselect');
		if (timeoutHandle) {
			clearTimeout(timeoutHandle);
			timeoutHandle=0;
		}
		
		if ((aLabServ_grpcode!="none")&&(serv_code)){
			timeoutHandle=setTimeout("xajax_psrv('"+aLabServ_grpcode+"','"+serv_code+"','"+dept_nr+"')",ms);
		}else{
			timeoutHandle=setTimeout("xajax_psrv('"+aLabServ_grpcode+"','none',0)",ms);
		}	
	}
	
	function clearText(){
		document.getElementById("searchserv").value="";
	}
	
// -->
</script>

<?php

$script = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$script);

# Assign Body Onload javascript code

if (isset($parameterselect)) $gid=$parameterselect;
else $gid=$groups[0]['group_code'];

$onLoadJS='onLoad="jsGetServiceGroup(paramselect);"';
$onLoadJS.='"';
$smarty->assign('sOnLoadJs',$onLoadJS);


$smarty->assign('sParamGroup',$groupname);

$all_meds=&$dept_obj->getAllRadiologyDept();
#echo "sql = ".$dept_obj->sql;
#print_r($all_meds);
$sTemp = '';
#$sTemp = $sTemp.'<select name="dept_nr" id="dept_nr" onChange="jsGetServiceGroup(paramselect);jsGetLabService(paramselect,0);">
$sTemp = $sTemp.'<select name="dept_nr" id="dept_nr" onChange="jsGetServiceGroup(paramselect);">
					  <option value="0">Select a Department</option>';
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
$sTemp = $sTemp.'</select><font size=1><img '.createComIcon($root_path,'redpfeil_l.gif','0','',TRUE).'> Radiology Department</font>';
$smarty->assign('sDeptSelect',$sTemp);

$sTemp = '';
$sTemp = $sTemp.'<select name="parameterselect" id="parameterselect" onChange="jsGetRadioService(paramselect,1);">';

			$sTemp = $sTemp.'</select>
							<font size=1><img '.createComIcon($root_path,'redpfeil_l.gif','0','',TRUE).'> Radiology Service Group</font>';
$smarty->assign('sParamGroupSelect',$sTemp);

$smarty->assign('sNewGroupCode','<input type="text" name="gcode" id="gcode" size="5" value="" onFocus="enablebutton(1);">');
$smarty->assign('sNewGroupName','<input type="text" name="gname" id="gname" value="" onFocus="enablebutton(1);">');
$smarty->assign('sNewGroupOthername','<input type="text" name="goname" id="goname" size="10" value="" onFocus="enablebutton(1);">');

$smarty->assign('sNewGroupSubmit','<input type="button" name="newgroup" id="newgroup" value="Create" disabled onclick="chkform(paramselect);enablebutton(0);">');
#----------------------------
$smarty->assign('sFilter','Filter: <input type="text" id="searchserv" name="searchserv" style="width:120px" value="" onKeyUp="fetchServList(300);">&nbsp;<img src="../../gui/img/common/default/redpfeil_l.gif"><font size=1>&nbsp;Service Code</font>');

$smarty->assign('sSubmitSelect','
	<input type="hidden" name="action" id="action" value="">
	<input type="hidden" name="sid" value="'.$sid.'">
	<input type="hidden" name="lang" value="'.$lang.'">');

$smarty->assign('sMainBlockIncludeFile','radiology/radio_services.tpl');

/**
 * show Template
 */
$smarty->display('common/mainframe.tpl');
?>
