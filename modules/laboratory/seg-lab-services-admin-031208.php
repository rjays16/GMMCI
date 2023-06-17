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

#---added by VAS
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

include_once('seg-lab-services-admin.action.php');

require_once($root_path.'include/inc_labor_param_group.php');

# Load the date formatter */
include_once($root_path.'include/inc_date_format_functions.php');
    
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
 $smarty->assign('sToolbarTitle',"Laboratory::Services Administration");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('lab_param_config.php')");

 # hide return  button
 $smarty->assign('pbBack',FALSE);

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 #$smarty->assign('sWindowTitle',"Laboratory::Services admin::".$dept_name);
 $smarty->assign('sWindowTitle',"Laboratory::Services Administration");

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
<!--added by VAN 02-06-08-->
<!--for shortcut keys -->
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script language="javascript" name="j1">
<!--  
//var dept_nr=-1, group_id=-1;
var group_code=-1;
var popWindowEditLab="";
//var group_code="none";

//---------------adde by VAN 02-06-08
	function ShortcutKeys(){
		shortcut.add('Ctrl+Shift+M', BackMainMenu,
							{
								'type':'keydown',
								'propagate':false,
							}
						 )					 
	}

	function BackMainMenu(){
		urlholder="labor.php<?=URL_APPEND?>";
		window.location.href=urlholder;
	}
//--------------------------------------

function chkselect(d){
 	if(d.parameterselect.value=="<?php echo $parameterselect ?>"){
		return false;
	}
}

function closechild(){
	//alert("popWindowEditLab= '"+popWindowEditLab+"'");
	try{
		if ((popWindowEditLab!=null)&&(false == popWindowEditLab.closed)){
			popWindowEditLab.close();
		}
	}finally{
		alert('Deleted sucessfully!');
	}
}

