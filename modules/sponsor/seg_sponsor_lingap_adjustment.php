<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/sponsor/ajax/lingap_adjustment.common.php");


/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
0* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');

$local_user='ck_grants_user';
require_once($root_path.'include/inc_front_chain_lang.php');

# Create products object
$GLOBAL_CONFIG=array();

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

# $phpfd = config date format in PHP date() specification

if (!$_GET['from'])
  $breakfile=$root_path."modules/sponsor/seg-sponsor-functions.php".URL_APPEND;
else {
  if ($_GET['from']=='CLOSE_WINDOW')
    $breakfile = "javascript:window.parent.cClick();";
  else
    $breakfile = $root_path.'modules/cashier/seg-cashier-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}
$thisfile='seg-sponsor-cmap-patient.php';


//LISTGEN YEHEY
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/sponsor/class_sponsor.php");
include_once($root_path."include/care_api_classes/sponsor/class_lingap.php");
$sc = new SegSponsor();
$lc = new SegLingap();
global $db;

$Nr = $_GET['nr'];

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Assign Body Onload javascript code
/*
if ($view_only) {     
  $onLoadJS='onload="eraseCookie(\'__ret_ck\');'.($Nr ? 'xajax_populate_items(\''.$Nr.'\',1)' : '').'"';
}
else {
 $onLoadJS='onload="eraseCookie(\'__ret_ck\');'.($Nr ? 'xajax_populate_items(\''.$Nr.'\')' : '').'"';
}
*/
$smarty->assign('sOnLoadJs',$onLoadJS);

if (isset($_POST["submitted"]) && !$_REQUEST['viewonly']) {
  $data = array(
    'control_nr'=>$_POST['control_nr'],
    'encounter_nr'=>$_POST['encounter_nr'],
    'pid'=>$_POST['pid'],
    'name'=>$_POST['name'],
    'entry_date'=>$_POST['entry_date'],
    'remarks'=>$_POST['remarks'],
    'modify_id'=>$_SESSION['sess_temp_userid']
  );
  
  $lc->setDataArray($data);
  $db->StartTrans();
  
  if ($_POST['mode']=='edit') {
    $data["history"]=$lc->ConcatHistory("Update: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n");
    $lc->setDataArray($data);
    $lc->where = "control_nr=".$db->qstr($_POST['control_nr']);
    $saveok=$lc->updateDataFromInternalArray($_POST['control_nr'],FALSE);
  }
  else {
    $data['create_id']=$_SESSION['sess_temp_userid'];
    $data['history']="Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n";
    $lc->setDataArray($data);
    $saveok = $lc->insertDataFromInternalArray();
  }
  
  if ($saveok) {
    # Bulk write entry items
    $bulk = array();
    foreach ($_POST["item"] as $i=>$v) {
      $bulk[] = array(
        $_POST["src"][$i],
        $_POST["ref"][$i],
        $_POST["item"][$i],
        $_POST["service"][$i],
        $_POST['amount'][$i],
      );
    }
    $saveok=$lc->clearEntry($_POST['control_nr']);
    if ($saveok) $saveok=$lc->addDetails($_POST['control_nr'], $bulk);
  }
  
  if ($saveok) {
    $smarty->assign('sysInfoMessage','<div style="margin:6px">Lingap entry successfully saved!</div>');
  }
  else {
    $db->FailTrans();
    $errorMsg = $db->ErrorMsg();
    if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
      $smarty->assign('sysErrorMessage','<br><strong>Error:</strong>An entry with the same control number already exists in the database.');
    else {
      if ($errorMsg)
        $smarty->assign('sysErrorMessage',"<br><strong>Error:</strong> $errorMsg");
      else
        $smarty->assign('sysErrorMessage',"<br><strong>Unable to save Lingap entry...</strong>");
      #print_r($order_obj->sql);
    }
  }
  $db->CompleteTrans();
}


# Collect javascript code
ob_start();
   # Load the javascript code
?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/lingap_adjustment.js?t=<?=time()?>"></script>
<script type="text/javascript" language="javascript">
<!--

  var glst, grid, buffer, 
    isLoading=false

  // callback triggered when Patient Search window is closed
  function pSearchClose() {
    if ($('pid').value) {
      xajax.call('updateBalance', { parameters:[$('pid').value]});
      $('select-enc').removeClassName('link').addClassName('disabled');
      $('adjust-balance').addClassName('link').removeClassName('disabled');
    }
    else {
      $('select-enc').addClassName('link').removeClassName('disabled');
      $('adjust-balance').removeClassName('link').addClassName('disabled');
    }
    refreashLists();
    cClick();
  }
  
  function refreashLists() {
    var o = new Object();
    if (!$('pid').value) {
      flst.clear();
      rlst.clear();
    }
    else {
      o['pid'] = $('pid').value;
      xajax.call('updateBalance', { parameters: [o['pid']] } );
      if (typeof(alst)=='object') {
        alst.fetcherParams = o;
        alst.reload();
      }
    }
  }
  
  function startLoading() {
    if (!isLoading) {
      isLoading = 1;
      return overlib('<strong>Loading items...</strong><br/><img src="../../images/ajax_bar.gif"/>',
        WIDTH,300, TEXTPADDING,5, BORDER,0,
        STICKY, SCROLL, CLOSECLICK, MODAL,
        NOCLOSE, TIMEOUT, 10000, OFFDELAY, 10000,
        CAPTION,'Loading', 
        MIDX,0, MIDY,0,
        STATUS,'Loading');
    }
  }

  function doneLoading() {
    if (isLoading) {
      setTimeout('cClick()', 500);
      isLoading = 0;
    }
  }

  function validate() {
  }
  
  function openPatientSelect() {
    if ($('select-enc').hasClassName('disabled')) return false;
<?php
  $var_arr = array(
    "var_pid"=>"pid",
    "var_encounter_nr"=>"encounter_nr",
    "var_name"=>"name",
    "var_clear"=>"clear-enc",
    "var_discount"=>"sw-class",
    "var_enctype"=>"encounter_type",
    "var_enctype_show"=>"encounter_type_show",
    "var_include_walkin"=>"0",
    "var_reg_walkin"=>"0"
  );
  $vas = array();
  foreach($var_arr as $i=>$v) {
    $vars[] = "$i=$v";
  }
  $var_qry = implode("&",$vars);
?>
    overlib(
        OLiframeContent('<?= $root_path ?>modules/registration_admission/seg-select-enc.php?<?=$var_qry?>&var_include_enc=0',
        700, 400, 'fSelEnc', 0, 'no'),
        WIDTH,700, TEXTPADDING,0, BORDER,0,
        STICKY, SCROLL, CLOSECLICK, MODAL,
        CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
        CAPTIONPADDING,2, 
        CAPTION,'Select registered person',
        MIDX,0, MIDY,0, 
        STATUS,'Select registered person');
    return false;
  }
  
  function newAdjustment() {
    if (!$('pid').value) return false;
    overlib(
      OLiframeContent('<?= $root_path ?>modules/sponsor/seg_sponsor_lingap_adjustment_edit.php?pid='+encodeURIComponent($('pid').value)+'&name='+encodeURIComponent($('name').value),
        480, 260, 'fWizard', 0, 'no'),
        WIDTH,480, TEXTPADDING,0, BORDER,0,
        STICKY, SCROLL, CLOSECLICK, MODAL,
        CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
        CAPTIONPADDING,2, 
        CAPTION,'Edit adjustment entry',
        MIDX,0, MIDY,0, 
        STATUS,'Edit adjustment entry');
    return false;
  }

-->
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$listgen->printJavascript($root_path);

# Setup dyynamic lists
$listgen->setListSettings('MAX_ROWS','10');
$listgen->setListSettings('RELOAD_ONLOAD', FALSE);

# Adjustment list
$alst = &$listgen->createList(
  array(
    'LIST_ID' => 'alst',
    'COLUMN_HEADERS' => array('Date','Amount','Encoder','Remarks','Status',''),
    'COLUMN_SORTING' => array(LG_SORT_DESC, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_UNSORTABLE, LG_SORT_UNSORTABLE),
    'AJAX_FETCHER' => 'populateAdjustments',
    'INITIAL_MESSAGE' => "Please select a patient first...",
    'ADD_METHOD' => 'addAdjustment',
    'FETCHER_PARAMS' => array(),
    'COLUMN_WIDTHS' => array('14%', '15%', '15%', '*', '10%', '4%')
  )
);
$smarty->assign('lstAdjustments',$alst->getHTML());

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$title = "Lingap :: Adjsutments";

# Title in the title bar 
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

if (!$_POST['control_nr']) {
  $_POST['control_nr'] = $lc->getNewControl();
}

$smarty->assign('sControlNo','<input id="control_nr" name="control_nr" class="segInput" type="text" value="'.$_POST["control_nr"].'" readonly="readonly"/>');
$smarty->assign('sSelectEnc','<img id="select-enc" class="link" src="../../images/btn_encounter_small.gif" border="0" onclick="openPatientSelect()" />');
$smarty->assign('sPatientEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');  
$smarty->assign('sPatientID','<input id="pid" name="pid" class="segInput" type="text" value="'.$_POST["pid"].'" readonly="readonly"/>');
$smarty->assign('sPatientName','<input class="segInput" id="name" name="name" type="text" size="30" style="font:bold 12px Arial;" readonly="readonly" value="'.$_POST["name"].'"/>');

$smarty->assign('sRunningBalance','<input class="segClearInput" id="bal" name="bal" type="text" size="10" style="margin-left:5px; font:bold 16px Arial; color:#000080; background-color:#fff; border:1px dashed #4e8ccf; text-align:right" readonly="readonly" value="0.00"/>');
$smarty->assign('sAdjustBalance','<img id="adjust-balance" class="disabled" src="../../images/btn_edit_small.gif" border="0" onclick="" />');

$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="Reset" disabled="disabled" onclick="if (confirm(\'Search for another patient?\')) resetControls()"/>');
$smarty->assign('sPatientEncType','<input id="encounter_type" name="encounter_type" type="hidden" value="'.$_POST["encounter_type"].'"/>');
$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)');
if ($_POST['encounter_type'])  $smarty->assign('sOrderEncTypeShow',$enc[$_POST['encounter_type']]);
else {
  if ($person['encounter_type'])
    $smarty->assign('sOrderEncTypeShow',$enc[$person['encounter_type']]);
  else  $smarty->assign('sOrderEncTypeShow', 'WALK-IN');
}
$smarty->assign('sSWClass',($_POST['discountid'] ? $_POST['discountid'] : 'None'));

