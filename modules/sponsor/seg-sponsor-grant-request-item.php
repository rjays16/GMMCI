<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/pharmacy/ajax/order-tray.common.php");
require($root_path.'include/inc_environment_global.php');
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
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
include_once($root_path."include/care_api_classes/sponsor/class_grant_account.php");
$ac = new SegGrantAccount();
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

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title $LDPharmaDb $LDSearch");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title $LDPharmaDb $LDSearch");

 # Assign Body Onload javascript code
  $onLoadJS="onload=\"init();\"";
  $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="", lastSearchPage=-1;

function init() {
   shortcut.add('ESC', closeMe,
    {
      'type':'keydown',
      'propagate':false,
    }
  );
  
  setTimeout("$('search').focus()",100);
}

function closeMe() {
  window.parent.cClick();
}

function prepareAddEx() {
  var prod = document.getElementsByName('prod[]');
  var qty = document.getElementsByName('qty[]');
  var prcCash = document.getElementsByName('prcCash[]');
  var prcCharge = document.getElementsByName('prcCharge[]');
  var nm = document.getElementsByName('pname[]');
  
  var details = new Object();
  var list = window.opener.document.getElementById('order-list');
  var result=false;
  var msg = "";
  for (var i=0;i<prod.length;i++) {
    result = false;
    if (prod[i].checked) {
      details.id = prod[i].value;
      details.name = nm[i].value;
      details.qty = qty[i].value;
      details.prcCash = prcCash[i].value;
      details.prcCharge = prcCharge[i].value;
      result = window.opener.appendOrder(list,details);
      msg += "     x" + qty[i].value + " " + nm[i].value + "\n";
      qty[i].value = 0;
      prod[i].checked = false;
    }
  }
  window.opener.refreshTotal();
  if (msg)
    msg = "The following items were added to the order tray:\n" + msg;
  else
    msg = "An error has occurred! The selected items were not added...";  
  alert(msg);
}

function startAJAXSearch(searchID, page) {
  var searchEL = $(searchID);
  if (!page) page = 0;

  var last_page;
  /*
  if (window.parent.$('area')) {
    areaSelected = window.parent.$('area').options[window.parent.$('area').selectedIndex].value;
  }
  */
  var areaSelected = "<?= $_GET['area'] ?>";
  var discountID = <?= $_GET['d'] ? ("'".$_GET['d']."'") : "null" ?>; 
  //if (window.parent.$('discountid')) discountID=window.parent.$('discountid').value;
  // if (searchEL && (lastSearch!=searchEL.value || lastSearchPage!=page)) {
  if (true) {
    searchEL.style.color = "#0000ff";
    if (AJAXTimerID) clearTimeout(AJAXTimerID);
    $("ajax-loading").style.visibility = "";
    var script = "xajax_populateProductList('"+searchID+"',"+page+",'"+searchEL.value+"'" +
      ",'"+discountID+"'" +
      ",'"+areaSelected+"'"+
      ", "+disableInput+")";
    AJAXTimerID = setTimeout(script,200);
    lastSearch = searchEL.value;
    lastSearchPage = page;
  }
}

function endAJAXSearch(searchID) {
  var searchEL = $(searchID);
  if (searchEL) {
    $("ajax-loading").style.visibility = "hidden";
    searchEL.style.color = "";
  }
}

// -->
</script>
<link rel="stylesheet" type="text/css" media="all" href="css/style.css"/>
<script type="text/javascript" src="<?=$root_path?>modules/sponsor/js/grant-item.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>  
  <div id="content_wrapper">
    <div id="item_details" style="width:100%">
      <table width="100%" cellspacing="2" cellpadding="2" style="">
        <tr>
          <td class="segPanelHeader" colspan="4">Item details</td>
        </tr>
        <tr>
          <td class="segPanel" width="20%">Cost center</td>
          <td class="segPanel3" width="30%"></td>
          <td class="segPanel" width="20%">Item</td>
          <td class="segPanel3" width="*"></td>
        </tr>
        <tr>
          <td class="segPanel">Reference</td>
          <td class="segPanel3"></td>
          <td class="segPanel">Quantity</td>
          <td class="segPanel3"></td>
        </tr>
        <tr>
          <td class="segPanel">Request date</td>
          <td class="segPanel3"></td>
          <td class="segPanel">Price (original)</td>
          <td class="segPanel3"></td>
        </tr>
        <tr>
          <td class="segPanel">Cost center</td>
          <td class="segPanel3"></td>
          <td class="segPanel">Price (discount)</td>
          <td class="segPanel3"></td>
        </tr>
      </table>
    </div>
    <div id="controls" style="margin:2px; text-align:left">
      <select class="segInput">
        <option>-- Select an account --</option>
<?php
          $accounts = $ac->getAccountsByPrototype(array('fund', 'corporate'));
          if ($accounts) {
            $prototype_str = array(
              'payable' => 'Payable accounts',
              'promissory' => 'Promissory accounts',
              'guarantor' => 'Guarantor accounts',
              'corporate' => 'Corporate accounts',
              'deposit' => 'Partial payments',
              'fund' => 'Funds accounts'
            );
            $account_prototypes = array();
            while ($row=$accounts->FetchRow()) {
              if (!is_array($account_prototypes[ $row['prototype'] ])) $account_prototypes[ $row['prototype'] ] = array();
              $account_prototypes[ $row['prototype'] ][] = array( $row['account_id'], $row['account_name'] );
            }
            
            $options_html = "";
            foreach ($account_prototypes as $i=>$ap) {
              if ($ap) {
                $options_html .= "              <optgroup label=\"{$prototype_str[$i]}\">\n";
                foreach ($ap as $v)
                  $options_html .= "                <option value=\"{$v[0]}\">{$v[1]}</option>\n";
                $options_html .= "              </optgroup>\n";
              }
            }
            echo $options_html;
          }
          else die($db->ErrorMsg()."\n".$ac->sql)
?>

      </select>
      <img <?= createLDImgSrc($root_path, 'add_grant.gif', 0, 'absmiddle') ?> class="link" />
    </div>
    <div id="grants" style="">
      <table id="grants_list" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
        <thead>
          <tr>
            <th width="40%">Account name</th>
            <th width="20%">Grant date</th>
            <th width="20%">Grant amount</th>            
            <th width="*"></th>
          </tr>
        </thead>        
        <tbody>
          <tr>
            <td colspan="4">No grants added yet...</td>
          </tr>
        </tbody>
      </table>
    </div>
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

?>

<form action="<?php echo $breakfile?>" method="post">
  <input type="hidden" name="sid" value="<?php echo $sid ?>">
  <input type="hidden" name="lang" value="<?php echo $lang ?>">
  <input type="hidden" name="userck" value="<?php echo $userck ?>">
</form>
<?php if ($from=="multiple")
echo '
<form name=backbut onSubmit="return false">
<input type="hidden" name="sid" value="'.$sid.'">
<input type="hidden" name="lang" value="'.$lang.'">
<input type="hidden" name="userck" value="'.$userck.'">
</form>
';
?>
</div>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
