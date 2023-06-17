<?php

//Populate row of social service classification
function listRow(){
	$objResponse = new xajaxResponse();
	$objSocial = new SocialService;
	
	$result =$objSocial->getSSInfo();
		
	if($result){
		$objResponse->addScriptCall("clearList", "sslistTable");	
		while($row = $result->FetchRow()){
			#$objResponse->addAlert("row service_code=".$row["discountid"]."\n service_desc=".$row["discountdesc"]." \n service_discount=".$row["discount"]." is_forall=".$row["is_forall"]);
			$discount = $row["discount"] * 100;
			$objResponse->addScriptCall("gui_AddRow",$row["discountid"],$row["discountdesc"],$discount, $row["is_forall"]);
					
		}
		#$objResponse->addAlert("success");
	}else{
		$tr = "<tr><td colspan=\"5\">No such person exists</td></tr>";	
		$objResponse->addAssign("sslistTable", "innerHTML", $tr);
	}
	
	$objResponse->script("cClick()");
	return $objResponse;
}

function refresh(){
	$objResponse = new xajaxResponse();
	$objResponse->script("cClick()");
	return $objResponse;
}

function deleteData($code, $rowNo){
	$objResponse = new xajaxResponse();
	$objSocial = new SocialService;
	
	if($code){
		//$objResponse->alert("success Deleted");
		if($objSocial->deleteSServiceData($code)){
			//$objResponse->alert("success Deleted");
			$objResponse->addScriptCall("removeRow",$rowNo);
		}else{
			$objResponse->alert("Failed to delete data : sql->".$objSocial->sql);
		}
	}
	return $objResponse;
} //end of function deleteData()

function updateData($code){
	$objResponse = new xajaxResponse();
		
	return $objResponse;
}

#----------added by VAN 07-05-08
function listModifierRow($modifier){
	$objResponse = new xajaxResponse();
	$objSocial = new SocialService;
	
	//$objResponse->alert($modifier);
	$result =$objSocial->getModifiers($modifier);
			
	if($result){
		$objResponse->addScriptCall("clearList2", "sslistTable");	
		#$alt = 1;
		while($row = $result->FetchRow()){
			#$objResponse->addAlert("row service_code=".$row["discountid"]."\n service_desc=".$row["discountdesc"]." \n service_discount=".$row["discount"]." is_forall=".$row["is_forall"]);
			#$discount = $row["discount"] * 100;
			#$objResponse->alert($alt);
			$objResponse->addScriptCall("gui_AddRow2",$row["mod_subcode"],$row["mod_subdesc"],$modifier);
			#if ($alt==2)
			#	$alt = 1;
			#else	
				#$alt++;		
		}
		#$objResponse->addAlert("success");
	}else{
		$tr = "<tr><td colspan=\"5\">No such modifiers</td></tr>";	
		$objResponse->addAssign("sslistTable", "innerHTML", $tr);
	}
	
	$objResponse->script("cClick()");
	return $objResponse;
}

#------------------------


require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path."include/care_api_classes/class_social_service.php");

require_once($root_path.'modules/social_service/ajax/social_common_ajx.php');


$xajax->processRequest();

?>