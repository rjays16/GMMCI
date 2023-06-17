<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/ic_lab/ajax/iclab-request-new.common.php');

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path.'include/care_api_classes/class_access.php');

function populateRequestListByRefNo($refno=0,$ref_source='LB',$fromSS=0, $discount=0, $discountid=''){
		global $db;
		$objResponse = new xajaxResponse();
		$srvObj=new SegLab();
		if (!$discount)
			$discount = 0;

		#$objResponse->alert('refno = '.$ref_source);
		$rs = $srvObj->getAllLabInfoByRefNo($refno, $ref_source,$fromSS, $discount, $discountid);

		#$objResponse->addAlert('sql = '.$srvObj->sql);

		if ($rs){
			while($result=$rs->FetchRow()) {
				$name = $result["name"];
				if (strlen($name)>40)
					$name = substr($result["name"],0,40)."...";
				#$objResponse->addAlert('dsprice = '.$result['discounted_price']);
				#$objResponse->addAlert('name = '.$result['request_flag']);
				$objResponse->addScriptCall("initialRequestList",$result['service_code'],$result['group_code'],
											$name, stripslashes($result['clinical_info']), $result['request_doctor'],
											$result['request_doctor_name'], $result['is_in_house'], $result['price_cash_orig'],
											$result['price_charge'],$result['hasPaid'],$result['is_socialized'],
											$result['approved_by_head'],$result['remarks'],$result['quantity'],number_format($result['discounted_price'], 2, '.', ''),$result['request_dept'],$result['request_flag']);
			}
		}else{
			$objResponse->addScriptCall("emptyIntialRequestList");
		}
		$objResponse->addScriptCall("refreshDiscount");
		return $objResponse;
	}# end of function populateRequestListByRefNo

function existSegOverrideAmount($ref_no){
		global $db;

		if (!$ref_no)
			return FALSE;

		$sql="SELECT *	FROM seg_override_amount
					WHERE ref_no='".$ref_no."' AND ref_source='LD'";

		if ($buf=$db->Execute($sql)){
			if($buf->RecordCount()) {
				return TRUE;
			}else { return FALSE; }
		}else { return FALSE; }
	}#end of function existSegCharityAmount

#added by VAN 08-11-2010
function updateRequest($usr, $pw, $refno, $discount_given){
	 global $db, $HTTP_SESSION_VARS;
	 $objResponse = new xajaxResponse();
	 $user=& new Access($usr,$pw);

	 if($user->isKnown()&&$user->hasValidPassword()&&$user->isNotLocked()){

			if ($HTTP_SESSION_VARS['sess_user_personell_nr'])
				$personnel_nr = $HTTP_SESSION_VARS['sess_user_personell_nr'];
			elseif ($HTTP_SESSION_VARS['sess_temp_personell_nr'])
				$personnel_nr = $HTTP_SESSION_VARS['sess_temp_personell_nr'];

			$grand_dte =  date('Y-m-d H:i:s');
			$ref_source = 'LD';

			/*if (existSegOverrideAmount($refno)){
				$sql="UPDATE seg_override_amount
						SET grant_dte=NOW(), personnel_nr=".$personnel_nr.", amount=".$discount_given."
						WHERE ref_no='".$refno."' AND ref_source='".$ref_source."'";
			}else{*/
				$sql = "INSERT INTO seg_override_amount (ref_no, ref_source, grant_dte, personnel_nr, amount) ".
					 "\n VALUES('".$refno."', '".$ref_source."', '".$grand_dte."', '".$personnel_nr."' , '".$discount_given."' )";
			#}

			#$db->StartTrans();
			$ok = $db->Execute($sql);
			#$objResponse->alert($sql);
			if ($ok){
				#$db->CommitTrans();
				$objResponse->alert('Request has been successfully granted');
				$objResponse->addScriptCall('submitform');
			}else{
				#$db->RollbackTrans();
				$objResponse->alert('Saving Data failed');
			}

	 }else{
		 $objResponse->alert('Your login or password is wrong');
	 }

	 return $objResponse;
}

$xajax->processRequests();
?>