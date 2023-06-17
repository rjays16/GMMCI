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
define('DEFAULT_NBPKG_RATE', 1750);  //added by jasper 09/04/2013 FOR BUG#305
define('DEFAULT_NBPKG_NAME','NEW BORN');//Added By Jarel 12/09/2013
 
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

$pkg_id = $_GET['pkg'];
$pkg_name = $bc->getPackageName();
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
if ($bc->getPackageBenefits($pkg_id)) {
    $pkg_hcare = $bc->getPkgBenefits();
    $issurgical = $bc->isSurgicalCase();
    $d2rate = $bc->isFreeDistribution() ? 0 : $bc->getCaseRatePkgLimit('D2', $issurgical);
    $d3rate = $bc->isFreeDistribution() ? 0 : $bc->getCaseRatePkgLimit('D3', $issurgical);
    $d4rate = $bc->isFreeDistribution() ? 0 : $bc->getCaseRatePkgLimit('D4', $issurgical);
      
    foreach ($pkg_hcare as $v) {        
      $hcares .= "  <input type=\"hidden\" id=\"hcare_{$v->hcare_id}\" name=\"hcare\" hcareId=\"{$v->hcare_id}\" d2Rate=\"{$d2rate}\" d3Rate=\"{$d3rate}\" d4Rate=\"{$d4rate}\" value=\"{$v->hcare_amountlimit}\" />\n";
      $hcareHeaders .= "        <th width=\"15%\" colspan=\"2\" nowrap=\"nowrap\">{$v->firm_id}</th>\n";
      $hcareFooters .= "        <th id=\"total_coverage_{$v->hcare_id}\" colspan=\"2\" style=\"font:bold 14px Arial;text-align:right\"></th>\n";
    }
}

?>
<?= $hcares ?>
  <table class="segList" border="1" cellpadding="0" cellspacing="0" width="100%">
    <thead>
      <tr>        
        <th width="*"><?= $title ?></th>
        <th width="12%" nowrap="nowrap">Total Charge</th>
