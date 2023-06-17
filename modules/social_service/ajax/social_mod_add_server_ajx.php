<?php

require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
#include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_social_service.php');
require_once($root_path.'modules/social_service/ajax/social_mod_add_common_ajx.php');


function processForm($aFormValues){
	if(array_key_exists("mod_code",$aFormValues )){
		return saveData($aFormValues);
	}
} //end of function processForm

function saveData($aFormValues){
	global $db;
	$objResponse = new xajaxResponse();
	$bError = false;
	
	#$objResponse->alert("saveData save = ".print_r($aFormValues));
	$objSocial = new SocialService;
	
	if(trim($aFormValues['mod_code']) == 0){
		$objResponse->alert("Please select a modifier.");
		$bError = true;
	}elseif(trim($aFormValues['mod_desc']) == ""){
		$objResponse->alert("Please fill the description of the modifier.");
		$bError = true;
	}
	
	#$objResponse->alert("1".print_r($_POST, true));
	
	if(!$bError){
		switch($aFormValues['mode']){
			case "add": 
				$_POST['mod_code']= trim($aFormValues['mod_code'][0]);
				
				$lastcode = $objSocial->getLastSubMod($_POST['mod_code']);
					
				$code = $lastcode['subcode']+1;
				$_POST['mod_subcode'] = $_POST['mod_code'].".".$code;
				$_POST['mod_subdesc'] = $aFormValues['mod_desc'];
								
				#$objResponse->alert("code = ".$_POST['mod_code']."\n subcod = ".$_POST['mod_subcode']."\n desc = ".$_POST['mod_subdesc']);
				$bolSuccess = $objSocial->addModifierData($_POST); 
			break;
			
			case "update":
				$_POST['mod_subdesc'] = $aFormValues['mod_desc'];
				$_POST['mod_code']= trim($aFormValues['mod_code'][0]);
				$_POST['mod_subcode']= trim($aFormValues['mod_subcode']);
				
				$sql = "UPDATE seg_social_service_submodifiers SET 
							mod_subdesc ='".$_POST['mod_subdesc']."'  
						WHERE mod_code = '".$_POST['mod_code']."'
						AND mod_subcode LIKE '".$_POST['mod_subcode']."'";
				
				if($db->Execute($sql)){
					$bolSuccess = true;
					#$objResponse->alert($sql);	
				}else{
					$objResponse->alert("error-".$sql);	
				}
			break;	
		}
		
		if($bolSuccess){
			//$objResponse->script("window.parent.windowClose();");
			$objResponse->script("javascript:self.parent.location.href=self.parent.location.href;");			
		}else{
			$objResponse->alert("Saving data failed .. sql->".$objSocial->sql);
		}
		
	}
		
	return $objResponse;
}//end of function saveData

function refresh(){
	$objResponse = new xajaxResponse();
	$objResponse->script("window.parent.popWinClose()");	
	
	return $objResponse;
}

function updateData(){

} //end of function updateData

$xajax->processRequest();

?>