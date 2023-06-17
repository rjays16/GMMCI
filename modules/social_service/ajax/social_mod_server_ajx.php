<?php

function refresh(){
	$objResponse = new xajaxResponse();
	$objResponse->script("cClick()");
	return $objResponse;
}

function deleteData($code, $mod){
	$objResponse = new xajaxResponse();
	$objSocial = new SocialService;
	
	if($code){
		$isUsed = $objSocial->modifierIsused($code);
		#$objResponse->alert("used = ".$isUsed);
		
		if ($isUsed){
			$objResponse->alert("Modifier can not be deleted. It is already been used.");
		}else{	
			if($objSocial->deleteModifierData($code, $mod)){
				$objResponse->alert("Modifier is successfully deleted");
				$objResponse->addScriptCall("removeRow",$code);
				$objResponse->script("javascript:self.location.href=self.location.href;");
			}else{
				$objResponse->alert("Failed to delete data : sql->".$objSocial->sql);
			}
		}	
		#$objResponse->alert($objSocial->sql);
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
	
	#$objResponse->alert($modifier);
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

require_once($root_path.'modules/social_service/ajax/social_mod_common_ajx.php');


$xajax->processRequest();

?>