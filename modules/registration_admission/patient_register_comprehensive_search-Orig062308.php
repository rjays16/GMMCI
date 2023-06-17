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
require($root_path.'include/inc_front_chain_lang.php');

#echo "dept = ".$dept;
#$dept_name = $dept;

if(empty($target)) $target='comprehensive';
#echo "target = ".$target;
#echo "origin = ".$origin;

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

 $smarty->assign('sToolbarTitle',$LDPatientRegister." - ".$LDComprehensiveSearch);

 # Added for the common header top block
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPatientRegister." - ".$LDComprehensiveSearch')");
 #echo "breakfile = $breakfile";	
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$LDPatientRegister." - ".$LDComprehensiveSearch);

 $smarty->assign('sOnLoadJs','onLoad="document.searchform.searchkey.select()"');

 $smarty->assign('pbHelp',"javascript:gethelp('person_how2search.php')");

 $smarty->assign('pbBack',FALSE);
 ?>
 
	<!-- added by VAN 06-20-08 -->
	<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
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
	background-color:#0000ff;
	border:1px solid #4d4d4d;
}
.olcg {
	background-color:#aa00aa; 
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffcc; 
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

 <script type="text/javascript">
 	function UpdateQuery(objVal){
		//alert("objVal = "+objVal);
		var enctype;
		//AND enc.encounter_type IN (3,4)
		if (objVal==0)
			enctype = "";
		else if (objVal==1)
			enctype = "AND enc.encounter_type IN (1)";	
		else if (objVal==2)
			enctype = "AND enc.encounter_type IN (2)";		
		else if (objVal==3)
			enctype = "AND enc.encounter_type IN (3,4)";		
	}
 </script>		 
<?php
 
 #
 # Create the search object
 #
 require_once($root_path.'include/care_api_classes/class_gui_comp_search_person.php');
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