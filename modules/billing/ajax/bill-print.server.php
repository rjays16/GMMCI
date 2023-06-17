<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/billing/class_billing.php');
include_once($root_path.'include/care_api_classes/class_personell.php');
include_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'modules/billing/ajax/bill-print.common.php');
require_once($root_path.'classes/json/json.php');

function initMain($encounter){
	$objResponse = new xajaxResponse();
	$objBill = new Billing($encounter);
	//set first confinement type.
	$objBill->getConfinementType;
	//initialize main()
	main($objResponse, $objBill);
	// return object response
	return $objResponse;
}//end of function initMain

function main(&$objResponse, &$objBill){
	$json = new Services_JSON();
	
	//call getPersonInformation
	$jsPerson = getPersonInformation($objResponse, $objBill);
	$JSONPerson = $json->encode($jsPerson);
	$objResponse->assign("personInfo", "value", $JSONPerson);
	$objResponse->alert("personInfo = ".$JSONPerson);	//alert
	
	//call getAccommodation function 
	$jsAcc= getAccommodation($objResponse, $objBill);
	$JSONAcc = $json->encode($jsAcc);
	$objResponse->assign("acc", "value", $JSONAcc);
	$objResponse->alert("accommodation = ".$JSONAcc);	//alert
	 
	//call getHospitalServices function 
	$jsHospitalServices = getHospitalServices($objResponse, $objBill);
	$JSONHospitalServices = $json->encode($jsHospitalServices);
	$objResponse->assign("hospitalServices", "value", $JSONHospitalServices);
	#$objResponse->alert("hospitalServices = ".$JSONHospitalServices);
	
	//call getMedicines
	$jsMed = getMedicines($objResponse, $objBill);
	$JSONMedicine = $json->encode($jsMed);
	$objResponse->assign("medicines", "value", $JSONMedicine);
	#$objResponse->alert("medicines = " .$JSONMedicine); //alert
	 
	//call getMedicalSupplies
	$jsMedSup = getMedicalSupplies($objResponse, $objBill);
	$JSONMedSupplies = $json->encode($jsMedSup);
	$objResponse->assign("medSupplies", "value", $JSONMedSupplies);
	#$objResponse->alert(" medical supplies = ". $JSONMedSupplies); //alert
	
	//call getProfFee
	
	
}//end of function main


function getPersonInformation(&$objResponse, &$objBill){ 
	global $db; 
	
	$PersonInfoArray = array();
	//sql statement to retrieve patient information 
	$sql = "SELECT ce.encounter_nr , ce.pid , cp.name_first, cp.name_middle, cp.name_last,  " .
			 "\n 	cp.street_name, sb.brgy_name, sm.mun_name, sp.prov_name, sm.zipcode, ".
			 "\n    ce.admission_dt ".
			 "\n 	FROM care_encounter AS ce ".
			 "\n 		INNER JOIN care_person AS cp ON cp.pid = ce.pid ".
			 "\n 	    	INNER JOIN seg_barangays AS sb ON sb.brgy_nr = cp.brgy_nr ".
			 "\n 	        	INNER JOIN seg_municity AS sm ON sm.mun_nr = sb.mun_nr ".
			 "\n	               INNER JOIN seg_provinces AS sp ON sp.prov_nr = sm.prov_nr ".			
			 "\n	WHERE ce.encounter_nr ='".$objBill->current_enr."'";
	
	if($result = $db->Execute($sql)){
		if($result->RecordCount()){
			$row = $result->FetchRow();
						
			$name = strtoupper($row['name_last']).", ".strtoupper($row['name_first'])." ".strtoupper($row['name_middle']);
			$address = strtoupper($row['street_name']).", ".strtoupper($row['brgy_name']).", ".strtoupper($row['mun_name']) .
						", ".strtoupper($row['prov_name']); 
			
			$PersonInfoArray[]= array("encounter"=> $row['encounter_nr'] ,
					"pid"=> $row['pid'], 
					"name" => $name, 
					"address" => $address, 
					"admission_dt" => $row['admission_dt']				 				
					
			);
			
		}else{
			$objResponse->alert("No record found.");
		}	
	}else{
		$objResponse->alert("sql error: ".$sql);
	}
	
	return $PersonInfoArray;
}//end of function getPersonInformation

