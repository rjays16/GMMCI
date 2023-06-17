<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

/**
* SegHIS Integrated Hospital Information System
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
 
define(AC_DESC, 'Accommodation');
define(MD_DESC, 'Drugs and Medicines');
define(HS_DESC, 'X-Ray/ Lab/ Others');
define(OP_DESC, 'Operating Room/ DR');
define(D1_DESC, 'General Practitioner');
define(D2_DESC, 'Specialist');
define(D3_DESC, 'Surgeon');
define(D4_DESC, 'Anesthesiologist');
define(XC_DESC, 'Miscellaneous');
 
$EncounterNr = $_GET['nr'];
$BillingNr = $_GET['bnr'];
$bill_date = (isset($_GET['billdt'])) ? strftime("%Y-%m-%d %H:%M:%S", $_GET['billdt']) : strftime("%Y-%m-%d %H:%M:%S");

if ($BillingNr) $NR = $BillingNr;
else $NR = "T".$EncounterNr;
 
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/billing/class_billing.php");

if ($BillingNr)
  $bc = new Billing($EncounterNr, $bill_date,'0000-00-00 00:00:00',$BillingNr);
else {
  $bc = new Billing($EncounterNr, $bill_date);
  if ($_GET['force'] == '1') $bc->forceEncounterStartDte();  
}

$title = "Billable Areas";

global $db;

$bc->getConfinementType();
$bc->getAccommodationHist();
$bc->getRoomTypeBenefits();
$bc->getProfFeesBenefits();

$bill_areas = array();
if (($ac_chrg = $bc->compTotalAccommodationChrg()) > 0) {
    $bill_areas[] = array('AC', AC_DESC, $ac_chrg, 1);
}
if (($md_chrg = $bc->getTotalMedCharge()) > 0) {
    $bill_areas[] = array('MS', MD_DESC, $md_chrg, 2);
}
if (($hs_chrg = $bc->getTotalSrvCharge()) > 0) {
    $bill_areas[] = array('HS', HS_DESC, $hs_chrg, 3);
}
if (($op_chrg = $bc->getTotalOpCharge()) > 0) {
    $bill_areas[] = array('OR', OP_DESC, $op_chrg, 4);
}

$ndays = 0;
$nrvu  = 0;
$npf   = 0;
$bc->getTotalPFParams($ndays, $nrvu, $npf, 'D1');
if ($npf > 0) {
    $bill_areas[] = array('D1', D1_DESC, $npf, 5);
}

$npf   = 0;
$bc->getTotalPFParams($ndays, $nrvu, $npf, 'D2');
if ($npf > 0) { 
    $bill_areas[] = array('D2', D2_DESC, $npf, 6);
}

$npf   = 0;
$bc->getTotalPFParams($ndays, $nrvu, $npf, 'D3');
if ($npf > 0) {
    $bill_areas[] = array('D3', D3_DESC, $npf, 7);
}

$npf   = 0;
$bc->getTotalPFParams($ndays, $nrvu, $npf, 'D4');
if ($npf > 0) {
    $bill_areas[] = array('D4', D4_DESC, $npf, 8);
}

if (($xc_chrg = $bc->getTotalMscCharge()) > 0) {
    $bill_areas[] = array('XC', XC_DESC, $xc_chrg, 9);
}

$hcares = '';
$hcareHeaders = '';
$hcareFooters = '';

// Get the health insurances with coverage for this package ...
$obj = (object) 'deposit'; 
$obj->id   = 'depo01';
$obj->name = 'Deposit';
$obj->amountlimit = $bc->getPreviousPayments(true);

$hcares .= "  <input type=\"hidden\" id=\"hcare_{$obj->id}\" name=\"hcare\" hcareId=\"{$obj->id}\" value=\"{$obj->amountlimit}\" />\n";
$hcareHeaders .= "        <th width=\"15%\" colspan=\"2\" nowrap=\"nowrap\">{$obj->name}</th>\n";
$hcareFooters .= "        <th id=\"total_coverage_{$obj->id}\" colspan=\"2\" style=\"font:bold 14px Arial;text-align:right\"></th>\n";        

?>
<?= $hcares ?>
  <table class="segList" border="1" cellpadding="0" cellspacing="0" width="100%">
    <thead>
      <tr>        
        <th width="*"><?= $title ?></th>
        <th width="12%" nowrap="nowrap">Total Charge</th>
        <?= $hcareHeaders ?>
        <th width="12%" nowrap="nowrap">Balance</th>
        <th width="6%">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
<?php
  $itemsHTML = "";
  $items = array();  
  
  reset($bill_areas);
  foreach($bill_areas as $i=>$v) {
      $items[$i]['area'] = $v[0];
      $items[$i]['name'] = $v[1];
      $items[$i]['charge'] = $v[2];
      $items[$i]['charge_show'] = number_format($v[2], 2);
      $items[$i]['priority_nr'] = $v[3]; 
  }  
  
  // clean up items
  
  // fetch order and applied coverages
  foreach ($items as $i=>$item) {
    $sql = "SELECT 'depo01' as hcare_id, IF(priority,priority,999) AS priority_nr, deposit FROM seg_applied_deposit\n".
           "   WHERE ref_no=".$db->qstr($NR)." AND bill_area=".$db->qstr($items[$i]['area']);
    $result=$db->Execute($sql);
    $deposits = array();
    $priority_nr = 999;
    if ($result) {
      while ($row=$result->FetchRow()) {
        if ((int)$row['priority_nr'] < $priority_nr) $priority_nr=(int)$row['priority_nr'];
        $deposits[$row['hcare_id']] = $row['deposit'];
      }
    }
    if ($priority_nr != 999) $items[$i]['priority_nr'] = $priority_nr;
    $items[$i]['deposit'] = $deposits;
  }
  
  function cmp_priority($a, $b)
  {
    return ((int)$a['priority_nr'] - (int)$b['priority_nr']);
  }
  usort($items, "cmp_priority");    
    
  // generate HTML
  foreach ($items as $i=>$item) {
//    if (!$item['source']) $item['source']='M';
    $alt = ($i%2>0) ? ' class="alt"' : '';
    $itemsHTML .= <<<EOD
      <tr{$alt}>
        <td>
          {$item['name']}
          <input type="hidden" id="{$item['area']}" name="items" refSource="1" itemCode="{$item['area']}" value="{$item['charge']}"/>
        </td>
        <td class="rightAlign" style="font:bold 14px Arial; color:#008000">{$item['charge_show']}</td>
EOD;

//    foreach ($pkg_hcare as $v) {
      $deposit =(float)$item['deposit'][$obj->id];
      $deposit_show = number_format($deposit,2);
      $checked = $deposit ? 'checked="checked"': "";
      $itemsHTML .= <<<EOT
        <td width="1%" class="centerAlign">
          <input class="segInput" type="checkbox" id="apply_{$obj->id}_{$item['area']}" name="apply_{$item['area']}" hcareId="{$obj->id}" refSource="1" itemCode="{$item['area']}" onclick="calculateDeposit()" {$checked}/>
        </td>
        <td class="centerAlign" width="10%">
          <input class="segInput" type="text" id="coverage_{$obj->id}_{$item['area']}" hcareId="{$obj->id}" refSource="1" itemCode="{$item['area']}" value="{$deposit_show}" onchange="calculateDeposit(this)" onfocus="this.select()" style="width:99%; text-align:right" />
        </td>
EOT;
//    }

    $itemsHTML .= <<<EOA
        <td class="rightAlign">
          <input type="hidden" id="excess_{$item['area']}" refsource="1" itemCode="{$item['area']}" value="0" />
          <span style="font:bold 14px Arial; color:#c00000; align:right">0.00</span>
        </td>
        <td class="centerAlign" nowrap="nowrap">
          <img title="Auto-compute" class="segSimulatedLink" src="../../images/cashier_check.png" border="0" align="absmiddle" refSource="1" itemCode="{$item['area']}" onclick="calculateDeposit(false,this)"/>
          <img title="Up" class="segSimulatedLink" src="../../images/cashier_up.gif" border="0" align="absmiddle" onclick="moveUp(this)" />
          <img title="Down" class="segSimulatedLink" src="../../images/cashier_down.gif" border="0" align="absmiddle" onclick="moveDown(this)" />
        </td>
      </tr>
EOA;

  }

?>
<?= 
  $itemsHTML ?
  $itemsHTML :
  '<tr><td colspan="9" style="padding-left:10px">No billable area for this patient ...</td></tr>'
?>
    </tbody>
    <tfoot>
      <tr>
        <th>Totals</th>
        <th id="total_cost" style="font:bold 14px Arial;text-align:right"></th>
<?= $hcareFooters ?>
        <th id="total_balance" style="font:bold 14px Arial;text-align:right"></th>        
        <th></th>
      </tr>
    </tfoot>
  </table>