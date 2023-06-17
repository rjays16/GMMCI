<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
//require_once($root_path.'include/care_api_classes/class_social_service.php');
require_once($root_path.'modules/social_service/ajax/social_srv_discount_common_ajx.php');


function saveDiscount(){
	global $db;
	$objResponse = new xajaxResponse();
	
	$sql = "INSERT INTO seg_charity_amount(refno, ref_source, grand_dte,sw_nr, amount) ".
		"\n VALUES('".$refno."', '".$ref_source."', '".$grand_dte."', '".$sw_nr."', '".$amount."')";
	
	if($db->Execute($sql)){
		$objResponse->alert("discount save");
	}else{
		
	}
	
	return $objResponse;
}



$xajax->processRequest();
?>