<?php
#edited by daryl
#put parameter $walkin
function populateRequestListByRefNo($refno=0,$batch_nr=0,$fromSS=0, $discount=0, $discountid='',$walkin=''){
		global $db;
		$objResponse = new xajaxResponse();
		$radio_obj=new SegRadio();

		if (!$discount)
			$discount = 0;
        #edited by daryl
        $rs = $radio_obj->getAllRadioInfoByRefNo($refno, $batch_nr, $fromSS, $discount, $discountid,'', $walkin);

		if ($rs){
			while($result=$rs->FetchRow()) {
				$name = $result["name"];
				if (strlen($name)>40)
					$name = substr($result["name"],0,40)."...";
#edited by daryl
				$objResponse->call("initialRequestList",$result['service_code'],$result['dept_short_name'],
											$name, stripslashes($result['clinical_info']), $result['request_doctor'],
											$result['request_doctor_name'], $result['is_in_house'], $result['price_cash_orig'],
											$result['price_charge'],$result['hasPaid'],$result['is_socialized'], $result['parent_batch_nr'],
											$result['approved_by_head'],$result['remarks'],$result['quantity'],number_format($result['discounted_price'], 2, '.', ''),
                                            $result['request_dept'],$result['request_flag'], $result['or_number'],$result['group_code'],$result['price_alter']);
			}
		}else{
			$objResponse->call("emptyIntialRequestList");
		}
		$objResponse->call("refreshDiscount");
		return $objResponse;
}# end of function populateRequestListByRefNo

function existSegOverrideAmount($ref_no){
		global $db;

		if (!$ref_no)
			return FALSE;

		$sql="SELECT *	FROM seg_override_amount
					WHERE ref_no='".$ref_no."' AND ref_source='RD'";

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
	 $user= new Access($usr,$pw);

	 if($user->isKnown()&&$user->hasValidPassword()&&$user->isNotLocked()){

			if ($HTTP_SESSION_VARS['sess_user_personell_nr'])
				$personnel_nr = $HTTP_SESSION_VARS['sess_user_personell_nr'];
			elseif ($HTTP_SESSION_VARS['sess_temp_personell_nr'])
				$personnel_nr = $HTTP_SESSION_VARS['sess_temp_personell_nr'];

			$grand_dte =  date('Y-m-d H:i:s');
			$ref_source = 'RD';

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
				$objResponse->call('submitform');
			}else{
				#$db->RollbackTrans();
				$objResponse->alert('Saving Data failed');
			}

	 }else{
		 $objResponse->alert('Your login or password is wrong');
	 }

	 return $objResponse;
}

#added by Francis L.G 02-05-2013
function chkCLhis($pid,$refno=0,&$radService,$submit){
        global $db;
        $objResponse = new xajaxResponse();
        $radio_obj = new SegRadio();

        if (!$pid)
            return FALSE;

        $counter = count($radService);

        if($radService){

             for($i=0;$i<$counter;$i++){

                $radServ = $radService[$i];

                //$radServ = "RECON 3D";
                $radGrpInfo = $radio_obj->getRadioServiceGroupInfo($radServ);
                if($radGrpInfo){
                    $grpCode = $radGrpInfo['name'];
                }
                

                if($refno){
                    $sqlCt = "SELECT * FROM seg_radio_ct_history
                            WHERE pid='$pid' AND refno='$refno' AND group_code='$grpCode'";
                     $sqlMri = "SELECT * FROM seg_radio_mri_history
                             WHERE pid='$pid' AND refno='$refno' AND group_code='$grpCode'";  
                }
                else{
                    $sqlCt = "SELECT * FROM seg_radio_ct_history
                            WHERE pid='$pid' AND refno='0' AND group_code='$grpCode'";
                    $sqlMri = "SELECT * FROM seg_radio_mri_history
                            WHERE pid='$pid' AND refno='0' AND group_code='$grpCode'";
                }

                 $bufCt = $db->Execute($sqlCt);
                 $bufMri = $db->Execute($sqlMri);

                if( (!$bufCt->RecordCount()) && (!$bufMri->RecordCount()) ){
                    $submit = 0;
                }

             }
        }
                    
        if($submit==1){
            $objResponse->call('submitAllow');
        }else{
            $objResponse->call('submitDisable');
        }                  
        
       return $objResponse; 
    }

