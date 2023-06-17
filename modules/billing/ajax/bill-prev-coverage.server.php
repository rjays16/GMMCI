<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/billing/class_hcare_benefit.php');

require_once($root_path.'modules/billing/ajax/bill-prev-coverage.common.php');

function populateCombo($strSQL, &$objResponse, $mode) {
	global $db;	
				  
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			$objResponse->addScriptCall("js_ClearOptions","hcare_combo");
			$objResponse->addScriptCall("js_AddOptions","hcare_combo","- Select Health Insurance -", 0);
			while ($row = $result->FetchRow()) {			
				$objResponse->addScriptCall("js_AddOptions", "hcare_combo", $row['name'], $row['hcare_id']);
			}
		}
	} else 
		$objResponse->addAlert("ERROR: Cannot retrieve health insurances".($mode == 0 ? "" : " with previous coverage")." for this encounter! ...");		
}

function getHealthInsurancesForEdit($enc_nr) {
	$objResponse = new xajaxResponse();	

	$strSQL = "select distinct sei.hcare_id, cif.name ".
   			  "   from care_insurance_firm as cif inner join seg_encounter_insurance as sei ".
      		  "      on cif.hcare_id = sei.hcare_id ".
   			  "   where sei.encounter_nr = '".$enc_nr."' ".
   			  "      order by cif.name";			  			  
	populateCombo($strSQL, $objResponse, 0);
	return $objResponse;
}

function getHealthInsurancesForViewing($enc_nr, $frm_dte) {	
	$objResponse = new xajaxResponse();	

	$strSQL = "select distinct td.hcare_id, cif.name ".
   			  "   from (care_insurance_firm as cif inner join seg_used_coverage_details as td ".
   			  "      on cif.hcare_id = td.hcare_id) inner join ".
			  "      (select disclose_id ".
			  "         from seg_used_coverage ".
			  "         where str_to_date(disclose_dte, '%Y-%m-%d %H:%i:%s') >= '".$frm_dte."' ".
			  "            and encounter_nr = '".$enc_nr."' ".
			  "         order by disclose_dte limit 1) as th ".
			  "      on th.disclose_id = td.disclose_id ".
			  "   order by cif.name";
	populateCombo($strSQL, $objResponse, 1);
	return $objResponse;
}

function getPrevCoverage($sdisclose_id, $nhcare_id, $nentry_no, $sfld_code) {
	global $db;
	
	$ncoverage = 0;
	
	$strSQL = "select used_".$sfld_code."_".(strcasecmp($sfld_code, 'days') == 0 ? "covered" : "coverage")." ".
			  "   from seg_used_coverage_details ".
			  "   where disclose_id = '".$sdisclose_id."' ".
			  "      and hcare_id = ".$nhcare_id." ".
			  "      and entry_no = ".$nentry_no;			  
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount())
			while ($row = $result->FetchRow()) {			
				$ncoverage = $row['used_'.$sfld_code.'_'.(strcasecmp($sfld_code,'days')==0?'covered':'coverage')];
			}
	}
	
	return($ncoverage);
}

function showPrevCoverageDetails($sdisclose_id = '', $nhcare_id, $nentry_no) {	
	$objResponse = new xajaxResponse();	

	$objb = new HealthCareBenefit();
	
	$fldcode = '';
	
	if ($result = $objb->getHealthCareBenefits()) {
		if ($result->RecordCount()) {						
			$objResponse->addScriptCall("jsClearList","coverage_details");			
			while ($row = $result->FetchRow()) {			
				switch ($row['area_code']) {
					case 'AC':
						$fldcode = 'acc';
						break;
					
					case 'HS':
						$fldcode = 'srv';
						break;
					
					case 'MD':
						$fldcode = 'med';
						break;
					
					case 'OR':
						$fldcode = 'ops';
						break;
					
					case 'SP':
						$fldcode = 'sup';
						break;
					
					case 'D1':
					case 'D2':
					case 'D3':
					case 'D4':
						$fldcode = strtolower($row['area_code']);
						break;
							
					case 'XC':
						$fldcode = 'msc';
						
					default:
						$fldcode = 'days';
								
				}				
						
				$objResponse->addScriptCall("js_AddCoverageDetail", $fldcode, $row['particulars'], getPrevCoverage($sdisclose_id, $nhcare_id, $nentry_no, $fldcode), (strcasecmp($fldcode, 'days') != 0 ? 2 : 0));
			}			
			
		}
	}
	
	$m = $objb->getMinEntry($sdisclose_id, $nhcare_id);
	$n = $objb->getMaxEntry($sdisclose_id, $nhcare_id);	
	
	if ($m == $n) 
		$objResponse->addScriptCall("js_setRecLinks", FALSE, FALSE);
	else {
		if ($nentry_no == $m)
			$objResponse->addScriptCall("js_setRecLinks", FALSE, TRUE);
		elseif ($nentry_no == $n)
			$objResponse->addScriptCall("js_setRecLinks", TRUE, FALSE);
		else		
			$objResponse->addScriptCall("js_setRecLinks", TRUE, TRUE);
	}
				
	return $objResponse;	
}

function delPrevCoverageDetail($sdisclose_id, $nhcare_id, $nentry_no) {
	$objResponse = new xajaxResponse();	

	$objb = new HealthCareBenefit();
	
	if ($objb->delThisEntry($sdisclose_id, $nhcare_id, $nentry_no)) 
		$objResponse->addScriptCall("js_initWindow");
	else
		$objResponse->addAlert("ERROR: ".$objb->getErrorMsg());
	
	return $objResponse;		
}

$xajax->processRequests();
?>
