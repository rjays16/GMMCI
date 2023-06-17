<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/billing/ajax/billing.common.php');
define('NO_CHAIN',1);

$lang_tables[]='search.php';
define('LANG_FILE','finance.php');
$local_user='aufnahme_user';

require_once($root_path.'include/inc_front_chain_lang.php');
$thisfile=basename(__FILE__);

/*
	Insert additional code here
*/

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"$LDBillingMain :: $LDManager");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('billing_main.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDBillingMain :: $LDListAll");

# Buffer page output
ob_start();

# insert javascript function here

?>
<!-- prototype -->
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>

<!-- Calendar -->
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />

<!-- include billing.css -->
<link type="text/css" rel="stylesheet" href="css/billing.css">

<!-- Core module and plugins: -->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>


<?php
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);



#Left Column
//Patient ID
$smarty->assign('sPid','<input class="segInput" id="pname" name="pname" type="text" size="25" value="" style="font:bold 12px Arial; float;left;" readOnly ');
//Patient name
$smarty->assign('sPatientName','<input class="segInput" id="pname" name="pname" type="text" size="40" value="" style="font:bold 12px Arial; float;left;" readOnly ');
//Patient Address
$smarty->assign('sPatientAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" '.$readOnly.'>'.$_POST['orderaddress'].'</textarea>');
//Select Patient
$smarty->assign('sSelectPatient','<input class="segInput" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" onclick="alert(\'Hello\')" style="margin-left:2px"/>');


#Right Column
//Patient Admission Encounter
$smarty->assign('sPatientEnc','<input class="segInput" id="pname" name="pname" type="text" size="25" value="" style="font:bold 12px Arial; float;left;" readOnly ');
//Date
$smarty->assign('sDate','<input name="orderdate" type="text" size="10" value="'.$_POST['orderdate'].'" style="font:bold 12px Arial">');


# Assign page output to the mainframe template

//$smarty->assign('sMainFrameBlockData',$sTemp);
$smarty->assign('sMainBlockIncludeFile','billing/billing_form.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>