<?php

require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
#include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_social_service.php');
require_once($root_path.'modules/social_service/ajax/social_add_common_ajx.php');


function processForm($aFormValues){
	if(array_key_exists("service_code",$aFormValues )){
		return saveData($aFormValues);
	}
} //end of function processForm

function saveData($aFormValues){
	global $db;
	$objResponse = new xajaxResponse();
	$bError = false;
	
	#$objResponse->alert("saveData save = ".print_r($aFormValues));
	$objSocial = new SocialService;
	
	if(trim($aFormValues['service_code']) == ""){
		$objResponse->alert("Please enter social service code.");
		$bError = true;
	}
	if(trim($aFormValues['service_desc']) == ""){
		$objResponse->alert("Please fill the description of the code.");
		$bError = true;
	}
	if(trim($aFormValues['service_discount']) == ""){
		$objResponse->alert("Please fill the discount. ");
		$bError = true;
	}	
			
	$is_forallValue = ($aFormValues['is_forall'])? '1':'0';
	
	$_POST['discountdesc']= trim($aFormValues['service_desc']);
	$_POST['discount'] = ($aFormValues['service_discount']/100);
	$_POST['is_forall'] = $is_forallValue;
	
	#$objResponse->alert("1".print_r($_POST, true));
	
	if(!$bError){
		switch($aFormValues['mode']){
			case "add": 
				$_POST['discountid']= trim($aFormValues['service_code']);	
				$_POST['create_id'] = $aFormValues['encoder'];
				$_POST['create_timestamp'] = date('Y-m-d H:i:s');
				#$objResponse->alert("service_code = ".$_POST['service_code']."\n create_id = ".$_POST['create_id']."\n create_time = ".$_POST['create_time']);
				$bolSuccess = $objSocial->addSServiceData($_POST); 
			break;
			case "update":
				$_POST['discountid']= trim($aFormValues['service_code']); 
				$_POST['modify_id'] = $aFormValues['encoder'];
				$_POST['modify_timestamp'] = date('Y-m-d H:i:s');
				
				#$objResponse->alert("discount = ".$_POST['discount']. " \n service_code = ".$_POST['service_code']."\n modify_id = ".$_POST['modify_id']."\n modity_time = ".$_POST['modify_timestamp']);
				//$bolSuccess= $objSocial->updateSServiceData($_POST['discountid'],$_POST); 
				//update seg_discount set discountid='A', discountdesc='A',discount='0',  is_forall='0', modify_id ='Clarito Clarito', 
				//modify_timestamp ='2007-10-17 14:44:58' where discountid='A'

				$sql = "UPDATE seg_discount SET discountid='".$_POST['discountid']."',  discountdesc='".$_POST['discountdesc']."', discount='".$_POST['discount']."',  is_forall='".$_POST['is_forall']."' ,  ".
					 "\n modify_id ='".$_POST['modify_id']."',   modify_timestamp='".$_POST['modify_timestamp']."' ".
					 "\n WHERE discountid = '".$_POST['discountid']."'";
				
				if($db->Execute($sql)){
					$bolSuccess = true;
				}else{
					$objResponse->alert("error-".$sql);	
				}
				//$objResponse->alert(print_r($_POST, true));
				#$objResponse->alert($objSocial->sql);
			break;	
		}
		
		if($bolSuccess){
			$objResponse->script("window.parent.xajax_listRow();");
			//$objResponse->script("window.parent.windowClose();");
			//$objResponse->script("javascript:self.parent.location.href=self.parent.location.href;");			
		}else{
			$objResponse->alert("Saving data failed .. sql->".$objSocial->sql);
		}
		
	}
		
	return $objResponse;
}//end of function saveData

function refresh(){
	$objResponse = new xajaxResponse();
	$objResponse->script("window.parent.xajax_listRow();");
	//$objResponse->script("window.parent.popWinClose()");	
	
	return $objResponse;
}

function updateData(){

} //end of function updateData

$xajax->processRequest();

?>