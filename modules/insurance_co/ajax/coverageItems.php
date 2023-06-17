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
define('NO_2LEVEL_CHK',1);
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

//global $db;

//$sql="SELECT ei.hcare_id FROM seg_encounter_insurance AS ei";
//$bc->correctBillDates();
$bc->getConfinementType();
$mode = $_GET['mode'];

if ($mode=="M") {
	$bc->getMedicineBenefits();
	$meds = $bc->getMedConfineBenefits();
	$bc->getConfineBenefits('MS','M');
	$confine = $bc->med_confine_benefits;
	$title = "Drugs and Medicines";
  $discountrate = "<input type=\"hidden\" id=\"discount\" name=\"discount\" value=\"{$bc->getBillAreaDRate('MS')}\" />";
}
elseif ($mode=='O') {
	$bc->getServiceBenefits();
	$srvs = $bc->getSrvBenefits();
	$bc->getConfineBenefits('HS');
	$confine = $bc->srv_confine_benefits;
	$title = "X-Ray, Lab and Other Charges";
  $discountrate = "<input type=\"hidden\" id=\"discount\" name=\"discount\" value=\"{$bc->getBillAreaDRate('HS')}\" />";
}
//$confine = $bc->getMedConfineCoverage();
//print_r( $meds );
$hcares = '';
$hcareHeaders = '';
$hcareFooters = '';

//print_r($confine, true);

foreach ($confine as $v) {
	$hcares .= "<input type=\"hidden\" id=\"hcare_{$v->hcare_id}\" name=\"hcare\" hcareId=\"{$v->hcare_id}\" value=\"{$v->hcare_amountlimit}\" />\n";
	//$hcareHeaders .= "<th width=\"15%\" colspan=\"2\" nowrap=\"nowrap\">{$v->firm_id}</th>\n";
	
	$hcareHeaders .= "<th class=\"centerAlign\" nowrap=\"nowrap\">".
		"<input class=\"segInput\" type=\"checkbox\" style=\"cursor:pointer\" id=\"apply_{$v->hcare_id}\" hcareId=\"{$v->hcare_id}\" onclick=\"applyToAll(this)\" /></th>".
		"<th style=\"font:bold 14px Arial;text-align:right\">{$v->firm_id}</th>\n";
#  $hcareFooters .= "        <th id=\"total_coverage_{$v->hcare_id}\" colspan=\"2\" style=\"font:bold 14px Arial;text-align:right\"></th>\n";   
	$hcareFooters .= "<th class=\"centerAlign\" nowrap=\"nowrap\">".
                      "<input class=\"segInput\" type=\"checkbox\" style=\"cursor:pointer\" id=\"apply_{$v->hcare_id}\" hcareId=\"{$v->hcare_id}\" onclick=\"applyToAll(this)\" />".
                   "</th>".
		"<th id=\"total_coverage_{$v->hcare_id}\" style=\"font:bold 14px Arial;text-align:right\"></th>\n";
}

?>
<?= $hcares ?>
<!--
	<input type="hidden" id="hcare_PHIC" name="hcare" hcareId="PHIC" value="5000"/>
	<input type="hidden" id="hcare_MCARE" name="hcare" hcareId="MCARE" value="4000"/>
	<input type="hidden" id="hcare_Westlife" name="hcare" hcareId="Westlife" value="2500"/>
-->
	<table class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>        
				<th width="*"><?= $title ?></th>
				<th width="6%">Qty</th>
				<th width="12%" nowrap="nowrap">Total cost</th>
<?= $hcareHeaders ?>
<!--        <th colspan="2" nowrap="nowrap">Insurance 1</th>
				<th colspan="2" nowrap="nowrap">Insurance 2</th>
				<th colspan="2" nowrap="nowrap">Insurance 3</th>-->
				<th width="12%" nowrap="nowrap">Excess</th>
				<th width="6%"></th>
			</tr>
		</thead>
		<tbody>
