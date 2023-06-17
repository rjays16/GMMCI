<?php
	function listDiscounts() {
		global $db;

//		$sql="SELECT * FROM seg_discount ORDER BY discountdesc";		
		$sql="select sd.*, ".
   			 "   (select group_concat(distinct benefit_desc order by benefit_desc separator ', ') as sdesc ".
      		 "      from seg_hcare_benefits as shb where sd.billareas_applied regexp concat(shb.bill_area,':') and bill_area != '') as areas_desc ".
			 "   from seg_discount as sd order by discountdesc";
		$objResponse = new xajaxResponse();
		
    	$ergebnis=$db->Execute($sql);
		$objResponse->addScriptCall("js_clearDiscounts");
		$rows=$ergebnis->RecordCount();

		while($result=$ergebnis->FetchRow()) {
				//$objResponse->addAlert(print_r($result,TRUE));
			$areas_desc = (is_null($result['areas_desc'])) ? '' : $result['areas_desc'];
			$objResponse->addScriptCall("js_addDiscount", $result['discountid'], $result['discountdesc'], $result['discount']-0,$result['area_used'], $result['billareas_applied'], $areas_desc);
		}
	
		if (!$rows) $objResponse->addScriptCall("js_addDiscount",NULL);
		return $objResponse;
	}
		
	function newDiscount($id, $desc, $discount, $area, $areas_id, $areas_desc, $encoder) {
		global $db;
		
		$dsc_obj=new SegDiscount;
		$result=$dsc_obj->CreateDiscount($id, $desc, $discount, $area, $areas_id, $encoder);		
		$objResponse = new xajaxResponse();
		
		if ($result) {
			$objResponse->addScriptCall("js_addDiscount", $id, $desc, $discount, $area, $areas_id, $areas_desc, TRUE);
			$objResponse->addAlert("New discount type succesfully added...");
		}
		else {			
			//$objResponse->addAlert("Error adding new discount type...");			
			$objResponse->addAlert($dsc_obj->sql);
		}
		return $objResponse;
	}
			
	function updDiscount($oldid, $id, $desc, $discount, $area, $bill_areas, $encoder, $rowno) {
		global $db;
		
		$dsc_obj=new SegDiscount;
		$result=$dsc_obj->UpdateDiscount($oldid, $id, $desc, $discount, $area, $bill_areas, $encoder);
		$objResponse = new xajaxResponse();
		
		if ($result) {
			$objResponse->addScriptCall("js_saveUpdate",$rowno);
			$objResponse->addAlert("Discount entry successfuly updated...");
		}
		else 
			$objResponse->addAlert("sql:\n".$dsc_obj->sql."\n\nmsg:". $db->ErrorMsg());	

		return $objResponse;		
	}
	
	function delDiscount($id, $rowno) {
	
		$dsc_obj=new SegDiscount;
		$result=$dsc_obj->DeleteDiscount($id);
		$objResponse = new xajaxResponse();
		
		if ($result) {
			$objResponse->addScriptCall("js_rmvDiscount",$rowno);
			$objResponse->addAlert("Discount entry successfully deleted...");
		}
		else {
			$objResponse->addAlert("Delete error...");			
		}
		return $objResponse;
	}
	
	function saveBillAreas($elem1, $elem2, $elem3) {
		global $db;
	
		$objResponse = new xajaxResponse();
		$sbill_areas = '';
		$sbillareas_desc = '';
		
#		$objResponse->addAlert("Bill areas are ".implode(", ", $_SESSION['bill_areas']));
		
		if ((isset($_SESSION['bill_areas'])) && (is_array($_SESSION['bill_areas']))) {					
			$sareas = implode("','", $_SESSION['bill_areas']);
			$sareas = "('".$sareas."')";
			
			$strSQL = "select group_concat(distinct benefit_desc order by benefit_desc separator ', ') as areas_desc ".
					  "   from seg_hcare_benefits where bill_area in ".$sareas;
			if ($result = $db->Execute($strSQL)) {		
				if ($result->RecordCount()) {
					$row = $result->FetchRow();
					$sbillareas_desc = $row['areas_desc'];					
				}
			}
			
			$sbill_areas = implode(':', $_SESSION['bill_areas']).':';
			unset($_SESSION['bill_areas']);
		}
		$objResponse->addScriptCall("js_saveBillAreas", $elem1, $elem2, $elem3, $sbill_areas, $sbillareas_desc);	
	
		return $objResponse;
	}	

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	require($root_path.'include/care_api_classes/class_discount.php');
	require($root_path."modules/system_admin/ajax/discount.common.php");
	$xajax->processRequests();
?>