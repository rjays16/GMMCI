<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/clinics/ajax/lab-request-new.common.php');

/*
function setCharityDiscounts($ref_no,$sw_nr,$amount){
	global $db;
	$objResponse = new xajaxResponse();
	
	$grand_dte =  date('Y-m-d H:i:s');
	$ref_source = 'LD';
	
	$sql = "INSERT INTO seg_charity_amount (ref_no, ref_source, grant_dte, sw_nr, amount) ".
		 "\n VALUES('".$ref_no."', '".$ref_source."', '".$grand_dte."', '".$sw_nr."' , '".$amount."' )";
		
	if($db->Execute($sql)){
		$objResponse->addAlert("Successfully save data.");
	}else{
		$objResponse->addAlert("ErrorMsg : ".$sql); 
	}
						
	return $objResponse;
}// edn of setCharityDiscounts
*/

#-----------added by VAN 10-30-07----------
function checkIfalreadyPaid_Granted($refno, $service_code){
	global $db;
	
	$objResponse = new xajaxResponse();	
		
	$sql = "SELECT ref_no AS refno, ref_source, service_code FROM seg_pay_request
			  WHERE ref_source = 'LD'
			  AND ref_no = '".$refno."'
			  AND service_code = '".$service_code."'
			  UNION
			  SELECT ref_no AS refno, ref_source, service_code FROM seg_granted_request
			  WHERE ref_source = 'LD'
			  AND ref_no = '".$refno."'
			  AND service_code = '".$service_code."'";
	#$objResponse->addAlert($sql);		  
	$res = $db->Execute($sql);
	$row=$res->RecordCount();
	if ($row==0){
		$objResponse->addAlert("The request is not yet paid or not in the list of the granted \n request.  Please  settle  this  request accounts.  Thank you...");
		$objResponse->addScriptCall("getBill_Status",0);								
	}else{
		$objResponse->addScriptCall("getBill_Status",1);								
	}		  
	
		
	return $objResponse;
}

function existSegCharityAmount($ref_no){
		global $db;

		if (!$ref_no)
			return FALSE;
	
		$sql="SELECT *	FROM seg_charity_amount
					WHERE ref_no='".$ref_no."' AND ref_source='LD'";

		if ($buf=$db->Execute($sql)){
			if($buf->RecordCount()) {
				return TRUE;
			}else { return FALSE; }
		}else { return FALSE; }		
	}#end of function existSegCharityAmount

#--------updated by VAN 10-30-07-----------
function setCharityDiscounts($ref_no,$sw_nr,$amount){
		global $db;
		$objResponse = new xajaxResponse();
		
		$grand_dte =  date('Y-m-d H:i:s');
		$ref_source = 'LD';

		if (existSegCharityAmount($ref_no)){
			$sql="UPDATE seg_charity_amount
					SET grant_dte=NOW(), sw_nr=".$sw_nr.", amount=".$amount."
					WHERE ref_no='".$ref_no."' AND ref_source='LD'";
		}else{
			$sql = "INSERT INTO seg_charity_amount (ref_no, ref_source, grant_dte, sw_nr, amount) ".
				 "\n VALUES('".$ref_no."', '".$ref_source."', '".$grand_dte."', '".$sw_nr."' , '".$amount."' )";
		}			
		if($db->Execute($sql)){
			$objResponse->addAlert("Successfully save data.");
		}else{
			$objResponse->addAlert("ErrorMsg : ".$sql); 
		}
							
		return $objResponse;
	}// edn of setCharityDiscounts
	
function getCharityDiscounts($refno=''){
	global $db;
	$objResponse = new xajaxResponse();
	
	$sql = "SELECT * FROM seg_charity_amount WHERE ref_no='".$refno."'";
	
	if($result = $db->Execute($sql)){
		if($result->RecordCount()){
			//$objResponse->addAlert("hello world1 =".print_r($row, true));
			$row = $result->FetchRow();
			$amount = sprintf('%01.2f', $row['amount']);
			$objResponse->addScriptCall("eDiscount",$amount , true); 
			
		}else{
			#$objResponse->addAlert("No record found");
			$objResponse->addScriptCall("eDiscount", 0 ,false);
		}
	}
	
	return $objResponse;
}// end of getCharityDiscounts

#---------added by VAN 12-20-07---
/*
function checkIfhasResult($parent_refno){
	global $db;
	$srv=new SegLab;

	$objResponse = new xajaxResponse();
	
	$hasResult=$srv->hasResult($parent_refno);
	$objResponse->addScriptCall("ValidatehasResult",$hasResult);
	
	return $objResponse;
}
*/
#--------------------------------

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$xajax->processRequest();
?>