<?php
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path."modules/system_admin/ajax/discount_application.common.php");
	
	function applyToBillArea($sarea_id) {
		$objResponse = new xajaxResponse();
	
		if (!isset($_SESSION['bill_areas'])) $_SESSION['bill_areas'] = array();		
		if (!isset($_SESSION['bill_areas'][$sarea_id]))
			$_SESSION['bill_areas'][$sarea_id] = $sarea_id;
		else
			unset($_SESSION['bill_areas'][$sarea_id]);		
				
		return $objResponse;
	}		
	
	function getBillAreas($sdiscount_id, $sarea_ids) {
		global $db;
		
		$objResponse = new xajaxResponse();
		
//		$objResponse->addAlert("Discount ID is ".$sdiscount_id);
		
		$strSQL = "select bill_area, group_concat(distinct benefit_desc order by benefit_desc separator ', ') as area_desc, ".
   				  "   (select count(*) as n from seg_discount as sd ".
      			  "       where sd.billareas_applied regexp concat(shb.bill_area,':') ".
         		  "          and sd.discountid = '".$sdiscount_id."') as applied ".
	   			  "   from seg_hcare_benefits as shb 
                      where bill_area != '' ".
   				  "   group by bill_area";
				  
		if ($result = $db->Execute($strSQL)) {		
			if ($result->RecordCount()) {									
				$objResponse->addScriptCall("js_clearBillAreas");												
				while ($row = $result->FetchRow()) {
                    if ($sarea_ids == '') 
                        $n_applied = true;
                    else
                        $n_applied = (strpos($sarea_ids, $row["bill_area"]) === false ? $row["applied"] : 1);
					$objResponse->addScriptCall("js_addBillArea", $row["bill_area"], $row["area_desc"], $n_applied);
				}
			}	
		} 
		else 		
			$objResponse->addAlert("ERROR: ".$db->ErrorMsg());
	
		return $objResponse;	
	}
	
	$xajax->processRequests();
?>