function getAccommodation(&$objResponse, &$objBill){
	//$accArrayList = array();
	$accArray = array();
	
	//Set Accommodation history
	$objBill->getAccommodationHist();
	//Set room type benefits of the patient
	$objBill->getRoomTypeBenefits();
	//Set confie benefits as accommodation
	$objBill->getConfineBenefits('AC');
	
	$accHistArray = array();
	//get accommodation object 
	$accHistArray = $objBill->accommodation_hist;
	//get accommodation benefits coverage
	$accBenefitsArray = $objBill->acc_roomtype_benefits;
	//get total confine coverage
	$totalConfineCoverage = $objBill->acc_confine_coverage;
	/*	
	$total = 0;
	if(is_array($accHistArray)){
		foreach($accHistArray as $key => $acchist){
			foreach($accBenefitsArray as $bkey => $accben){
				if(($acchist->type_nr) == ($accben->type_nr)){
					$total_charge = round($accben->getActualCharge(), 0);
					$total_coverage = round($accben->getTotalCoverage(), 0);
					$day_count = $accben->days_count;
					$excess_hr = $accben->days_cout;
					
					$total += $total_charge;
				}
			}
			$typeDesc = $acchist->getTypeDesc();
			$type_nr = $acchist->type_nr;
			$roomRate = round($acchist->getRoomRate(), 0);
			$roomNr = $acchist->getRoomNr();
			
			//if(count($accHistArray)>=-1){			
				$accArrayList = array("type-nr" => $type_nr,
									  "typDesc"=> $typeDesc, 
									  "roomRate"=> $roomRate, 
									  "roomNr"=> $roomNr							
								
				);
			
			//}
						
		}//end foreach statement
	}//end if statement
	*/
	//$excessValue = $total_charge - $totalConfineCoverage;
	
	//$accArrayTotal = array("accTotal"=> $total, 
	//					   "accHealthcare"=> $totalConfineCoverage, 
	//					   "accExcess"=> $excessValue
										
	//);
	
	//$accArrayTotalCoverage['totalConfineCoveragex'] = $totalConfineCoverage;
	
	//$accHistArray[0] = $totalConfineCoverage;
	$accArray= array_merge( $accHistArray , $accBenefitsArray);
	//$objResponse->alert("hello mark = ". print_r($accArray, true));	
	
	return $accArray;
	
}// end of function getAccommudatin 

function getHospitalServices(&$objResponse, &$objBill){
	
	$objBill->getServicesList();
	$objBill->getServiceBenefits();
	$objBill->getConfineBenefits('HS');
	
	$srvConfineBenefits = $objBill->srv_confine_benefits;
	$hsServiceBenefits = $objBill->hsp_service_benefits;
	
	$totalSrvConfineCoverage = $objBill->serv_confine_coverage;
	$totalSrvCharge = $objBill->getTotalSrvCharge();
	
	//Note check this for multiple services ( subject to modification) Nov. 20, 2007 
	/*if(is_array($hsServiceBenefits)){
		foreach($hsServiceBenefits as $key => $hsValue){
			$srvPrice = $hsValue->getServPrice();
			$srvCharge = $hsValue->getServCharge();
			$srvCode = $hsValue->getServiceCode();
			$srvDesc = $hsValue->getServiceDesc();
			
			//hidden input for service code 
			$objResponse->insertInputAfter("inputExcess", "hidden", "hscode-".$srvCode, "inputhscode-".$srvCode);
			$objResponse->assign("inputhsdesc-".$srvCode, "value", $srvCode);
			//hidden input for service description
			$objResponse->insertInputAfter("inputhscode-".$srvCode, "hidden", "hsdesc-".$srvCode, "inputhsdesc-".$srvCode);
			$objResponse->assign("inputhsdesc-".$srvCode, "value", $srvDesc);
			//hidden input for service charge
			$objResponse->insertInputAfter("inputhsdesc-".$srvCode , "hidden", "hs-".$srvCode, "inputHS-".$srvCode);
			$objResponse->assign("inputHS-".$srvCode, "value", $srvCharge);
		}	
	}
	*/
	/*
	$excessValue = $totalSrvCharge - $totalSrvConfineCoverage;
	//hidden Total Actual Charge for hospital service
	$objResponse->insertInputAfter("inputExcess", "hidden", "hsAP", "inputTotalHsAP");
	$objResponse->assign("inputTotalHsAP", "value", $totalSrvCharge);
	//hidden Total service confine coverage
	$objResponse->insertInputAfter("inputTotalHsAP", "hidden", "hsHC", "inputTotalHsHC");
	$objResponse->assign("inputTotalHsHC", "value", $totalSrvConfineCoverage);
	//hidden Total excess
	$objResponse->insertInputAfter("inputTotalHsHC", "hidden", "hsEX", "inputTotalHsEX");
	$objResponse->assign("inputTotalHsEX", "value", $excessValue);
	*/
	
	$hsTotalSrvCharge = array();
	if($totalSrvConfineCoverage == NULL) $totalSrvConfineCoverage = 0;
	$hsTotalSrvCharge = array("totalSrvCharge"=> $totalSrvCharge, "totalSrvConfineCoverage"=> $totalSrvConfineCoverage);
	$hsArray = array();
	$hsArray[] = array_merge($hsServiceBenefits, $srvConfineBenefits, $hsTotalSrvCharge);
	
	return $hsArray;
	
}//end of function getHospitalServices

