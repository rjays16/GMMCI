<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/radiology/ajax/radio-request-new.common.php');

require_once($root_path.'include/care_api_classes/class_radiology.php');

	function populateRequestListByRefNo($refno=0, $batch_nr=0){
		global $db;
		$objResponse = new xajaxResponse();
		$objRadio = new SegRadio();

		$rs = $objRadio->getAllRadioInfoByRefNo($refno, $batch_nr);
		
		if ($rs){
			while($result=$rs->FetchRow()) {
				$name = $result["name"];
				if (strlen($name)>40)
					$name = substr($result["name"],0,40)."...";
				$objResponse->addScriptCall("initialRequestList",$result['service_code'],$result['dept_short_name'],
											$name, stripslashes($result['clinical_info']), $result['request_doctor'],
											$result['request_doctor_name'], $result['is_in_house'], $result['price_cash_orig'], 
											$result['price_charge'],$result['hasPaid'],$result['is_socialized'],
											$result['parent_batch_nr'],$result['approved_by_head'],$result['remarks']);
			}
		}else{
			$objResponse->addScriptCall("emptyIntialRequestList");		
		}
		$objResponse->addScriptCall("refreshDiscount");
		return $objResponse;
	}# end of function populateRequestListByRefNo


$xajax->processRequests();
?>