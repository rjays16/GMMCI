<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
0* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/

define('LANG_FILE','products.php');
$local_user=$_GET['userck'];

require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen("../../");

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$php_date_format = strtolower($date_format);
$php_date_format = str_replace("dd","d",$php_date_format);
$php_date_format = str_replace("mm","m",$php_date_format);
$php_date_format = str_replace("yyyy","Y",$php_date_format);
$php_date_format = str_replace("yy","y",$php_date_format);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 
global $db;
 
$Source = $_GET['src'];
$Nr = $_GET['nr'];
$Code = $_GET['code'];
$Area = $_GET['area'];
$Total = $_GET['total'];

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

$type_str = array(
  'FB'=>'Billing',
  'PH'=>'Medicine/Supplies',
  'LD'=>'Laboratory',
  'RD'=>'Radiology',
  'OR'=>'Operating Room'
);

$area_str = array(
  'acc'=>'Room & Accommodation',
  'med'=>'Drugs & Medicines',
  'srv'=>'X-Ray, Lab & Other Charges',
  'ops'=>'Operating/Delivery Room',
  'doc'=>'Doctor\'s Fees',
  'msc'=>'Miscellaneous Charges'
);

include_once($root_path."include/care_api_classes/sponsor/class_sponsor.php");
$sc = new SegSponsor();
$info = $sc->getGrantInfo($Source, $Nr, $Code);

$account=$db->GetOne('SELECT name_long FROM seg_cashier_account_subtypes WHERE type_id='.$db->qstr($Code));

include_once($root_path."include/care_api_classes/sponsor/class_grant_account.php");
$ac = new SegGrantAccount();

include_once($root_path."include/care_api_classes/sponsor/class_grant.php");
$gc = new SegGrant();
$grant = $gc->getTotalGrant($Source, $Nr, $Code, $Area);

# Setup dyynamic lists
$listgen->setListSettings('MAX_ROWS','10');
$listgen->setListSettings('RELOAD_ONLOAD', TRUE);

$glst = &$listgen->createList('glst',array('Date','Account','Amount','Encoder',''),array(0,1,0,0,NULL),'populateGrants');
$glst->initialMessage = "No grants found for this item...";
$glst->addMethod = 'addGrant';
$glst->fetcherParams = array('src'=>$Source,'nr'=>$Nr, 'code'=>$Code, 'area'=>$Area);
$glst->columnWidths = array("25%","35%", "15%", "15%", "5%");

?>

<table border="0" cellspacing="2" cellpadding="2" align="center" width="100%;margin:4px">
  <tr>
    <td class="segPanelHeader">Item details</td>
  </tr>
  <tr>
    <td class="segPanel" align="left" valign="top">
      <table width="95%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Arial" >
        <tr>
          <td width="50" align="right" valign="middle"><strong>Type</strong></td>
          <td width="50%" valign="middle">
            <input class="segClearInput" type="text" disabled="disabled" style="font:bold 12px Tahoma;color:#800000;width:99%" value="<?= $type_str[strtoupper($Source)] . ($Source=='FB' ?  "::".$area_str[strtolower($Area)] : '') ?>">
          </td>
          <td width="50" align="right" valign="middle" nowrap="nowrap"><strong style="white-space:nowrap;width:99%">Item name</strong></td>
          <td width="*" valign="middle">
            <input class="segClearInput" type="text" disabled="disabled" style="font:bold 12px Tahoma;width:99%" value="<?= $account ? $account : 'N/A' ?>">
          </td>
        </tr>
        <tr>
          <td align="right" valign="middle" nowrap="nowrap"><strong>Total</strong></td>
          <td valign="middle">
            <input id="grant-total" class="segClearInput" type="text" disabled="disabled" style="font:bold 14px Arial; color:#000060; width:99%" value="<?= number_format($Total,2) ?>">
          </td>
          <td align="right" valign="middle" nowrap="nowrap"><strong>Total grant</strong></td>
          <td valign="middle">
            <input id="grant-payable" class="segClearInput" type="text" disabled="disabled" style="font:bold 14px Arial; color:#006000; width:99%" value="<?= number_format((float)$Total-(float)$grant,2) ?>">
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="segPanelHeader">Add grant</td>
  </tr>
  <tr>
    <td class="segPanel" align="left" valign="top">
      <table width="95%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Arial" >
        <tr>
          <td width="70" align="right" valign="middle" nowrap="nowrap"><strong>Account</strong></td>
          <td width="45%" valign="middle">
            <select id="grant-account" class="segInput" style="width:99%">
              <optgroup label="Personal accounts">
<?php
  $accounts = $ac->getAccounts();
  while ($row=$accounts->FetchRow()) {
    if ($row['is_personal']==='0' && !$show_group2) {
?>            </optgroup>
              <optgroup label="External accounts">
              
<?php
      $show_group2 = TRUE;
    }    
?>
                <option value="<?= $row['account_id'] ?>"><?= $row['account_name'] ?></option>
<?php
  }
?>
              </optgroup>
            </select>
          </td>
          <td width="50" align="right" valign="middle" nowrap="nowrap"><strong>Amount</strong></td>
          <td width="20%" valign="middle">
            <input id="grant-amount" class="segInput" type="text" value="0.00" style="text-align:right; width:99%; font-size:14px" onfocus="this.select()" onblur="if ( parseFloatEx(this.value) > parseFloatEx($('grant-payable').value) ) { this.value = formatNumber($('grant-payable').value,2) } else { this.value = formatNumber(parseFloatEx(this.value),2) }" />
          </td>
          <td><input type="button" class="segButton" value="Add" onclick="clickGrant()" /></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
<?= $glst->getHTML() ?>
    </td>
  </tr>
</table>