#added by VAS 03-26-2012
function updateCoverage($enc_nr, $type, $nr=null) {
    global $db;

    $objResponse = new xajaxResponse();
    $amount = 0;
    
    if ($enc_nr) {
        if ($type=='phic') {
            $bill_date = strftime("%Y-%m-%d %H:%M:%S");
            
            $bc = new Billing($enc_nr, $bill_date);

            $bc->getConfinementType();
            $amount = 0;

            define('__HCARE_ID__',18);

            $total_coverage = $bc->getActualSrvCoverage(__HCARE_ID__);
            $total_benefits = $bc->getConfineBenefits('HS', NULL, 0, TRUE);
            #$total_benefits = 2240;
            $covered = 0;
            
            if ($nr)
            {
                $query = "SELECT SUM(quantity*price_charge) FROM care_test_request_radio WHERE refno=".$db->qstr($nr);
                /*$query = "SELECT SUM(d.quantity*d.price_cash) 
                            FROM seg_lab_servdetails d
                            INNER JOIN seg_lab_serv h ON h.refno=d.refno
                            WHERE h.ref_source='SPL' AND refno=".$db->qstr($nr);*/
                #$objResponse->alert($query);            
                $covered = (float) $db->GetOne($query);
            }
            
            $objResponse->assign('coverage','value', (float)$total_benefits-(float)$total_coverage+$covered);
            $objResponse->call('refreshTotal');
        }elseif ($type=='LINGAP') {
            $lc = new SegLingapPatient();
            $pid = $db->GetOne("SELECT pid FROM care_encounter WHERE encounter_nr=".$db->qstr($enc_nr));
            $amount = $lc->getBalance($pid);
            $objResponse->assign('coverage','value', $amount);
            $objResponse->call('refreshTotal');
        }
        elseif ($type=='CMAP') {
            $amount = 0;
            $pc = new SegCMAPPatient();
            $pid = $db->GetOne("SELECT pid FROM care_encounter WHERE encounter_nr=".$db->qstr($enc_nr));
            $amount = $pc->getBalance($pid);

            $objResponse->assign('coverage','value', $amount);
            $objResponse->call('refreshTotal');
        }
        else {
            $objResponse->assign('cov_type','innerHTML', '');
            $objResponse->assign('cov_amount','innerHTML', '');
            $objResponse->assign('coverage','value', -1);
            $objResponse->call('refreshTotal');
        }

    }
    else
        $objResponse->assign('cov_amount','innerHTML', '');
    return $objResponse;
}

//added by maimai
function getTotalCharge($encounter_nr){
    $objResponse = new xajaxResponse();
    $radObj = new SegRadio();

    $result = $radObj->getTotalChargedServ($encounter_nr);
    $total_charge = 0;
    
    while($row = $result->FetchRow()){
        $total_charge = $row['total_charge'];
    }

    $objResponse->assign('total_charge', 'innerHTML', number_format($total_charge, 2));

    return $objResponse;
}

function getInsurances($pid){
    $objResponse = new xajaxResponse();
    $insObj = new PersonInsurance();

    $result = $insObj->getPersonInsuranceItems($pid);
    if($result){
        while($row=$result->FetchRow()){
            $ins->id=$row['hcare_id'];
            $ins->name=$row['firm_id'];

            $objResponse->call("appendInsurance", $ins);
        }
    }

    return $objResponse;
}
//end added by maimai

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/radiology/ajax/radio-request-new.common.php');

require_once($root_path.'include/care_api_classes/class_radiology.php');
require_once($root_path.'include/care_api_classes/class_access.php');
require($root_path.'include/care_api_classes/class_insurance.php');
#added by VAS 03-26-2012
require_once($root_path."include/care_api_classes/billing/class_billing.php");
require_once($root_path."include/care_api_classes/sponsor/class_lingap_patient.php");
require_once($root_path."include/care_api_classes/sponsor/class_cmap_patient.php");

$xajax->processRequest();
?>