function editService(nr,rowno){
	var grpcode = document.forms["paramselect"].parameterselect.value;
	//alert("nr = "+nr);
	//urlholder="<?php echo $root_path ?>modules/laboratory/seg-lab-services-edit.php?sid=<?php echo "$sid&lang=$lang" ?>&nr="+nr+"&grpcode="+grpcode+"&row="+rowno;
	//editsrv_<?php echo $sid ?>=window.open(urlholder,"editsrv_<?php echo $sid ?>","width=500,height=400, left=250, top=200, menubar=no,resizable=yes,scrollbars=yes");
	var w=window.screen.width;
	var h=window.screen.height;
	var ww=500;
	var wh=500;
	//alert("encodeURI(nr) = "+encodeURI(nr));
	urlholder="<?php echo $root_path ?>modules/laboratory/seg-lab-services-edit-031208.php?sid=<?php echo "$sid&lang=$lang" ?>&nr="+escape(nr)+"&grpcode="+grpcode+"&row="+rowno;
   //alert("urlholder = "+urlholder);
	//alert("urlholder:encodeURI = "+encodeURI(urlholder));
	//urlholder = encodeURI(urlholder);
	if (window.showModalDialog){  //for IE
		window.showModalDialog(urlholder,"width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes, center=yes");
	}else{
		popWindowEditLab=window.open(urlholder,"editsrv_<?php echo $sid ?>","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes, left=300, top=100");
		//window.editsrv_<?php echo $sid ?>.moveTo((w/2)+80,(h/2)-(wh/2));
	}

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
	 xajax_getServiceGroup("none");	
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
		//alert(document.forms["paramselect"].parameterselect.value);
		//alert("ajxSetServiceGroup = "+group_id);
		document.forms["paramselect"].parameterselect.value = group_code;
	}
	
	//function jsGetLabService(d, mod){
	function jsGetLabService(d, mod){
		var serv_code = $F('searchserv');
		var aLabServ_grpcode = d.options[d.selectedIndex].value;
		
		//alert("serv_code = "+serv_code);
		if (aLabServ_grpcode!=0)
			document.getElementById("sparamgroup").innerHTML = d.options[d.selectedIndex].text;
		else
			document.getElementById("sparamgroup").innerHTML = "&nbsp;";	
		
		if ((serv_code == "none")||(serv_code=="")){
			if ((mod)&&(aLabServ_grpcode!="none")){
				xajax_psrv(aLabServ_grpcode,"none");
			}else{
				xajax_psrv(0,"none");
			}	
		}else{
		
			if (timeoutHandle) {
				clearTimeout(timeoutHandle);
				timeoutHandle=0;
			}
		
			if ((aLabServ_grpcode!="none")&&((serv_code!="")||(serv_code!="none"))){
				timeoutHandle=setTimeout("xajax_psrv('"+aLabServ_grpcode+"','"+serv_code+"')",300);
			}else{
				timeoutHandle=setTimeout("xajax_psrv(0,'none')",300);
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
		//checkFilter();
		
		if ((aLabServ_grpcode!="none")&&(serv_code)){
			timeoutHandle=setTimeout("xajax_psrv('"+aLabServ_grpcode+"','"+serv_code+"')",ms);
		}else{
			timeoutHandle=setTimeout("xajax_psrv('"+aLabServ_grpcode+"','none')",ms);
		}	
	}
	
	function clearText(){
		document.getElementById("searchserv").value="";
	}
	
	function enableSelect(mod){
		// edited by VAN 01-30-08
		//if (mod)
			document.getElementById("parameterselect").disabled = false;
		//else
			//document.getElementById("parameterselect").disabled = true;	
	}
	
	// added by VAN 01-23-08
	function checkFilter(){
		//alert("bol = "+(($F('searchserv')==null) || ($F('searchserv')=="") || ($F('searchserv')==" ")));
		if (($F('searchserv')==null) || ($F('searchserv')=="") || ($F('searchserv')==" "))
			document.getElementById("parameterselect").disabled = true;	
		else
			document.getElementById("parameterselect").disabled = false;		
			
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
#echo "<br>grp = ".$gid."<br>";

	$onLoadJS='onLoad="ShortcutKeys(); jsGetServiceGroup(paramselect);"';

$onLoadJS.='"';
$smarty->assign('sOnLoadJs',$onLoadJS);

#echo "groupname = ".$groupname;
#$groupname = "";
$smarty->assign('sParamGroup',$groupname);


# VAS: 06-07-2007  
#------------------------

#$all_labgrp=&$srvObj->getLabServiceGroups2();
#echo "sql = ".$srvObj->sql;
#print_r($all_labgrp);
#$parameterselect = 1;
#echo "<br>parameterselect = ".$parameterselect;

$sTemp = '';
#edited by VAN 01-23-08
$sTemp = $sTemp.'<select name="parameterselect" id="parameterselect" onChange="jsGetLabService(this,1);clearText();enableSelect(0);">';
#$sTemp = $sTemp.'<select name="parameterselect" id="parameterselect" disabled onChange="jsGetLabService(this,1);clearText();enableSelect(0);" onBlur="checkFilter();">';
								#<option value="none">Select a Laboratory Service Group</option>';
/*
					if(!empty($all_labgrp)&&$all_labgrp->RecordCount()){
						while($result=$all_labgrp->FetchRow()){
							$sTemp = $sTemp.'
								<option value="'.$result['group_code'].'" ';
							if(isset($parameterselect)&&($parameterselect==$result['group_code'])) $sTemp = $sTemp.'selected';
							$sTemp = $sTemp.'>'.$result['name'].'</option>';
						}
					} */
					$sTemp = $sTemp.'</select>
							<font size=1><img '.createComIcon($root_path,'redpfeil_l.gif','0','',TRUE).'> Laboratory Service Group</font>';
$smarty->assign('sParamGroupSelect',$sTemp);

#$smarty->assign('sNewGroupCode','<input type="text" name="gcode" id="gcode" size="5" value="" onFocus="enablebutton(1);" onBlur="enablebutton(0);">');
$smarty->assign('sNewGroupCode','<input type="text" name="gcode" id="gcode" size="5" value="" onFocus="enablebutton(1);">');
#$smarty->assign('sNewGroupName','<input type="text" name="gname" id="gname" value="" onFocus="enablebutton(1);" onBlur="enablebutton(0);">');
$smarty->assign('sNewGroupName','<input type="text" name="gname" id="gname" value="" onFocus="enablebutton(1);">');
#$smarty->assign('sNewGroupOthername','<input type="text" name="goname" id="goname" size="10" value="" onFocus="enablebutton(1);" onBlur="enablebutton(0);enableSelect(0);">');
$smarty->assign('sNewGroupOthername','<input type="text" name="goname" id="goname" size="10" value="" onFocus="enablebutton(1);">');

#$smarty->assign('sNewGroupSubmit','<input type="button" name="newgroup" id="newgroup" value="Create" style="cursor:pointer" disabled onClick="chkform(paramselect);enablebutton(0);">');
$smarty->assign('sNewGroupSubmit','<input type="button" name="newgroup" id="newgroup" value="Create" style="cursor:pointer" disabled onClick="chkform(paramselect);">');

#$smarty->assign('sFilter','Filter: <input type="text" id="searchserv" name="searchserv" style="width:120px" value="" onKeyUp="fetchServList(300)" onBlur="clearText();">&nbsp;<img src="../../gui/img/common/default/redpfeil_l.gif"><font size=1>&nbsp;Service Code</font>');
#edited by VAN 01-23-08
$smarty->assign('sFilter','Filter: <input type="text" id="searchserv" name="searchserv" style="width:120px" value="" onFocus="enableSelect(1);" onKeyUp="fetchServList(300);">&nbsp;<img src="../../gui/img/common/default/redpfeil_l.gif"><font size=1>&nbsp;Service Code</font>');
#$smarty->assign('sFilter','Filter: <input type="text" id="searchserv" name="searchserv" style="width:120px" value="" onFocus="checkFilter();" onKeyUp="fetchServList(300);">&nbsp;<img src="../../gui/img/common/default/redpfeil_l.gif"><font size=1>&nbsp;Service Code</font>');

/*
$fileforward="seg-lab-request-new-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
$smarty->assign('sViewRequest','<a href="'.$fileforward.'"><img '.createLDImgSrc($root_path,'showrequest.gif','0','left').' border=0 alt="View the List of Requestors"></a>');

$fileforward2="seg-lab-request-new.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
$smarty->assign('sAddNewRequest','<a href="'.$fileforward2.'"><img '.createLDImgSrc($root_path,'newrequest.gif','0','left').' border=0 alt="Enter New Lab Request"></a>');
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