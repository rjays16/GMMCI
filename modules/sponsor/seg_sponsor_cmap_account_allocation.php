<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/sponsor/ajax/cmap_allotment.common.php");

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_grants_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');


$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$title = "Auto-compute billing payment";

# Title in the title bar 
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

# Assign Body Onload javascript code
$smarty->assign('sOnLoadJs','onLoad="init()"');

include_once($root_path."include/care_api_classes/sponsor/class_grant_account.php");

# Collect javascript code
ob_start()

?>
<style type="text/css">
<!--
  
  .priorityIndicator {
    font: bold 10px Tahoma;
    background-color: #43609c;
    border-color:1px solid #768bb7;
    padding: 2px 4px;
    color: white;
    -moz-border-radius: 4px;
  }
  
  ul.sortable {
    width: 100%;
    list-style: none;
    margin: 0;
    display: block;
    border-collapse: collapse;
  }
  
  ul.sortable li {
    margin: 0;
    width: 100%;
  }

  .olbg {
    background-color: transparent;
    border: 0;
  }

  .olcg {
    background-color: transparent; 
    background-image: none;
    text-align:center;
    margin:0;
  }

  .olcgif {
    background-color: transparent;
    text-align: center;
  }

  .olfg {
    background-color: transparent;
    text-align: center;
  }
  
  .olfgif {
    background-color: none; 
    text-align: center;
  }
  
  .olcap {
    display: none;
    font-family:Tahoma; 
    font-size:11px; 
    font-weight:bold; 
    color:white;
    margin-top:0px;
    margin-bottom:1px;
  }
  
  a.olclo {
    display: none;
    font-family:Verdana;
    font-size:11px;
    font-weight:bold;
    color:#ddddff;
  }

  .olText {
    font:bold 11px Tahoma;
    color:#2d2d2d;
  }

  
-->
</style>  
<script language="javascript" >
<!--
var AJAXTimerID=0;
var isLoading=false;

function init() {
}

function save() {
  if (validate()) {
    startLoading();
    xajax.call( 'save', { parameters:[xajax.getFormValues('adjustment-data')] } );
  }
}

function validate() {
  return true;
}

function startLoading() {
  if (!isLoading) {
    isLoading = 1;
    return overlib('<img src="../../images/loading6.gif"/>',
      WIDTH,300, TEXTPADDING,5, BORDER,0,
      SHADOW, 0,
      MODALCOLOR, '#ffffff',
      MODALOPACITY, 80,
      STICKY, MODAL,
      NOCLOSE, TIMEOUT, 0, OFFDELAY, 0,
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

function parseFloatEx(x) {
  var str = x.toString().replace(/\,|\s/,'')
  return parseFloat(str)
}

function formatNumber(num,dec) {
  var nf = new NumberFormat(num);
  if (isNaN(dec)) dec = nf.NO_ROUNDING;
  nf.setPlaces(dec);
  return nf.toFormatted();
}

function tooltip (text) {
  return overlib(text,WRAP,0,HAUTO,VAUTO, BGCLASS,'olTooltipBG', FGCLASS,'olTooltipFG', TEXTFONTCLASS,'olTooltipTxt', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);
}

function enterAmount() {
  var amt;
  while (isNaN(amt)) {
    amt = prompt('Enter amount to be transferred:');
    if (amt===null) return false;
  }
  
  $('amount').value = amt;
  $('show_amount').value = formatNumber(amt,2);
}

// -->
</script> 
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<?php

$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Entry date
$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
if ($_POST['return_date']) {
  $dStockDate = strtotime($_POST['entry_date']);
  $curDate = date($dbtime_format,$dStockDate);
  $curDate_show = date($fulltime_format,$dStockDate);
}
else {
  $curDate = date($dbtime_format,time());
  $curDate_show = date($fulltime_format,time());
}

# Buffer page output
ob_start();

?>
  <form id="adjustment-data">
  <div style="width:98%; padding:5px 0px">
    <table border="0" cellspacing="0" cellpadding="2" width="99%" align="center" style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
      <tbody>
        <tr>
          <td class="segPanel" align="right" valign="middle" width="18%"><strong>Entry date</strong></td>
          <td class="segPanel2" align="left" valign="middle" width="30%" nowrap="nowrap">
            <span id="show_entry_date" class="segInput" style="font-weight:bold; color:#000080; padding:1px; width:200px; height:24px"><?= $curDate_show ?></span>
            <input name="entry_date" id="entry_date" type="hidden" value="<?= $curDate ?>">
            <img <?= createComIcon($root_path,'show-calendar.gif','0') ?> id="entry_date_trigger" class="link" align="absmiddle" style="">
            <script type="text/javascript">
              Calendar.setup ({
                displayArea : "show_entry_date",
                inputField : "entry_date",
                ifFormat : "%Y-%m-%d %H:%M", 
                daFormat : "%B %e, %Y %I:%M%P", 
                showsTime : true, 
                button : "entry_date_trigger", 
                singleClick : true,
                step : 1
              });
            </script>
            
            <input id="" name="account_nr" class="segInput" type="hidden" value="<?= $_REQUEST['nr'] ?>">
          </td>
          <td class="segPanel2" align="left" valign="middle" width="*" style="">
            <strong>Date of this transfer</strong>
          </td>
        </tr>
<!--        <tr>
          <td class="segPanel" align="right" valign="middle" width="18%"><strong>Control No.</strong></td>
          <td class="segPanel2" align="left" valign="middle" width="30%" style="">
            <input id="control_nr" name="control_nr" class="segInput" type="text" size="15" value="" >
          </td>
          <td class="segPanel2" align="left" valign="middle" width="*" style="">
            <strong>Control no. for this transfer</strong>
          </td>
        </tr>-->
        <tr>
          <td class="segPanel" align="right" valign="middle"><strong>Amount</strong></td>
          <td class="segPanel2" align="left" valign="middle" style="border-right:0" nowrap="">
            <input id="show_amount" class="segInput" type="text" value="0.00" size="15" readonly="readonly" style="text-align:right; font:bold 12px Tahoma" />
            <input id="amount" name="amount" type="hidden" value="0.00" />
            <input class="segButton" type="button" value="Set" onclick="enterAmount()" />
          </td>
          <td class="segPanel2" align="left" valign="middle" style="border-left:0">
            <strong>Amount to be transferred</strong>
          </td>
        </tr>
        <tr>
          <td class="segPanel" align="right" valign="middle">
            <strong>Remarks</strong>
          </td>
          <td class="segPanel2" align="left" valign="middle" style="border-right:0">
            <textarea class="segInput" id="remarks" name="remarks" rows="2" cols="23"></textarea>
          </td>
          <td class="segPanel2" align="left" valign="middle" style="border-left:0">
            <strong>Additional notes/comments</strong>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  </form>
  <div style="width:99%;padding:2px; padding-left:10px">
    <input class="segButton" type="button" value="Save" onclick="save()"/>
    <input class="segButton" type="button" value="Close" onclick="parent.cClick()"/>
  </div>
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">

<?php
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

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
