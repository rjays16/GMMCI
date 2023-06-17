<?php         
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');              
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template    
require_once($root_path.'include/inc_date_format_functions.php'); //include the date formatting functions      
require($root_path.'modules/or/ajax/or_schedule_viewer.common.php');           

																													
/*
** Created by Celsy
** Created on 06/28/2010
** This class provides a calendar in which the user 
** may view the or sched for the day
*/
#define('LANG_FILE','lab.php');
define('LANG_FILE','or.php');  
define('NO_CHAIN',1);							 
															
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');              
																																												
$title="OR :: Schedule Viewer";                       
$breakfile = $root_path.'main/op-doku.php';
$thisfile='or_view_calendar_sched.php';
$prev_date='';
																																							
# Start Smarty templating here
require_once($root_path.'include/inc_front_chain_lang.php');
																																					
 $smarty = new smarty_care('common');
																																			 
	# Title in the title bar
 $smarty->assign('sToolbarTitle',"$title");      
																																	 
 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title");
 
 $smarty->assign('sOnLoadJs',"");

# Buffer page output 
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
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>            
																																																														
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/calendar_or_schedule/calendar.css" type="text/css"/>
<script type="text/javascript" src="<?=$root_path?>js/calendar_or_schedule/calendar_or.js"></script>                             
																																																 
<?php
	 $smarty->assign('sFormStart','<form name="inputform" id="inputform" method="POST">');
	 $smarty->assign('sFormEnd','</form>');
	 $smarty->assign('dateInput','<input type="hidden" name="datepicked" id="datepicked"/>');
	 $jsCalScript = "<script type=\"text/javascript\">   
										new tcal ({         
											'formname': 'inputform',    
											'controlname': 'datepicked'
										});   
									</script>";	    
	 $smarty->assign('jsCalendarSetup', $jsCalScript); 
	 $sTemp='';
	 ob_start();
 ?>
<script type="text/javascript">
 function show_calendar_tray(d,m,y)
 {
	 var calenDate = y+"-"+m+"-"+d;   
	 var ol_caption = "OR Schedule :: "+A_TCALDEF.months[m-1]+" "+d+", "+y;   
	 var popup_html ='<div id="body" style="overflow: auto; height: 222px; background: silver; border: 2px;"></div>';
	 //alert("calendate  "+ol_caption+"!  ");    
	 xajax_get_or_today(calenDate);  
	 return overlib(
		popup_html,     
		WIDTH, 420, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL,
		CAPTIONPADDING, 4, CAPTION, ol_caption, MIDX, 0, MIDY, 0, STATUS, ol_caption);       
 }     
 //OLiframeContent('or_view_calendar_sched_tray.php?dbdate='+calenDate+'', 650, 300, 'orSchedule', 1, 'auto'),                           
</script>


<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sOnHoverMenu',$sTemp);

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
 $smarty->assign('sMainBlockIncludeFile','or/or_view_calendar_sched.tpl');

/** show Template**/
 $smarty->display('common/mainframe.tpl'); 
?>
