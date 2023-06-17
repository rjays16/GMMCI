<?php

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
  $sc = new SegSponsor();
  global $db;

  $Nr = $_GET['nr'];
  $is_refund = ($_GET['refund'] != "no");

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
<script type="text/javascript" src="js/sponsor.js?t=<?=time()?>"></script>
<script type="text/javascript" language="javascript">
<!--

  var glst, grid, buffer, 
    isLoading=false,
    iSrc='', iNr='', iCode='', iArea='';

  // callback triggered when Patient Search window is closed
  function pSearchClose() {
    $('select-enc').className = ($('pid').value == '') ? 'segSimulatedLink' : 'segDisabledLink';
    
    $('rqsearch').show();
    search();
    cClick();
  }
  
  function search() {
    var o = new Object();
    if (!$('pid').value) return false;
    o['pid'] = $('pid').value;
    
    if (typeof(rlst)=='object') {
      rlst.fetcherParams = o;
      rlst.reload();
    }
    
    hlst.fetcherParams = o;
    hlst.reload();
    
    dlst.clear();
    dlst.add(null);
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
    "var_name"=>"patientname",
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
  
  function openWizard(nr) {
    if ($('autocompute').hasClassName('disabled')) return false;
    overlib(
        OLiframeContent('<?= $root_path ?>modules/sponsor/seg-sponsor-recompute.php?nr='+nr,
        620, 340, 'fWizard', 0, 'auto'),
        WIDTH,500, TEXTPADDING,0, BORDER,0,
        STICKY, SCROLL, CLOSECLICK, MODAL,
        CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
        CAPTIONPADDING,2, 
        CAPTION,'Autocompute payment',
        MIDX,0, MIDY,0, 
        STATUS,'Autocompute payment');
    return false;
  }
  
  function grantItem(src, nr, item) {
    if (typeof(src)=='undefined' || typeof(nr)=='undefined' || typeof(item)=='undefined') return false;
    overlib(
      OLiframeContent('<?= $root_path ?>modules/sponsor/seg-sponsor-grant-request-item.php?src='+src+'&nr='+nr+'&item='+item,
      620, 340, 'fGrant', 0, 'auto'),
      WIDTH,500, TEXTPADDING,0, BORDER,0,
      STICKY, SCROLL, CLOSECLICK, MODAL,
      CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
      CAPTIONPADDING,2, 
      CAPTION,'Grant request item',
      MIDX,0, MIDY,0, 
      STATUS,'Grant request item');
    return false;
  }
  
  function openGrant(src, nr, code, area, total) {
    if (!code) code='';
    if (!area) area='';
    iSrc = src; iNr = nr; iCode = code; iArea = area;
    return OLgetAJAX('<?= $root_path ?>modules/sponsor/ajax/grant.php<?= URL_APPEND ?>&userck=<?= $userck ?>&src='+src+'&nr='+nr+'&code='+code+'&area='+area+'&total='+total, OLcmdExT1, 300, 'ovfl1');
  }
  
  function OLcmdExT1() {
    overlib(
        OLresponseAJAX,
        WIDTH,560, HEIGHT,360, TEXTPADDING,0, BORDER,0,
        STICKY, SCROLL, CLOSECLICK, MODAL,
        CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
        CAPTIONPADDING,2, 
        CAPTION,'Edit grant',
        MIDX,0, MIDY,0, 
        STATUS, 'Edit grant');

    glst = new LGList('glst');
    glst.ajaxFetcher='populateGrants';
    glst.sortOrder=[0,1,0,0,null];
    glst.fetcherParams={ 'src':iSrc,'nr':iNr,'code':iCode,'area':iArea};
    glst.maxRows='5';
    glst.emptyMessage='No grants found for this item...';
    glst.columnCount='4';
    glst.add=addGrant;
    glst.reload();

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

$hlst = &$listgen->createList('hlst',array('Reference','Date','Status',''),
  array(LG_SORT_NONE,LG_SORT_DESC,LG_SORT_UNSORTABLE,LG_SORT_UNSORTABLE),
  'populatePatientBillingAccounts');
$hlst->initialMessage = "Please select a patient first...";
$hlst->addMethod = 'addPatientBillAccount';
$hlst->fetcherParams = array();
$hlst->columnWidths = array("25%", "25%", "25%", "25%");
$smarty->assign('lstBillingAccounts',$hlst->getHTML());

/*
$rlst = &$listgen->createList(
  array(
    'LIST_ID' => 'rlst',
    'COLUMN_HEADERS' => array('Date','Source','Reference','Item name','Quantity','Total due','Discounted','Status',''),
    'COLUMN_SORTING' => array(LG_SORT_DESC, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_UNSORTABLE, LG_SORT_UNSORTABLE),
    'AJAX_FETCHER' => 'populatePatientRequestList',
    'INITIAL_MESSAGE' => "Please select a patient first...",
    'ADD_METHOD' => 'addPatientRequest',
    'FETCHER_PARAMS' => array(),
    'COLUMN_WIDTHS' => array("10%", "7%", "7%", "16%", "6%", "8%", "8%", "13%", "5%")
  )
);
$smarty->assign('lstRequest',$rlst->getHTML());
*/

$blst = &$listgen->createList('blst',array('Bill area','Total payable','Status',''),array(NULL,NULL,NULL,NULL),'populateBillingBreakdown');
$blst->initialMessage = "No items found...";
$blst->addMethod = 'addBillingBreakdownItem';
$blst->fetcherParams = array();
$blst->columnWidths = array("52%", "25%", "15%", "8%");
$smarty->assign('lstBillAreas',$blst->getHTML());

$dlst = &$listgen->createList('dlst',array('Account','Total','Grants','Status',''),array(1,0,0,NULL,NULL),'populateBreakdownDetails');
$dlst->initialMessage = "List is currently empty...";
$dlst->addMethod = 'addBreakdownDetail';
$dlst->fetcherParams = array();
$dlst->columnWidths = array("37%", "20%", "25%", "10%", "8%");
$smarty->assign('lstDetails',$dlst->getHTML());


$balst = &$listgen->createList('balst',array('Account','Amount','Status',''),array(NULL,NULL,NULL,NULL),'populateBillGrantAccounts');
$balst->initialMessage = "No billing payments assigned yet...";
$balst->addMethod = 'addBillGrantAccount';
$balst->fetcherParams = array();
$balst->columnWidths = array("50%", "20%", "20%", "10%");
$smarty->assign('lstBillingGrantAccounts',$balst->getHTML());

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$title = "Cashier::Grants";

# Title in the title bar 
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

# Render form values
if ($is_refund) {
  $smarty->assign('sRefundAmount','<input class="segInput" type="text" id="refund_amount" name="refund_amount" size="15" readonly="readonly" style="text-align:right" value="'.$_POST['refund_amount'].'"/>');
  $chk_adjust = ($_POST['refund_amount_fixed'] && $_POST['refund_amount']!=$_POST['refund_amount_fixed']);
  $smarty->assign('sCheckAdjust', '<input type="checkbox" id="chk_adjust" name="chk_adjust" class="segInput" value="1" onclick="$(\'refund_amount_fixed\').disabled=!this.checked" '.( $chk_adjust ? 'checked="checked"' : '' ).'/><label class="segnput" for="chk_adjust">Adjust amount</label>');
  $smarty->assign('sAdjustAmount','<input class="segInput" type="text" id="refund_amount_fixed" name="refund_amount_fixed" size="15" style="text-align:right"'.($chk_adjust ? '' : ' disabled="disabled"').' value="'.$_POST['refund_amount_fixed'].'"/>');
}
else {
  $smarty->assign('sRefundAmount','<input class="segInput" type="text" id="refund_amount" name="refund_amount" size="15" readonly="readonly" value="'.$_POST['refund_amount'].'" disabled="disabled" style="text-align:right;visibility:hidden" />');
  $smarty->assign('sCheckAdjust', '<input type="checkbox" id="chk_adjust" name="chk_adjust" class="segInput" value="1" onclick="$(\'refund_amount_fixed\').disabled=!this.checked" '.( $chk_adjust ? 'checked="checked"' : '' ).' disabled="disabled" style="visibility:hidden"/><label class="segInput" for="chk_adjust" style="visibility:hidden">Adjust amount</label>');
  $smarty->assign('sAdjustAmount','<input class="segInput" type="text" id="refund_amount_fixed" name="refund_amount_fixed" size="15" disabled="disabled" value="'.$_POST['refund_amount_fixed'].'" style="text-align:right;visibility:hidden"/>');
}

$smarty->assign('sSelectEnc','<img id="select-enc" class="segSimulatedLink" src="../../images/btn_encounter_small.gif" border="0" onclick="openPatientSelect()" />');
$smarty->assign('sPatientEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');  
$smarty->assign('sPatientID','<input id="pid" name="pid" class="segInput" type="text" value="'.$_POST["pid"].'" readonly="readonly"/>');
$smarty->assign('sPatientName','<input class="segInput" id="patientname" name="patientname" type="text" size="30" style="font:bold 12px Arial;" readonly="readonly" value="'.$_POST["ordername"].'"/>');
$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="New search" disabled="disabled" onclick="if (confirm(\'Search for another patient?\')) resetControls()"/>');
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


$smarty->assign('sDateStart','
  <input class="segInput" name="from" id="from" type="text" size="8" value=""/>
  <img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_from" align="absmiddle" style="cursor:pointer;"  />
  <script type="text/javascript">
    Calendar.setup ({
      inputField : "from", ifFormat : "'. $phpfd .'", showsTime : false, button : "tg_from", singleClick : true, step : 1
    });
  </script>
');
$smarty->assign('sDateEnd','
  <input class="segInput" name="to" id="to" type="text" size="8" value=""/>
  <img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_to" align="absmiddle" style="cursor:pointer;"  />
  <script type="text/javascript">
    Calendar.setup ({
      inputField : "to", ifFormat : "'. $phpfd .'", showsTime : false, button : "tg_to", singleClick : true, step : 1
    });
  </script>
');



/*
$smarty->assign('sReturnDate','<span id="show_return_date" class="segInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.$curDate_show.'</span><input class="segInput" name="return_date" id="return_date" type="hidden" value="'.$curDate.'" style="font:bold 12px Arial">');
if ($view_only) {
  $smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="return_date_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;opacity:0.5">');
}
else {
  $smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="return_date_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
  $jsCalScript = "<script type=\"text/javascript\">
    Calendar.setup ({
      displayArea : \"show_return_date\",
      inputField : \"return_date\",
      ifFormat : \"%Y-%m-%d %H:%M\", 
      daFormat : \"  %B %e, %Y %I:%M%P\", 
      showsTime : true, 
      button : \"return_date_trigger\", 
      singleClick : true,
      step : 1
    });
  </script>";
  $smarty->assign('jsCalendarSetup', $jsCalScript);  
}

$smarty->assign('sComments','<textarea class="segInput" name="comments" cols="26" rows="2" style="float:left; margin-left:5px; font-size:12px;">'.$_POST['comments'].'</textarea>');
$smarty->assign('sReturnItems',"<tr><td colspan=\"10\">Item list is currently empty...</td></tr>");
*/

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&nr='.$Nr.'&from='.$_GET['from'].'&refund='.$_GET['refund'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
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
$smarty->assign('sMainBlockIncludeFile','sponsor/grant.tpl');
$smarty->display('common/mainframe.tpl');

