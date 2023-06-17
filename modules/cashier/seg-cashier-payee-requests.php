<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/

define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$title=$LDPharmacy;
$breakfile=$root_path."modules/cashier/seg-cashier-requests.php".URL_APPEND."&mode=payor";
$imgpath=$root_path."pharma/img/";
$thisfile='seg-cashier-payee-requests.php';

$sRefNo=$_GET["ref"];
$sDept=strtolower($_GET["src"]);
if (!$sRefNo || !$sDept) {
	die("Invalid reference...");
}

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/class_cashier.php");

$cClass = new SegCashier();
global $db;

$payeeInfo = $cClass->GetPayeeInformationFromRequest($sDept,$sRefNo);
$pid = $payeeInfo['pid'];
$encounter_nr = $payeeInfo['encounter_nr'];
# echo("sDept=$sDept, sRefNo=$sRefNo");

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Saving
if (isset($_POST["submitted"])) {
}

$smarty->assign('sRootPath',$root_path);

# Title in the title bar
$smarty->assign('sToolbarTitle',"Cashier::View payor's active requests");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Cashier::View payor's active requests");

# Assign Body Onload javascript code
$onLoadJS='';
$smarty->assign('sOnLoadJs',$onLoadJS);

# Collect javascript code

ob_start();
# Load the javascript code

?>
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
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" language="javascript">
<!--

	function validate() {
		return true;
	}
-->
</script>

<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages
 $smarty->assign('bShowHospitalServices',FALSE);


# Render form values
if (isset($_POST["submitted"])) {
	if ($saveok) {
		$smarty->assign('sWarning',"Pay request successfully processed...");
	}
	else
		$smarty->assign('sWarning',"Error processing request...".$cClass->sql);
}

/* Request List */

require_once($root_path.'include/care_api_classes/class_person.php');
/* Default path for fotos. Make sure that this directory exists! */
$default_photo_path=$root_path.'fotos/registration';
$photo_filename='nopic';

if(isset($pid) && ($pid!='')) {
	$person_obj=new Person($pid);

	if($data_obj=&$person_obj->getAllInfoObject()){
		$zeile=$data_obj->FetchRow();
		while(list($x,$v)=each($zeile))	$$x=$v;       
	}
}

$glob_obj->getConfig('person_%');
$glob_obj->getConfig('patient_%');

/* Check whether config foto path exists, else use default path */			
$photo_path = (is_dir($root_path.$GLOBAL_CONFIG['person_foto_path'])) ? $GLOBAL_CONFIG['person_foto_path'] : $default_photo_path;
require_once($root_path.'include/inc_photo_filename_resolve.php');


$smarty->assign('img_source',"<img $img_source>");
$smarty->assign('sFullname',$payeeInfo['name']);
$smarty->assign('sAddress',$payeeInfo['address']);
$smarty->assign('sPID', ($payeeInfo['pid'] ? $payeeInfo['pid'] : '<span style="color:red">Not Registered</span>'));
$smarty->assign('sEncounterNr',$payeeInfo['encounter_nr']);
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="seg-cashier-main.php'.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&ref='.$sRefNo.'&dept='.$sDept.'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');

$requests = $cClass->GetRequestsByPayeeInfo($payeeInfo['pid'], $payeeInfo['encounter_nr'], $payeeInfo['name']);
$rHTML = '';
if ($requests) {
	$count=0;
	while ($row = $requests->FetchRow()) {
		$class = (($count++%2)==0)?"":"wardlistrow2";
		$items = array();
		$exploded = explode("\n",$row["request_items"]);
		foreach ($exploded as $i=>$v) {
			$items[$i] = "<span style=\"color:".stringToColor($v)."\">$v</span>";
		}
		$rHTML .= "									<tr class=\"$class\">
									<td align=\"center\"><input type=\"checkbox\" name=\"reference[]\" id=\"ref".$row['source_dept'].$row['reference_no']."\" value=\"".$row['source_dept']."_".$row['reference_no']."\" checked=\"checked\"/></td>
									<td align=\"center\" nowrap=\"nowrap\">".date("M d",strtotime($row['request_date']))."&nbsp;</td>
									<td align=\"center\">".$row['source_dept']."&nbsp;</td>
									<td>".$row['reference_no']."&nbsp;</td>
									<td style=\"font-size:9px;font-weight:bold\">".implode(", ",$items)."&nbsp;</td>
								</tr>\n";
	}
	if (!$rHTML) 
		$rHTML .= "									<tr>
									<td colspan=\"5\">".$cClass->sql."&nbsp;</td>
								</tr>\n";		
}
else {
	// Database error
		$rHTML .= "									<tr>
									<td colspan=\"5\">".$cClass->sql."&nbsp;</td>
								</tr>\n";
}
$smarty->assign('sRequestRows',$rHTML);

ob_start();
$sTemp='';

?>
  <input type="hidden" name="refno" value="<?php echo $sRefNo?>">
  <input type="hidden" name="dept" value="<?php echo $sDept?>">
  <input type="hidden" name="sid" value="<?php echo $sid?>">
  <input type="hidden" name="lang" value="<?php echo $lang?>">
  <input type="hidden" name="cat" value="<?php echo $cat?>">
  <input type="hidden" name="userck" value="<?php echo $userck?>">  
  <input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
  <input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
  <input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
  <input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
  <input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
  <input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
	<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';	
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sContinueButton','<img class="segSimulatedLink" src="'.$root_path.'images/btn_submitorder.gif" align="absmiddle" alt="Submit" onclick="document.forms[0].submit()"/>');
$smarty->assign('sBreakButton','<img class="segSimulatedLink" src="'.$root_path.'images/btn_cancelorder.gif" alt="'.$LDBack2Menu.'" align="absmiddle" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;">');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','cashier/cashier_payee_Requests.tpl');
$smarty->display('common/mainframe.tpl');

?>