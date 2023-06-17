<?php
#created by CELSY 06-23-2010
#Display radiology schedule for the day
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require($root_path."modules/radiology/ajax/radio-daily-sched-tray.server.php");

#$xajax->printJavascript($root_path.'classes/xajax_0.5');

define('LANG_FILE','specials.php');
define('NO_2LEVEL_CHK',1);      

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');                               

#must only close overlib
$breakfile="javascript:window.close();";
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'include/inc_config_color.php');
																																
$thisfile=basename(__FILE__);                                     
 
 if(empty($_GET['sub_dept_nr_name']))	
	$sub_dept_nr_name = $_POST['sub_dept_nr_name'];
 else
	$sub_dept_nr_name = $_GET['sub_dept_nr_name'];

 if(empty($_GET['month']))	
	$month = $_POST['month'];
 else
	$month = $_GET['month'];
	
	if(empty($_GET['mo']))	
	$mo = $_POST['mo'];
 else
	$mo = $_GET['mo'];
	
 if(empty($_GET['day']))	
	$date = $_POST['day'];
 else
	$date = $_GET['day'];
	
 if(empty($_GET['year']))	
	$year = $_POST['year'];
 else
	$year = $_GET['year'];

$LDTitleMgr = $sub_dept_nr_name." :: ".$month." ".$date.", ".$year.":: Radiology Schedule";
 echo $LDTitleMgr;             
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);
 $smarty->assign('sToolbarTitle',"$LDTitleMgr");   
 
	# href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDTitleMgr");      
 $smarty->assign('sOnLoadJs','onLoad=""');	

 # Collect javascript code
 ob_start();

?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<script type="text/javascript" src="<?= $root_path ?>js/listgen/listgen.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="<?=$root_path?>js/listgen/css/default/default.css"></link>
<script type="text/javascript" src="<?=$root_path?>modules/radiology/ajax/radio-daily-sched-tray.js"></script>

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
 alert($LDTitleMgr);
 -->
</script>

<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>





<script language="javascript" >
<!--   //functions here!
function call_display_radio()
{
	
	//Fetch entries for the day
	 //get_daily_radiology(department, date, mo, year);
			//Create table to print radiology schedule for the day  
	document.write('<form ENCTYPE="multipart/form-data" action="<?=$thisfile?>" method="POST" name="inputgroupform" id="inputgroupform">');
	document.write('<div style="background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden;">');   
	document.write('<table border="0" width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%; font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden">')
	document.write('<tbody>');
	//edit for loop conditions
	for(i = 0; i < 3; i++){
		 document.write('<tbody><tr><td width="20%">Patient:</td>');
		 document.write('<td> hi  <? //Last Name (Sex, Age) here ?></td>');
		 document.write('</tr><tr><td width="20%">Time:</td>');
		 document.write('<td>hhhjh <?//Scheduled Time here ?>   </td>');
		 document.write('</tr><tr><td width="20%">Doctor:</td>');
		 document.write('<td> ghhg<?//Doctors name =echo ucfirst($person['name_last']).', '.ucfirst($person['name_first']); ?></td>');
		 document.write('</tr><tr><td width="20%">Procedures: </td>');
		 document.write('<td rowspan=3>ghfh <?//Procedures here ?></td>');   
		 document.write('</tr></tbody>');
	}                          
	
	document.write('</tbody>');
	document.write('</table>');
	document.write('</div></form>');               

}


-->
</script>  
<?php

//$xajax->printJavascript($root_path.'classes/xajax');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('sFormEnd','</form>');

#assign smartys here

call_display_radio();



$smarty->assign('closeBtn','<a onclick="window.close(); return false;" href="#"><img id="close" name="close" src="../../gui/img/control/default/en/en_close.gif" border=0 alt="Close" title="Close" style="cursor:pointer"></a></tr>'); 

# Buffer page output
ob_start();

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

# Assign the form template to mainframe
 $smarty->assign('sMainBlockIncludeFile','or/anesthesia/tray_gui.tpl');
 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>


