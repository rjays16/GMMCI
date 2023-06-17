<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/registration_admission/ajax/comp_search.common.php");
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

 #$smarty->assign('sOnLoadJs','onLoad="UpdateQuery(0);"');
 $smarty->assign('sOnLoadJs','onLoad="preSet();DisabledSearch();"');

 $smarty->assign('pbHelp',"javascript:gethelp('person_how2search.php')");

 $smarty->assign('pbBack',FALSE);
 
 #ob_start();
 #$xajax->printJavascript($root_path.'classes/xajax');
 #$sTemp = ob_get_contents();
 #ob_end_clean();

 # $smarty->append('JavaScript',$sTemp);

 # Buffer page output

 ob_start();
 $xajax->printJavascript($root_path.'classes/xajax');
 ?>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<!-- YUI Library -->
<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dom/dom.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dragdrop/dragdrop.js" ></script>

<script type="text/javascript" src="<?=$root_path?>js/yui/container/container.js"></script>
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/container/assets/container.css">
 
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
    
    function UpdateQuery(objVal){
		//alert("objVal = "+objVal);
		
		var enctype;
				
		if (objVal==0)
			enctype = "";
		else if (objVal==1)
			enctype = "AND enc.encounter_type IN (1)";	
		else if (objVal==2)
			enctype = "AND enc.encounter_type IN (2)";		
		else if (objVal==3)
			enctype = "AND enc.encounter_type IN (3,4)";	
		
		xajax_populatePatientList(enctype);
		document.getElementById('enctype').value = enctype;
		//document.getElementById('key').value = '<?= $HTTP_SESSION_VARS['searchkey']?>';
		//alert('key = '+document.getElementById('key').value);	
		
	}
	
	function preSet(){
		//var post_key = '<?= $HTTP_SESSION_VARS['searchkey']?>';
		//alert("preset = "+post_key);
		//document.getElementById('searchkey').value = post_key;
        //$('searchkey').value = '';
        $('searchkey').focus();
	}
	
	function onsubmitForm(){
		searchform.submit();
	}
 </script>		 
<?php
 
 #
 # Create the search object
 #
 /*
echo "kwy = ".$HTTP_SESSION_VARS['searchkey'];
die();
*/
 require_once($root_path.'include/care_api_classes/class_gui_comp_search_person.php');
 $psearch = & new GuiSearchPerson;
 #die('here');
# Start buffering the text above  the search block
 ob_start();

/* Load the tabs */
$tab_bot_line='#66ee66'; // Set the horizontal bottom line color
#require('./gui_bridge/default/gui_tabs_patreg.php');
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
#die('here = '.$origin);

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