function getMedicines(&$objResponse, &$objBill){
	//list all medicines consume by the patient
	$objBill->getMedicinesList();
	//Set compensible to healthcase insurance that covered this benefits
	$objBill->getMedicineBenefits();
	//Set confine benefits to medical supplies as medicines (M)
	$objBill->getConfineBenefits('MS', 'M');
	
	//$medArray = $objBill->med_product_benefits;
	$medProductsBenefits = $objBill->med_product_benefits;
	
	/*
	if(is_array($medArray)){
		foreach( $medArray as $key => $medValue){
			$medBestellNum = $medValue->bestellnum;
			$medDesc = $medValue->artikelname;
			$medAccPrice = $medValue->item_charge;
			$medPricePerItem = $medValue->item_price;
			$medExcess = $medAccPrice - $medPricePerItem;  
		}
	}*/
	/*
	//total medicine charges
	$totalMedCharge = $objBill->getTotalMedCharge();
	//total Medicine coverage
	$totalMedConfineCoverage = $objBill->med_confine_coverage;
	//excess medicine 
	$totalExcess = $totalMedCharge - $totalMedConfineCoverage;
	//hidden input for total medicine
	$objResponse->insertInputAfter("inputTotalHsEX", "hidden", "medAP", "inputTotalMedAP");
	$objResponse->assign("inputTotalMedAP", "value", $totalMedCharge);
	//hidden input for healthcare coverage
	$objResponse->insertInputAfter("inputTotalMedAP","hidden", "medHC", "inputTotalMedHC");
	$objResponse->assign("inputTotalMedHC", "value", $totalMedConfineCoverage);
	//hidden input for excess
	$objResponse->insertInputAfter("inputTotalMedHC","hidden", "medEX", "inputTotalMedEX");
	$objResponse->assign("inputTotalMedEX", "value", $totalExcess);
	*/	
	$totalMedCharge = $objBill->getTotalMedCharge();
	$totalMedConfineCoverage = $objBill->med_confine_coverage;
	$totalExcess = $totalMedCharge - $totalMedConfineCoverage;
	
	$medArray = array();	
	$medTotalArray[0] = array("totalMedCharge"=> $totalMedCharge, 
							"totalMedConfineCoverage"=> $totalMedConfineCoverage, 
							"totalExcess"=> $totalExcess);
	
	
	$medArray= array_merge($medProductsBenefits, $medTotalArray);
	
	return $medArray;	
}//end of function getMedicines


function  getMedicalSupplies(&$objResponse, &$objBill){
	$objBill->getSuppliesList();
	$objBill->getSupplyBenefits();
	$objBill->getConfineBenefits('MS', 'S');
	
	//total supplies cofine coverage
	$totalSupConfineCoverage = $objBill->sup_confine_coverage;
	//total supplies charge to the patient
	$totalSupCharge = $objBill->getTotalSupCharge();
	//supples excess
	$totalExcess = $totalSupCharge - $totalSupConfineCoverage;
	/*
	//hidden input for total supplies charge
	$objResponse->insertInputAfter("inputTotalMedEX", "hidden", "supAP", "inputSupAP");
	$objResponse->assign("inputSupAP", "value", $totalSupCharge);
	//hidden input for health care coverage
	$objResponse->insertInputAfter("inputSupAP", "hidden", "supHC", "inputSupHC");
	$objResponse->assign("inputSupHC", "value", $totalSupConfineCoverage);
	//hidden input for supplies excess
	$objResponse->insertInputAfter("inputSupHC", "hidden", "supEX", "inputSupEX");
	$objResponse->assign("inputSupEX", "value", $totalExcess);
	*/
	
	$medSuppliesArray = array();
	$medSuppliesArray[] = array("totalSupConfineCoverage"=>$totalSupConfineCoverage, 
							  "totalSupCharge"=>$totalSupCharge, 
							  "totalExcess"=> $totalExcess);		
	
	return $medSuppliesArray;
	
} //end of function getMedicalSupplies


function getProfessionalFee(&$objResponse, &$objBill){
	$objBill->getProfFeesList();
	$objBill->getProfBenefits();
	
	$hsp_pfs_benefits = $objBill0>hsp_pfs_benefits;
	
	
}// end of function getProfessionalFee

function getDiscounts(&$objResponse, &$objBill){
	
}//end of function getDiscounts

function getTotalBillCharges(&$objResponse, &$objBill){

}// end of function getTotalBillCharges 


$xajax->processRequest();
?>