<?php
	$itemsHTML = "";
	$items = array();
	
	if ($mode == 'M') {
		foreach ($meds as $i=>$med) {
			if ($items[$med->bestellnum]) {
				$items[$med->bestellnum]['cost'] = (float) $med->item_charge + (float) $items[$med->bestellnum]['cost'];
				$items[$med->bestellnum]['qty'] = (float) $med->item_qty + (float) $items[$med->bestellnum]['qty'];
				$items[$med->bestellnum]['cost_show'] = number_format($items[$med->bestellnum]['cost'],2);
			}
			else {
				$items[$med->bestellnum]['code'] = $med->bestellnum;
				$items[$med->bestellnum]['name'] = $med->artikelname;
				$items[$med->bestellnum]['item_price'] = $med->item_price;
				$items[$med->bestellnum]['qty'] = $med->item_qty;
				$items[$med->bestellnum]['cost'] = $med->item_charge;
				$items[$med->bestellnum]['cost_show'] = number_format($med->item_charge,2);
				$items[$med->bestellnum]['source'] = 'M';
			}
		}
	}
	else {
		$source_convert = array('LB'=>'L', 'RD'=>'R', 'SU'=>'S', 'MS'=>'S', 'OE'=>'E', 'OA'=>'O');
		foreach ($srvs as $i=>$srv) {
			if ($items[$srv->serv_code]) {
			$cost = $srv->serv_price * $srv->serv_qty;
				$items[$srv->serv_code]['cost'] = $cost + (float) $items[$srv->serv_code]['cost'];
				$items[$srv->serv_code]['qty'] = (float) $srv->serv_qty + (float) $items[$srv->serv_code]['qty'];
				$items[$srv->serv_code]['cost_show'] = number_format($items[$srv->serv_code]['cost'],2);
			}
			else {
				$items[$srv->serv_code]['code'] = $srv->serv_code;
				$items[$srv->serv_code]['name'] = $srv->serv_desc;
				$items[$srv->serv_code]['item_price'] = $srv->serv_price;
				$items[$srv->serv_code]['qty'] = $srv->serv_qty;
				$cost = $srv->serv_price * $srv->serv_qty;
				$items[$srv->serv_code]['cost'] = $cost;
				$items[$srv->serv_code]['cost_show'] = number_format($cost,2);
				$items[$srv->serv_code]['source'] = $source_convert[$srv->serv_provider];
			}
		}
	}
	
	// clean up items
	
	// fetch order and applied coverages
	foreach ($items as $i=>$item) {
		$sql = "SELECT hcare_id,IF(priority,priority,999) AS priority_nr,coverage FROM seg_applied_coverage\n".
			"WHERE ref_no=".$db->qstr($NR)." AND source=".$db->qstr($items[$i]['source'])." AND item_code=".$db->qstr($items[$i]['code']);
		$result=$db->Execute($sql);
		$coverages = array();
		$priority_nr = 999;
		if ($result) {
			while ($row=$result->FetchRow()) {
				if ((int)$row['priority_nr'] < $priority_nr) $priority_nr=(int)$row['priority_nr'];
				$coverages[$row['hcare_id']] = $row['coverage'];
			}
		}
		$items[$i]['priority_nr'] = $priority_nr;
		$items[$i]['coverage'] = $coverages;
	}
	
	function cmp_priority($a, $b)
	{
		return ((int)$a['priority_nr'] - (int)$b['priority_nr']);
	}
	usort($items, "cmp_priority");
		
	// generate HTML
	foreach ($items as $i=>$item) {
		if (!$item['source']) $item['source']='M';
		$alt = ($i%2>0) ? ' class="alt"' : '';
		$itemsHTML .= <<<EOD
			<tr{$alt}>
				<td>
					{$item['name']}
					<input type="hidden" id="{$item['source']}_{$item['code']}" name="items" refSource="{$item['source']}" itemCode="{$item['code']}" value="{$item['cost']}"/>
				</td>
				<td class="centerAlign">{$item['qty']}</td>
				<td class="rightAlign" style="font:bold 14px Arial; color:#008000">{$item['cost_show']}</td>
EOD;

		foreach ($confine as $v) {
			$coverage=(float)$item['coverage'][$v->hcare_id];
			$coverage_show = number_format($coverage,2);
			$checked = $coverage ? 'checked="checked"': "";
			$itemsHTML .= <<<EOT
				<td width="1%" class="centerAlign">
					<input class="segInput" style="cursor:pointer" type="checkbox" id="apply_{$v->hcare_id}_{$item['source']}_{$item['code']}" name="apply_{$item['source']}_{$item['code']}" hcareId="{$v->hcare_id}" refSource="{$item['source']}" itemCode="{$item['code']}" onclick="calculateCoverage()" {$checked}/>
				</td>
				<td class="centerAlign" width="10%">
					<input class="segInput" type="text" id="coverage_{$v->hcare_id}_{$item['source']}_{$item['code']}" hcareId="{$v->hcare_id}" refSource="{$item['source']}" itemCode="{$item['code']}" value="{$coverage_show}" onchange="calculateCoverage(this)" onfocus="this.select()" style="width:99%; text-align:right" />
				</td>
EOT;
		}

		$itemsHTML .= <<<EOA
				<td class="rightAlign">
					<input type="hidden" id="excess_{$item['source']}_{$item['code']}" refsource="{$item['source']}" itemCode="{$item['code']}" value="0" />
					<span style="font:bold 14px Arial; color:#c00000; align:right">0.00</span>
				</td>
				<td class="centerAlign" nowrap="nowrap">
					<img title="Auto-compute" class="segSimulatedLink" src="../../images/cashier_check.png" border="0" align="absmiddle" refSource="{$item['source']}" itemCode="{$item['code']}" onclick="calculateCoverage(false,this)"/>
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
	'<tr><td colspan="10" style="padding-left:10px">No items of this category were found for this patient\'s billing records...</td></tr>'
?>
		</tbody>
		<tfoot>
			<tr>
        <th><?= $discountrate ?>Totals</th>
				<th></th>
				<th id="total_cost" style="font:bold 14px Arial;text-align:right"></th>
<?= $hcareFooters ?>
				<th id="total_excess" style="font:bold 14px Arial;text-align:right"></th>        
				<th></th>
			</tr>
		</tfoot>
	</table>