$dbtime_format = "Y-m-d";
//$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format);

$smarty->assign('sRequestFilterDate','
  <input class="segInput" name="date_request" id="date_request" type="text" size="8" value="'.$curDate.'"/>
  <img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_date_request" align="absmiddle" class="segSimulatedLink"  />
  <script type="text/javascript">
    Calendar.setup ({
      inputField : "date_request", ifFormat : "'. $phpfd .'", showsTime : false, button : "tg_date_request", singleClick : true, step : 1
    });
  </script>
');

if ($_POST['entry_date'])
  $dEntryDate = strtotime($_POST['entry_date']);
else
  $dEntryDate = time();

$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format,$dEntryDate);
$curDate_show = date($fulltime_format,$dEntryDate);

$smarty->assign('sEntryDate',
'<span id="show_entry_date" class="segInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.
$curDate_show.'</span>
<input class="segInput" name="entry_date" id="entry_date" type="hidden" value="'.
$curDate.'" style="font:bold 12px Arial">');

if ($view_only) 
  $smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="entry_date_trigger" align="absmiddle" style="margin-left:2px;opacity:0.2">');
else {
  $smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="entry_date_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
  $jsCalScript = "<script type=\"text/javascript\">
  Calendar.setup ({
    displayArea : \"show_entry_date\",
    inputField : \"entry_date\",
    ifFormat : \"%Y-%m-%d %H:%M\", 
    daFormat : \"  %B %e, %Y %I:%M%P\", 
    showsTime : true, 
    button : \"entry_date_trigger\", 
    singleClick : true,
    step : 1
  });
  </script>";
  $smarty->assign('jsCalendarSetup', $jsCalScript);  
}

$smarty->assign('sContinueButton','<input type="image" src="'.$root_path.'images/btn_submitorder.gif" align="center">');
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>
  <input type="hidden" name="submitted" value="1" />
  <input type="hidden" name="sid" value="<?php echo $sid?>">
  <input type="hidden" name="lang" value="<?php echo $lang?>">
  <input type="hidden" name="cat" value="<?php echo $cat?>">
  <input type="hidden" name="userck" value="<?php echo $userck?>">  
  <input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
  <input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
  <input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
  <input type="hidden" name="lockflag" value= "<?php echo  $lockflag?>">
  <input type="hidden" id="refno" name="refno" value="">
  <input type="hidden" id="refsource" name="refsource" value="">

<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';  
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
  $smarty->assign('sContinueButton','<input type="image" class="segSimulatedLink" src="'.$root_path.'images/btn_submitorder.gif" align="absmiddle" alt="Submit">');
  $smarty->assign('sBreakButton','<img class="segSimulatedLink" src="'.$root_path.'images/btn_cancelorder.gif" alt="'.$LDBack2Menu.'" align="absmiddle" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;">');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','sponsor/lingap_adjustment.tpl');
$smarty->display('common/mainframe.tpl');

