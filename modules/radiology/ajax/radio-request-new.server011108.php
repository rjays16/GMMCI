<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/radiology/ajax/radio-request-new.common.php');

require_once($root_path.'include/care_api_classes/class_radiology.php');

		/*
		*	burn created: August 31, 2007 
		*/
	function populateRequestListByRefNo($refno=0){
		global $db;
		$objResponse = new xajaxResponse();
		$objRadio = new SegRadio();

#$objResponse->addAlert("populateRequestListByRefNo : refno='".$refno."'");
		$rs = $objRadio->getAllRadioInfoByRefNo($refno);
#$objResponse->addAlert("populateRequestListByRefNo : objRadio->sql='".$objRadio->sql."'");
#		$objResponse->addAlert("populateRequestListByRefNo : rs : \n".print_r($rs,TRUE));
		if ($rs){
			while($result=$rs->FetchRow()) {
	#			$objResponse->addAlert("populateRequestListByRefNo : inside while loop : result : \n".print_r($result,TRUE));
#$objResponse->addAlert("populateRequestListByRefNo : result['hasPaid']='".$result['hasPaid']."'");
				$name = $result["name"];
				if (strlen($name)>40)
					$name = substr($result["name"],0,40)."...";
				$objResponse->addScriptCall("initialRequestList",$result['service_code'],$result['group_code'],
											$name, $result['clinical_info'], $result['request_doctor'],
											$result['request_doctor_name'], $result['is_in_house'], $result['price_cash_orig'], 
											$result['price_charge'],$result['hasPaid'],$result['is_socialized']);
			}
		}else{
			$objResponse->addScriptCall("emptyIntialRequestList");		
		}
//		$objResponse->addScriptCall("refreshTotal");
		$objResponse->addScriptCall("refreshDiscount");
		return $objResponse;
	}# end of function populateRequestListByRefNo

	function get_charity_discounts($nr=0) {
		
		
	} // end of get_charity_discounts
	
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
				//$objResponse->addAlert("No record found");
				$objResponse->addScriptCall("eDiscount", '' ,false);
			}
		}
		
		return $objResponse;
	}// end of getCharityDiscounts
	
		/*
		*	burn created: October 26, 2007
		*/
	function existSegCharityAmount($ref_no){
		global $db;

		if (!$ref_no)
			return FALSE;
	
		$sql="SELECT *	FROM seg_charity_amount
					WHERE ref_no='".$ref_no."' AND ref_source='RD'";

		if ($buf=$db->Execute($sql)){
			if($buf->RecordCount()) {
				return TRUE;
			}else { return FALSE; }
		}else { return FALSE; }		
	}#end of function existSegCharityAmount
	
	function setCharityDiscounts($ref_no,$sw_nr,$amount){
		global $db;
		$objResponse = new xajaxResponse();
		
		$grand_dte =  date('Y-m-d H:i:s');
		$ref_source = 'RD';

		if (existSegCharityAmount($ref_no)){
			$sql="UPDATE seg_charity_amount
					SET grant_dte=NOW(), sw_nr=".$sw_nr.", amount=".$amount."
					WHERE ref_no='".$ref_no."' AND ref_source='RD'";
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
	}// end of setCharityDiscounts
	

$xajax->processRequests();
?>