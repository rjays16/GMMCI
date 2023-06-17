<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/laboratory/ajax/lab-param.common.php");
require($root_path.'include/inc_environment_global.php');
$lang_tables=array('chemlab_groups.php','chemlab_params.php');
define('LANG_FILE','lab.php');
$local_user='ck_lab_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

# Create lab object
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();

# Check for the department nr., else show department selector
if(!isset($dept_nr)||!$dept_nr){
	if($cfg['thispc_dept_nr']){
		$dept_nr=$cfg['thispc_dept_nr'];
	}else{
		header('Location:seg-lab-select-dept.php'.URL_REDIRECT_APPEND.'&target=labservparam&retpath='.$retpath);
		exit;
	}
}

######################  include_once('seg-lab-services-admin.action.php');
require($root_path.'include/inc_labor_param_group.php');

# Load the date formatter */
include_once($root_path.'include/inc_date_format_functions.php');
$breakfile="labor.php".URL_APPEND;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"Laboratory::Parameters::".$dept_name);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('lab_param_config.php')");

 # hide return  button
 $smarty->assign('pbBack',FALSE);

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Laboratory::Paramters::".$dept_name);

 # collect extra javascript code
ob_start();
echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'modules/laboratory/js/lab-param-gui.js"></script>'."\r\n";
$xajax->printJavascript($root_path.'classes/xajax');

?>
<script language="javascript" name="j1">
<!--  
var editImageSrc='<?= createLDImgSrc($root_path,'edit_sm.gif','0') ?>S';
var dept_nr=-1, group_id=-1, service_code='';
var dept_name='<?= $dept_name ?>',
		group_name='', service_name='';

function chkselect(d)
{
 	if(d.parameterselect.value=="<?php echo $parameterselect ?>"){
		return false;
	}
}

function editParam(id,rowno)
{
	urlholder="<?php echo $root_path ?>modules/laboratory/seg-lab-services-paramedit.php?sid=<?php echo "$sid&lang=$lang" ?>&id="+id+"&row="+rowno;
	editparam_<?php echo $sid ?>=window.open(urlholder,"editparam_<?php echo $sid ?>","width=500,height=465,menubar=no,resizable=yes,scrollbars=yes");
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
$onLoadJS='onLoad="dept_nr='.$dept_nr.';group_id='.$gid.';xajax_loadsrvparam('.$gid.')';
$onLoadJS.='"';
$smarty->assign('sOnLoadJs',$onLoadJS);

# Create the parameter group select
$sel='';
if ($selectgroup==$gr['group_id'] || empty($selectgroup)) $sel='selected';
$sTemp = '<select id="selectgroup" name="selectgroup" size="1" onchange="if (this.selectedIndex!=-1) xajax_lsrv(this.options[this.selectedIndex].value)" style="width:100%">';
foreach ($groups as $gr) {
	$sTemp = $sTemp.'<option value="'.$gr['group_id'].'"';
	if($parameterselect==$gr['group_id']) $sTemp = $sTemp.' selected';
	$sTemp = $sTemp.'>'.$gr['name'];
	$sTemp = $sTemp.'</option>';
	$sTemp = $sTemp."\n";
}
$sTemp = $sTemp.'</select>';
$smarty->assign('sServiceGroupSelect',$sTemp);

$smarty->assign('sServiceSelect','<select id="selectservice" name="selectservice" size="1" style="width:100%"></select>');

# AJMQ: Assign the controls for the Create New Group option
$smarty->assign('sNewParamName','<input type="text" id="pname" name="pname" value="">');
$smarty->assign('sNewParamSubmit','<input type="button" name="newparam" value="Create" onclick="validate(\'pname\')">');

# Assign the parameter group hidden and submit inputs
$smarty->assign('sRefreshPage','
	<input type="hidden" name="action" id="action" value="">
	<input type="hidden" name="sid" value="'.$sid.'">
	<input type="hidden" name="lang" value="'.$lang.'">
	<img '.createLDImgSrc($root_path,'auswahl2.gif','0').' onclick="refreshTitle();xajax_pparam(getSelectedService());return false;" style="cursor:pointer">');

$smarty->assign('sMainBlockIncludeFile','laboratory/lab_params.tpl');

/**
 * show Template
 */
$smarty->display('common/mainframe.tpl');
?>