<?= $hcareHeaders ?>
        <th width="12%" nowrap="nowrap">Excess</th>
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
  $totalcoverage = array();
  foreach ($items as $i=>$item) {
    $sql = "SELECT hcare_id, IF(priority,priority,999) AS priority_nr, coverage FROM seg_applied_pkgcoverage\n".
           "   WHERE ref_no=".$db->qstr($NR)." AND bill_area=".$db->qstr($items[$i]['area']);
    $result=$db->Execute($sql);
    $coverages = array();
    $limits = array();
    $balreadyset = false;
    $priority_nr = 999;
    $totalcoverage[$items[$i]['area']] = 0;
    if ($result) {
        if ($result->RecordCount()) {
            while ($row=$result->FetchRow()) {
                if ((int)$row['priority_nr'] < $priority_nr) $priority_nr=(int)$row['priority_nr'];
                $coverages[$row['hcare_id']] = $row['coverage'];
                $totalcoverage[$items[$i]['area']] += $row['coverage'];
            }
            $balreadyset = (count($coverages) > 0);
        }
        
        $pkg_hcare = $bc->getPkgBenefits();
        $issurgical = $bc->isSurgicalCase();
        foreach ($pkg_hcare as $v) {
            $nCoverage = 0;
            $amountlimit = $v->hcare_amountlimit;
            if (!$bc->isFreeDistribution()) {
                switch ($items[$i]['area']) {
                    case 'AC':
                    case 'MS':
                    case 'HS':
                    case 'OR':
                    case 'XC':
                        // Get the % for hospital charges ...
                        $rate = $bc->getCaseRatePkgLimit('', $issurgical);
                        $nCoverage = $amountlimit * $rate;                                    

                        // Coverage for accommodation ...
                        $nchrg = $bc->compTotalAccommodationChrg();
                        if (isset($totalcoverage['AC'])) {
                            if ($totalcoverage['AC'] < $nchrg) $nchrg = $totalcoverage['AC']; 
                        }

                        if ($nchrg < $nCoverage) {                       
                            if ($items[$i]['area'] == 'AC') {
                                $nCoverage = $nchrg;
                                break;
                            }
                            else
                                $nCoverage -= $nchrg;
                        }
                        else {
                            if ($items[$i]['area'] == 'AC')
                                break;
                            else
                                $nCoverage = 0;
                        }

                        // Coverage for medicines ...
                        $nchrg = $bc->getTotalMedCharge();
                        if (isset($totalcoverage['MS'])) {
                            if ($totalcoverage['MS'] < $nchrg) $nchrg = $totalcoverage['MS']; 
                        }

                        if ($nchrg < $nCoverage) {                        
                            if ($items[$i]['area'] == 'MS') {
                                $nCoverage = $nchrg;
                                break;
                            }
                            else
                                $nCoverage -= $nchrg;
                        }
                        else {
                            if ($items[$i]['area'] == 'MS')
                                break;
                            else
                                $nCoverage = 0;
                        }                     

                        // Coverage for hospital services ...
                        $nchrg = $bc->getTotalSrvCharge();
                        if (isset($totalcoverage['HS'])) {
                            if ($totalcoverage['HS'] < $nchrg) $nchrg = $totalcoverage['HS'];
                        }

                        if ($nchrg < $nCoverage) {                                                
                            if ($items[$i]['area'] == 'HS') {
                                $nCoverage = $nchrg;
                                break;
                            }
                            else
                                $nCoverage -= $nchrg;
                        }
                        else {
                            if ($items[$i]['area'] == 'HS')
                                break;
                            else
                                $nCoverage = 0;
                        } 

                        // Coverage for operating room ...                
                        $nchrg = $bc->getTotalOpCharge();
                        if (isset($totalcoverage['OR'])) {
                            if ($totalcoverage['OR'] < $nchrg) $nchrg = $totalcoverage['OR'];
                        }

                        if ($nchrg < $nCoverage) {                        
                            if ($items[$i]['area'] == 'OR') {                                                                                                               
                                $nCoverage = $nchrg;
                                break;
                            }
                            else
                                $nCoverage -= $nchrg;
                        }
                        else {
                            if ($items[$i]['area'] == 'OR')
                                break;
                            else
                                $nCoverage = 0;
                        } 

                        // Coverage for miscellaneous expenses ...
                        $nchrg = $bc->getTotalMscCharge();
                        if (isset($totalcoverage['XC'])) {
                            if ($totalcoverage['XC'] < $nchrg) $nchrg = $totalcoverage['XC'];
                        }

                        if ($nchrg < $nCoverage) {
                            if ($items[$i]['area'] == 'XC') {
                                $nCoverage = $nchrg;
                                break;
                            }
                            else
                                $nCoverage -= $nchrg;
                        }
                        else {
                            if ($items[$i]['area'] == 'XC')
                                break;
                            else
                                $nCoverage = 0;
                        }
                        break;

                    case 'D1':
                    case 'D2':                        
                        $rate = $bc->getCaseRatePkgLimit('D2', $issurgical);
                        //added by jasper 09/03/2013 FOR BUG#305
                        if ($pkg_name == DEFAULT_NBPKG_NAME) {
                            $nCoverage = DEFAULT_NBPKG_RATE * $rate;    
                        } else {
                            $nCoverage = $amountlimit * $rate;
                        }

                        //added by jasper 09/03/2013 FOR BUG#305
                        $total_pf = $bc->getTotalPFCharge('D2');
                        if ($total_pf < $nCoverage) {
                            if ($items[$i]['area'] == 'D2') {
                                $nCoverage = $total_pf;
                                break;
                            }
                            else {
                                $nCoverage -= $total_pf;                                    
                            }                                
                        }
                        else {
                            if ($items[$i]['area'] == 'D2')
                                break;
                            else
                                $nCoverage = 0;
                        }
                        if ($nCoverage > 0) {      
                            $npf = $bc->getTotalPFCharge('D1');                            
                            if ($npf < $nCoverage) {
                                $nCoverage = $npf;
                            }
                        }                        
                        break;                    

                    case 'D3':
                        // Compute the % for surgeons ...
                        $rate = $bc->getCaseRatePkgLimit('D3', $issurgical);
                        $nCoverage = $amountlimit * $rate;

                        $npf = $bc->getTotalPFCharge('D3');
                        if ($npf < $nCoverage) {
                            $nCoverage = $npf;
                        }

                        // Compute if there is no anaesthesiologist PF ...
                        $pfd4 = $bc->getTotalPFCharge('D4');
                        if ($pfd4 == 0) {
                            $rate = $bc->getCaseRatePkgLimit('D4', $issurgical);
                            $nCoverage += $amountlimit * $rate;

                            if ($npf < $nCoverage) {
                                $nCoverage = $npf;
                            }
                        }                                        
                        break;

                    case 'D4':
                        // Compute the % for anaesthesiologists ...
                        $rate = $bc->getCaseRatePkgLimit('D4', $issurgical);
                        $nCoverage = $amountlimit * $rate;                                    

                        $npf = $bc->getTotalPFCharge('D4');
                        if ($npf < $nCoverage) {
                            $nCoverage = $npf;
                        }                                                
                        break;

                }
            }

            if (!$balreadyset) $coverages[$v->hcare_id] = $nCoverage;
            $limits[$v->hcare_id] = $nCoverage;
        }  // for loop
    }
    if ($priority_nr != 999) $items[$i]['priority_nr'] = $priority_nr;
    $items[$i]['coverage'] = $coverages;
    $items[$i]['limit'] = $limits;
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
          <input type="hidden" id="{$item['area']}" name="items" refSource="1" itemCode="{$item['area']}" discount="{$bc->getBillAreaDRate($item['area'])}" value="{$item['charge']}"/>
        </td>
        <td class="rightAlign" style="font:bold 14px Arial; color:#008000">{$item['charge_show']}</td>
EOD;

    foreach ($pkg_hcare as $v) {
      $coverage = (float)$item['coverage'][$v->hcare_id];
      $limit = (float)$item['limit'][$v->hcare_id];
      $coverage_show = number_format($coverage,2);
      $checked = $coverage ? 'checked="checked"': "";
      $itemsHTML .= <<<EOT
        <td width="1%" class="centerAlign">
          <input class="segInput" type="checkbox" id="apply_{$v->hcare_id}_{$item['area']}" name="apply_{$item['area']}" hcareId="{$v->hcare_id}" refSource="1" itemCode="{$item['area']}" onclick="calculateCoverage()" {$checked}/>
        </td>
        <td class="centerAlign" width="10%">
          <input class="segInput" type="text" id="coverage_{$v->hcare_id}_{$item['area']}" hcareId="{$v->hcare_id}" refSource="1" itemCode="{$item['area']}" value="{$coverage_show}" onchange="calculateCoverage(this)" onfocus="this.select()" style="width:99%; text-align:right" />
          <input type="hidden" id="limit_{$v->hcare_id}_{$item['area']}" hcareId="{$v->hcare_id}" refSource="1" itemCode="{$item['area']}" value="{$limit}" />
        </td>
EOT;
    }

    $itemsHTML .= <<<EOA
        <td class="rightAlign">
          <input type="hidden" id="excess_{$item['area']}" refsource="1" itemCode="{$item['area']}" value="0" />
          <span style="font:bold 14px Arial; color:#c00000; align:right">0.00</span>
        </td>
        <td class="centerAlign" nowrap="nowrap">
          <img title="Auto-compute" class="segSimulatedLink" src="../../images/cashier_check.png" border="0" align="absmiddle" refSource="1" itemCode="{$item['area']}" onclick="calculateCoverage(false,this)"/>
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
        <th id="total_excess" style="font:bold 14px Arial;text-align:right"></th>        
        <th></th>
      </tr>
    </tfoot>
  </table>