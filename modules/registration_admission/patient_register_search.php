<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System beta 2.0.1 - 2004-07-04
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/

# The language table
define('LANG_FILE','aufnahme.php');
$local_user='aufnahme_user';
define('NO_2LEVEL_CHK',1);
require($root_path.'include/inc_front_chain_lang.php');

#echo "dept = ".$dept;
#$dept_name = $dept;

if(empty($target)) $target='search';


switch($origin)
{
		case 'archive': $breakfile='patient_register_archive.php';
													 break;
		case 'admit': $breakfile='patient.php';
													 break;
	/*---replaced, 2007-10-03 FDP
		default : $breakfile='patient.php';
	-----*/
	default : $breakfile=$root_path.'main/startframe.php';
}

$breakfile.=URL_APPEND;

if ($ptype=='er')
	$breakfile=$root_path.'modules/er/seg-er-functions.php'.URL_APPEND;
elseif ($ptype=='opd')
	$breakfile=$root_path.'modules/opd/seg-opd-functions.php'.URL_APPEND;
elseif ($ptype=='ipd')
	$breakfile=$root_path.'modules/ipd/seg-ipd-functions.php'.URL_APPEND;
elseif ($ptype=='phs')
    $breakfile=$root_path.'modules/ipd/seg-phs-functions.php'.URL_APPEND;    
elseif ($ptype=='medocs')
	$breakfile=$root_path.'modules/medocs/seg-medocs-functions.php'.URL_APPEND;	
	
$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Added for the common header top block

 $smarty->assign('sToolbarTitle',$LDPatientRegister." - ".$LDSearch);

 # Added for the common header top block
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPatientRegister." - ".$LDSearch')");
 #echo "breakfile = $breakfile";
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$LDPatientRegister." - ".$LDSearch);

 $smarty->assign('sOnLoadJs','onLoad="document.searchform.searchkey.select();DisabledSearch();"');

 $smarty->assign('pbHelp',"javascript:gethelp('person_how2search.php')");

 $smarty->assign('pbBack',FALSE);


 #
 # Create the search object
 #
 require_once($root_path.'include/care_api_classes/class_gui_search_person.php');
 $psearch = & new GuiSearchPerson;

# Start buffering the text above  the search block
 ob_start();

/* Load the tabs */
$tab_bot_line='#66ee66'; // Set the horizontal bottom line color
require('./gui_bridge/default/gui_tabs_patreg.php');
#require('./gui_bridge/default/gui_tabs_patreg.php'.URL_APPEND.'&dept='.$dept_name);

/* If the origin is admission link, show the search prompt */
if(isset($origin) && $origin=='pass')
{
?>
<table border=0>
	<tr>
		<td valign="bottom"><img <?php echo createComIcon($root_path,'angle_down_l.gif','0') ?>></td>
		<td class="prompt"><?php echo $LDPlsSelectPatientFirst ?></td>
		<td><img <?php echo createMascot($root_path,'mascot1_l.gif','0','absmiddle') ?>></td>
	</tr>
</table>
<?php
}

 $sTemp = ob_get_contents();
 ob_end_clean();

# Assign the preceding text
$psearch->pretext=$sTemp;

# sets the type of search (person or personnel)
# burn added: March 16, 2007
$psearch->setSearchType("person");

$psearch->setTargetFile('patient_register_show.php');

/*----replaced, 2007-10-03 FDP
$psearch->setCancelFile($breakfile);
------*/
$psearch->setCancelFile('patient.php');

$psearch->setPrompt($LDEnterPersonSearchKey);

# Set to TRUE if you want to auto display a single result
//$psearch->auto_show_byalphanumeric =TRUE;
# Set to TRUE if you want to auto display a single result based on a numeric keyword
# usually in the case of barcode scanner data
$psearch->auto_show_bynumeric = TRUE;

# Start buffering the appending post search text
ob_start();

# If the origin is admission link, show a button for creating an empty form
if(isset($origin) && $origin=='pass')
{
?>
<form action="patient_register.php" method=post>

<?php
#------------edited by vanessa 03-26-07-------------
include_once($root_path.'include/care_api_classes/class_department.php');

$dept_obj=new Department;
#$dept_belong = $dept_obj->getUserDeptInfo($HTTP_SESSION_VARS['sess_user_name']);
#echo "dept_belong = ".$dept_belong['id'];
	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);


if (($dept_belong['id']=="OPD-Triage")||($dept_belong['id']=="ER")||(($dept_belong['id']=="HRD"))){
?>
<!--commented by VAN 05-06-08-->
<!--<input type=submit value="<?php echo $LDNewForm ?>">-->

<?php } ?>
<input type=hidden name="sid" value=<?php echo $sid; ?>>
<input type=hidden name="lang" value="<?php echo $lang; ?>">
<!--<input type="hidden" name="dept_belong" value="<?php echo $dept_name?>">-->
</form>
<?php
}

#  End buffering and assign buffer contents to template

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the appending post search text
$psearch->posttext = $sTemp;

$smarty->assign('sMainDataBlock',$psearch->create());

$smarty->assign('sMainBlockIncludeFile','registration_admission/reg_plain.tpl');

# Show mainframe

$smarty->display('common/mainframe.tpl');



?>