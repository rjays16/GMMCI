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
$bc->getRoomTypeBenefits();         // Accommodation
$bc->getMedicineBenefits();         // Drugs and Meds
$bc->getServiceBenefits();          // X-Ray, Lab and Others
$bc->getOpBenefits();               // Operating Room
$bc->getProfFeesBenefits();         // Professional Fees
$bc->getMiscellaneousBenefits();    // Miscellaneous

$bill_areas = array();
if (($ac_chrg = $bc->compTotalAccommodationChrg()) > 0) {
	$bc->getConfineBenefits('AC');
	$bill_areas[] = array('AC', AC_DESC, $bc->getAccConfineCoverage(), $ac_chrg, 1);
}
if (($md_chrg = $bc->getTotalMedCharge()) > 0) {
	$bc->getConfineBenefits('MS', 'M');
	$bill_areas[] = array('MS', MD_DESC, $bc->getMedConfineCoverage(), $md_chrg, 2);
}
if (($hs_chrg = $bc->getTotalSrvCharge()) > 0) {
	$bc->getConfineBenefits('HS');
	$bill_areas[] = array('HS', HS_DESC, $bc->getSrvConfineCoverage(), $hs_chrg, 3);
}
if (($op_chrg = $bc->getTotalOpCharge()) > 0) {
	$bc->getConfineBenefits('OR');
	$bill_areas[] = array('OR', OP_DESC, $bc->getOpsConfineCoverage(), $op_chrg, 4);
}

$ndays = 0;
$nrvu  = 0;
$npf   = 0;
$bc->getTotalPFParams($ndays, $nrvu, $npf, 'D1');
if ($npf > 0) {
	$bc->getConfineBenefits('D1');
	$bill_areas[] = array('D1', D1_DESC, $bc->pfs_confine_coverage['D1'], $npf, 5);
}

$npf   = 0;
$bc->getTotalPFParams($ndays, $nrvu, $npf, 'D2');
if ($npf > 0) {
	$bc->getConfineBenefits('D2');
	$bill_areas[] = array('D2', D2_DESC, $bc->pfs_confine_coverage['D2'], $npf, 6);
}

$npf   = 0;
$bc->getTotalPFParams($ndays, $nrvu, $npf, 'D3');
if ($npf > 0) {
	$bc->getConfineBenefits('D3');
	$bill_areas[] = array('D3', D3_DESC, $bc->pfs_confine_coverage['D3'], $npf, 7);
}

$npf   = 0;
$bc->getTotalPFParams($ndays, $nrvu, $npf, 'D4');
if ($npf > 0) {
	$bc->getConfineBenefits('D4');
	$bill_areas[] = array('D4', D4_DESC, $bc->pfs_confine_coverage['D4'], $npf, 8);
}

if (($xc_chrg = $bc->getTotalMscCharge()) > 0) {
	$bc->getConfineBenefits('XC');
	$bill_areas[] = array('XC', XC_DESC, $bc->getMscConfineCoverage(), $xc_chrg, 9);
}

$hcares = '';
$hcareHeaders = '';
$hcareFooters = '';

// Get the health insurances with coverage ...
$bc->getPerHCareCoverage();

