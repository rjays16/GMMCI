<?php
    function resetRefNo() {
        global $db;
        $objResponse = new xajaxResponse();
        
        $objclaim = new Claim();
        $lastnr = $objclaim->getLastNr(date("Y-m-d"));

        if ($lastnr)
            $objResponse->call("resetRefNo",$lastnr);
        else
            $objResponse->call("resetRefNo","ERROR!",1);
        return $objResponse;
    }  
    
    function setMemCategoryOptions($ncateg_id) {
        global $db;
        $objResponse = new xajaxResponse();    
        
        $strSQL = "select * from seg_memcategory order by memcategory_desc";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $objResponse->call("js_ClearOptions","category_list");
                $objResponse->call("js_AddOptions","category_list","-Select Category-", 0);
                while ($row = $result->FetchRow()) {
                    $objResponse->call("js_AddOptions", "category_list", $row['memcategory_desc'], $row['memcategory_id'], ($ncateg_id == $row['memcategory_id']));
                }
            }
        } else {
            $objResponse->alert("ERROR: Cannot retrieve membership categories ...");
        }
        
        return $objResponse;
    }
    
    function addClaimDet($s_encrs, $hcare_id) {
        global $db;
        
        $objResponse = new xajaxResponse();
        $objtransmit = new Transmittal();   
            
        $cases = "('".implode("','",$s_encrs)."')";        
        if (($cases) && ($cases != '')) {            
            $strSQL = "select ce.pid, cpi.insurance_nr, (case when isnull(sem.memcategory_id) then '' else sem.memcategory_id end) as categ_id, 
                           (case when isnull(sm.memcategory_desc) then 'NONE' else sm.memcategory_desc end) as categ_desc, 
                           (select concat(date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p'), ' to ', date_format(concat((case when discharge_date is null or discharge_date = '' then '0000-00-00' else discharge_date end), ' ', (case when discharge_time is null or discharge_time = '' then '00:00:00' else discharge_time end)), '%b %e, %Y %l:%i%p')) as prd 
                               from care_encounter as ce1 where ce1.encounter_nr = ce.encounter_nr) as confine_period, 
                           ce.encounter_nr, fn_get_pid_lastfirstmi(ce.pid) as full_name, is_denied, is_returned, is_paid                         
                        from (((care_encounter as ce inner join care_person_insurance as cpi on ce.pid = cpi.pid and cpi.hcare_id = $hcare_id) 
                                 inner join seg_encounter_insurance as sei on ce.encounter_nr = sei.encounter_nr and sei.hcare_id = $hcare_id) 
                                 left join (seg_encounter_memcategory as sem inner join seg_memcategory as sm on sem.memcategory_id = sm.memcategory_id) 
                                 on ce.encounter_nr = sem.encounter_nr) 
                                 left join
                                   (select scd.encounter_nr, 1 as is_denied, 0 as is_returned, 0 as is_paid 
                                       from seg_claim_denied as scd inner join seg_claim_posting as scp 
                                          on scd.ref_no = scp.ref_no 
                                       where scd.encounter_nr in $cases and scp.hcare_id = $hcare_id 
                                    union 
                                    select h.encounter_nr, 0 as is_denied, 0 as is_returned, 1 as is_paid 
                                       from 
                                    (select scph.ref_no, encounter_nr 
                                       from seg_claim_pay_hosp as scph inner join seg_claim_posting as scp 
                                          on scph.ref_no = scp.ref_no 
                                       where encounter_nr in $cases and scp.hcare_id = $hcare_id
                                     union 
                                     select scpp.ref_no, encounter_nr 
                                       from seg_claim_pay_patient as scpp inner join seg_claim_posting as scp 
                                          on scpp.ref_no = scp.ref_no 
                                       where encounter_nr in $cases and scp.hcare_id = $hcare_id
                                       union 
                                     select scpf.ref_no, encounter_nr 
                                       from seg_claim_pay_pf as scpf inner join seg_claim_posting as scp 
                                          on scpf.ref_no = scp.ref_no
                                       where encounter_nr in $cases and scp.hcare_id = $hcare_id
                                    ) as h 
                                    union 
                                    select scr.encounter_nr, 0 as is_denied, 1 as is_returned, 0 as is_paid 
                                       from seg_claim_returned as scr inner join seg_claim_posting as scp 
                                          on scr.ref_no = scp.ref_no 
                                       where scr.encounter_nr in $cases and scp.hcare_id = $hcare_id) as t on t.encounter_nr = ce.encounter_nr                                                                      
                        where ce.encounter_nr in $cases
                        order by discharge_date asc";                        
                                                                                                                            
            if ($result = $db->Execute($strSQL)) {            
                if ($result->RecordCount()) {
                    while ($row = $result->FetchRow()) {
                        $obj = (object) 'details';
                        $obj->enc_nr   = $row["encounter_nr"];      
                        $obj->patient  = $row["full_name"];
                        $obj->prd      = $row["confine_period"];
                        $obj->insurance_nr = $row["insurance_nr"];
                        if ($objtransmit->isPersonPrincipal($row["pid"],$hcare_id))
                            $obj->member   = $row["full_name"];
                        else 
                            $obj->member = $objtransmit->getFullNameOfMember($row["pid"],$hcare_id);                        
                        $obj->categ_desc   = $row["categ_desc"];
                        
                        $obj->status   = ($row["is_paid"] == 1) ? "PAID" : (($row["is_returned"] == 1) ? "RETURNED" : (($row["is_denied"] == 1) ? "DENIED" : "NONE"));
                        $obj->statcode = ($row["is_paid"] == 1) ? 1 : (($row["is_returned"] == 1) ? 2 : (($row["is_denied"] == 1) ? 3 : 0)); 
                                                
                        $objResponse->call("js_addclaimdet", $obj);
                        
                        if (!is_null($row["is_paid"]) && ($row["is_paid"] == 1)) {                            
                            $objResponse->call("postPayment", $row["encounter_nr"]);
                        }                                                
                    }                                
                }                        
            }
            else  
                $objResponse->alert("ERROR: ".$db->ErrorMsg());                      
        }
                   
        return $objResponse;
    }    
    
    function showClaimsPayment($enc_nr, $hcare_id) {                      
        $objResponse = new xajaxResponse();
        
        $objclaim = new Transmittal();
        $objpay = new Claim();                
        
        $objResponse->call("clearClaimPostDetails", "post_claim_details_".$enc_nr, 0);
        $cnt = 0;
        $tax = 0.02;
        if ($claimres = $objclaim->getClaimsDue($enc_nr, $hcare_id)) {
            if ($claimres->RecordCount()) {                                  
                while ($row = $claimres->FetchRow()) {
                    $obj = (object) 'paydetails';
                    $obj->priority = $row["priority"];
                    $obj->enc_nr   = $row["encounter_nr"];
                    $obj->pid      = $row["pid"];     
                    $obj->full_name = $row["full_name"];
                    $obj->acc_claim = $row["acc_claim"];
                    $obj->med_claim = $row["med_claim"];
                    $obj->sup_claim = $row["sup_claim"];
                    $obj->srv_claim = $row["srv_claim"];
                    $obj->ops_claim = $row["ops_claim"];
                    $obj->msc_claim = $row["msc_claim"];
                    $obj->d1_claim  = $row["d1_claim"];
                    $obj->d2_claim  = $row["d2_claim"]; 
                    $obj->d3_claim  = $row["d3_claim"]; 
                    $obj->d4_claim  = $row["d4_claim"]; 
                    $cnt++;
                    $obj->cnt = $cnt;
                    // Initialize holder of payments for claims.
                    $obj->acc_pay = 0;
                    $obj->med_pay = 0;
                    $obj->srv_pay = 0;
                    $obj->ops_pay = 0;
                    $obj->msc_pay = 0;
                    $obj->d1_pay  = 0;
                    $obj->d2_pay  = 0;
                    $obj->d3_pay  = 0;
                    $obj->d4_pay  = 0;
                    $obj->tax_wheld =0;

                    $obj->total_pay = $obj->acc_claim + $obj->med_claim + $obj->sup_claim + $obj->srv_claim + $obj->ops_claim + $obj->msc_claim + $obj->d1_claim + $obj->d2_claim + $obj->d3_claim + $obj->d4_claim;
                   
                    $obj->total_claim = $obj->total_pay;
                    $obj->net_pay   = $obj->total_pay - $obj->tax_wheld;  

                    if ($payres = $objpay->getClaimsPay($enc_nr, $hcare_id, $row["pid"], $row["priority"])) {                     
                        if ($payres->RecordCount()) {
                            if ($row = $payres->FetchRow()) {
                                $obj->acc_pay = (is_null($row["acc_pay"])) ? 0 : $row["acc_pay"];
                                $obj->med_pay = (is_null($row["med_pay"])) ? 0 : $row["med_pay"];
                                $obj->srv_pay = (is_null($row["srv_pay"])) ? 0 : $row["srv_pay"];
                                $obj->ops_pay = (is_null($row["ops_pay"])) ? 0 : $row["ops_pay"];
                                $obj->msc_pay = (is_null($row["msc_pay"])) ? 0 : $row["msc_pay"];
                                $obj->d1_pay  = (is_null($row["d1_pay"]))  ? 0 : $row["d1_pay"];
                                $obj->d2_pay  = (is_null($row["d2_pay"]))  ? 0 : $row["d2_pay"];
                                $obj->d3_pay  = (is_null($row["d3_pay"]))  ? 0 : $row["d3_pay"];
                                $obj->d4_pay  = (is_null($row["d4_pay"]))  ? 0 : $row["d4_pay"];
                                
                                $obj->tax_wheld = (is_null($row["tax_wheld"])) ? 0 : $row["tax_wheld"];
                                $obj->total_pay = $obj->acc_pay + $obj->med_pay + $obj->srv_pay + $obj->ops_pay + $obj->msc_pay + $obj->d1_pay + $obj->d2_pay + $obj->d3_pay + $obj->d4_pay;
                                $obj->net_pay   = $obj->total_pay - $obj->tax_wheld;                                                                
                            }                            
                        }                        
                    }                    
                    $objResponse->call("js_addclaimpayment", $enc_nr, $obj); 
                }
            }
        }
        else  
            $objResponse->alert("ERROR: ".$objclaim->sql);                      
                     
        return $objResponse;
    }
            
    function delPostedClaim($ref_no) {
        $objResponse = new xajaxResponse();                        
        
        $objclaim = new Claim();
        if ($objclaim->delPostedClaim($ref_no)) {
            $objResponse->alert("Posted claim {$ref_no} successfully deleted!"); 
            $objResponse->call("gotoBreakFile", $_SESSION["breakfile"]);
        }
        else
            $objResponse->alert("ERROR: ".$objclaim->getErrorMsg()); 
        
        return $objResponse;
    } 
    
    function assignToClaimsSessionVar($enc_nrs) {
        $objResponse = new xajaxResponse();    
        $_SESSION['claimcases'] = explode(",",$enc_nrs);            
        return $objResponse;
    }    

    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');     
    require_once($root_path.'include/care_api_classes/billing/class_claim.php'); 
    require_once($root_path.'include/care_api_classes/billing/class_transmittal.php');
    require_once($root_path.'modules/billing/ajax/claim-posting.common.php');        
    $xajax->processRequest();  
?>