if (!empty($bc->hcare_coverage)) {
	foreach($bc->hcare_coverage as $v) {
		$hcare_id    = $v->getID();
		$firm_id     = $v->getFirmID();
		$total_limit = $v->getAccCoverage() + $v->getMedCoverage() + $v->getSupCoverage() + $v->getSrvCoverage() + $v->getOpsCoverage() +
						 $v->getD1Coverage() + $v->getD2Coverage() + $v->getD3Coverage() + $v->getD4Coverage() + $v->getMscCoverage();

		reset($bill_areas);
		$amnt_limit = 0;
		foreach($bill_areas as $i=>$b) {
			switch ($b[0]) {
				case 'AC': $amnt_limit = $v->getAccCoverage(); break;
				case 'MS': $amnt_limit = $v->getMedCoverage(); break;
				case 'HS': $amnt_limit = $v->getSrvCoverage(); break;
				case 'OR': $amnt_limit = $v->getOpsCoverage(); break;
				case 'D1': $amnt_limit = $v->getD1Coverage();  break;
				case 'D2': $amnt_limit = $v->getD2Coverage();  break;
				case 'D3': $amnt_limit = $v->getD3Coverage();  break;
				case 'D4': $amnt_limit = $v->getD4Coverage();  break;
				case 'XC': $amnt_limit = $v->getMscCoverage();
			}

			$hcares .= "  <input type=\"hidden\" id=\"hcare_{$hcare_id}_{$b[0]}\" name=\"hcare\" hcareId=\"{$hcare_id}\" itemCode=\"{$b[0]}\" value=\"{$amnt_limit}\" />\n";
		}

//        $hcares .= "  <input type=\"hidden\" id=\"hcare_{$hcare_id}\" name=\"hcare\" hcareId=\"{$hcare_id}\" value=\"{$total_limit}\" />\n";
		$hcareHeaders .= "        <th width=\"15%\" colspan=\"2\" nowrap=\"nowrap\">{$firm_id}</th>\n";
		$hcareFooters .= "        <th id=\"total_coverage_{$hcare_id}\" colspan=\"2\" style=\"font:bold 14px Arial;text-align:right\"></th>\n";
	}
}
?>
<?= $hcares ?>
	<table class="segList" border="1" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
		<th width="*"><?= $title ?></th>
		<th width="12%" nowrap="nowrap">Total Charge</th>
		<th width="12%" nowrap="nowrap">Max Coverage</th>
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
		$items[$i]['max_coverage'] = $v[2];
		$items[$i]['charge'] = $v[3];
		$items[$i]['covrge_show'] = number_format($v[2], 2);
		$items[$i]['charge_show'] = number_format($v[3], 2);
		$items[$i]['priority_nr'] = $v[4];
	}

	// fetch order and applied coverages
	foreach ($items as $i=>$item) {
	$sql = "SELECT hcare_id, IF(priority,priority,999) AS priority_nr, coverage FROM seg_billingcoverage_adjustment\n".
			 "   WHERE ref_no=".$db->qstr($NR)." AND bill_area=".$db->qstr($items[$i]['area']);
	$result=$db->Execute($sql);
	$coverages = array();
	$priority_nr = 999;
	if ($result) {
		while ($row=$result->FetchRow()) {
		if ((int)$row['priority_nr'] < $priority_nr) $priority_nr=(int)$row['priority_nr'];
		$coverages[$row['hcare_id']] = $row['coverage'];
		}
	}
	if ($priority_nr != 999) $items[$i]['priority_nr'] = $priority_nr;
	$items[$i]['coverage'] = $coverages;
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
			<input type="hidden" id="{$item['area']}" name="items" refSource="1" itemCode="{$item['area']}" maxCoverage="{$item['max_coverage']}" value="{$item['charge']}"/>
		</td>
		<td class="rightAlign" style="font:bold 14px Arial; color:#008000">{$item['charge_show']}</td>
		<td class="rightAlign" style="font:bold 14px Arial; color:#008000">{$item['covrge_show']}</td>
EOD;

	foreach($bc->hcare_coverage as $v) {
		$hcare_id   = $v->getID();

		$coverage=(float)$item['coverage'][$hcare_id];
		$coverage_show = number_format($coverage,2);
		$checked = ($coverage || (($coverage == 0) && isset($item['coverage'][$hcare_id]))) ? 'checked="checked"': "";
		$itemsHTML .= <<<EOT
		<td width="1%" class="centerAlign">
			<input class="segInput" type="checkbox" id="apply_{$hcare_id}_{$item['area']}" name="apply_{$item['area']}" hcareId="{$hcare_id}" refSource="1" itemCode="{$item['area']}" onclick="calculateCoverage()" {$checked}/>
		</td>
		<td class="centerAlign" width="10%">
			<input class="segInput" type="text" id="coverage_{$hcare_id}_{$item['area']}" hcareId="{$hcare_id}" refSource="1" itemCode="{$item['area']}" value="{$coverage_show}" onchange="calculateCoverage(this)" onfocus="this.select()" style="width:99%; text-align:right" />
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
	'<tr><td colspan="10" style="padding-left:10px">No billable area for this patient ...</td></tr>'
?>
	</tbody>
	<tfoot>
		<tr>
		<th>Totals</th>
		<th id="total_cost" style="font:bold 14px Arial;text-align:right"></th>
		<th id="total_coverage" style="font:bold 14px Arial;text-align:right"></th>
<?= $hcareFooters ?>
		<th id="total_excess" style="font:bold 14px Arial;text-align:right"></th>
		<th></th>
		</tr>
	</tfoot>
	</table>