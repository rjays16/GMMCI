<?php
/*
 * @package care_api
 * Class of Transmittal.
 *
 * Created: January 17, 2009 (LST)
 * Modified: January 17, 2009 (LST)
 */
require_once($root_path.'include/care_api_classes/class_core.php');

class Transmittal extends Core {
    /**
    * Table name
    * @var string
    */
    var $tb_hdr = 'seg_transmittal'; # transmittal header table
        /**
    * Table name
    * @var string
    */
    var $tb_details = 'seg_transmittal_details'; # transmittal details table
    /*
    * @var String
    */
    var $transmit_no;
    /*
    * @var Datetime
    */
    var $transmit_dte;
    /*
    * @var Integer
    */
    var $hcare_id;
    /*
    * @var String
    */
    var $remarks;
    /*
    * @var String
    */
    var $old_trnsmit_no;
    /*
    * @var String
    */
    var $user_name;
    /*
    * @var Array of String (encounter nos.)
    */
    var $encounters;
    /*
    * @var Array of Double (patient claims)
    */
    var $patient_claims;

    function setTransmitNo($no) {
        $this->transmit_no = $no;
    }

    function setTransmitDte($dte) {
        $this->transmit_dte = $dte;
    }

    function setInsuranceID($id) {
        $this->hcare_id = $id;
    }

    function setRemarks($srem) {
        $this->remarks = $srem;
    }

    function setUser($user) {
        $this->user_name = $user;
    }

    function setOldTransmitNo($no) {
        $this->old_trnsmit_no = $no;
    }

    function setEncountersWithClaim($cases) {
        $this->encounters = $cases;
    }

    function setPatientClaims($pclaims) {
        $this->patient_claims = $pclaims;
    }

    function setFromDte($fromdte){
        $this->claim_returned_from = $fromdte;
    }

    function setToDte($todte){
        $this->claim_returned_to = $todte;
    }
    
    function getErrorMsg() {
        global $db;

        $this->db_error_msg = $db->ErrorMsg();
        $this->error_msg = $this->LastErrorMsg();
        if ($this->error_msg == "") $this->error_msg = $this->db_error_msg;

        return $this->error_msg;
    }

   /**
    * @internal     Saves the transmittal header info in seg_transmittal and encounter nos. billed in seg_transmittal_details.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    * @global       db - database object, $_SESSION['sess_user_name'], $_SESSION['cases']
    *
    * @param        trnsmit_no, trnsmit_dte, hcare_id, remarks
    * @return       boolean TRUE if successful, FALSE otherwise.
    */
    function saveTransmittal() {
        global $db;

        $s_errmsg = '';
        $bSuccess = false;

        //$objResponse->alert($trnsmit_no);
        if ($this->transmit_no != '') {
            $this->startTrans();

            if ($this->old_trnsmit_no == '') {
                $strSQL = "insert into $this->tb_hdr (transmit_no, transmit_dte, hcare_id, remarks, create_id, modify_id, create_dt, modify_dt)
                              values('$this->transmit_no', '$this->transmit_dte', $this->hcare_id, '$this->remarks', '$this->user_name',
                                     '$this->user_name', now(), now())";
                $bSuccess = $db->Execute($strSQL);
            }
            else {
                $strSQL = "delete from $this->tb_details where transmit_no = '$this->old_trnsmit_no'";
                $bSuccess = $db->Execute($strSQL);

                if ($bSuccess) {
                    $strSQL = "update $this->tb_hdr set
                                  transmit_no  = '$this->transmit_no',
                                  transmit_dte = '$this->transmit_dte',
                                  hcare_id     = $this->hcare_id,
                                  remarks      = '$this->remarks',
                                  modify_id    = '$this->user_name',
                                  modify_dt    = now()
                                  where transmit_no = '$this->old_trnsmit_no'";
                    $bSuccess = $db->Execute($strSQL);
                }
                else
                    $s_errmsg = $this->getErrorMsg();
            }

            if ($bSuccess) {
                if (is_array($this->encounters) && (count($this->encounters) > 0)) {
                    $i = 0;
                    foreach ($this->encounters as $k=>$v) {
                        $pclaim = $this->patient_claims[$i++];
                        $strSQL = "insert into seg_transmittal_details (transmit_no, encounter_nr, patient_claim)
                                      values ('$this->transmit_no', '$v', $pclaim)";
                        $bSuccess = $db->Execute($strSQL);
                        if (!$bSuccess) {
                            $s_errmsg = $this->getErrorMsg();
                            break;
                        }
                    }
                }
                else {
                    $s_errmsg = "System cannot save transmittal without billing to be transmitted!";
                    $bSuccess = false;
                }
            }
            else
                $s_errmsg = $this->getErrorMsg();

            if (!$bSuccess) $this->failTrans();
            $this->completeTrans();
        }
        else
            $s_errmsg = "No valid transmittal control no.!";

        $this->error_msg = $s_errmsg;

        return $bSuccess;
    }

    function concatname($slast, $sfirst, $smid) {
        $stmp = "";

        if (!empty($slast)) $stmp .= $slast;
        if (!empty($sfirst)) {
            if (!empty($stmp)) $stmp .= ", ";
            $stmp .= $sfirst;
        }
        if (!empty($smid)) {
            if (!empty($stmp)) $stmp .= " ";
            $stmp .= $smid;
        }
        return($stmp);
    }

    /**
    * @internal     Return the transmittal header info.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    *
    * @param        stransmit_no -- transmittal control no.
    * @return       recordset if successful, FALSE otherwise.
    */
    function getTransmittalHeaderInfo($stransmit_no) {
        global $db;

        $this->sql = "select h.*, ci.name, ci.addr_mail \n
                         from $this->tb_hdr as h inner join care_insurance_firm as ci \n
                            on h.hcare_id = ci.hcare_id \n
                         where transmit_no = '$stransmit_no'";
        if ($this->result = $db->Execute($this->sql)) {
            if ($this->result->RecordCount())
                return $this->result->FetchRow();
            else
                return false;
        }
        else
            return false;
    }

    /**
    * @internal     Return the transmittal details.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    *
    * @param        stransmit_no -- transmittal control no.
    * @return       recordset if successful, FALSE otherwise.
    */
    function getTransmittalDetailsInfo($stransmit_no) {
        global $db;

        $this->sql = "select * \n
                         from $this->tb_details \n
                         where transmit_no = '$stransmit_no'";
        if ($this->result = $db->Execute($this->sql)) {
            if ($this->result->RecordCount())
                return $this->result;
            else
                return false;
        }
        else
            return false;
    }

   /**
    * @internal     Return the recordset of transmittals given the filter.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    *
    * @param        filters, offset, rowcount, bForClaim (is extraction for claim posting?), encrs (list of encounter no.s to exclude) 
    * @return       recordset if successful, FALSE otherwise.
    */
    function getTransmittalDetails($filters, $offset=0, $rowcount=15, $bForClaim=false, $encrs = '') {
        global $db;

        if (!$offset) $offset = 0;
        if (!$rowcount) $rowcount = 15;

                $filter_err = '';

        if (is_array($filters)) {
            foreach ($filters as $i=>$v) {
                switch (strtolower($i)) {
                    case 'transmittal_no':
                        $phFilters[] = 'h.transmit_no = ' . $db->qstr($v);
                        break;
                    case 'datetoday':
                        $phFilters[] = 'DATE(transmit_dte)=DATE(NOW())';
                    break;
                    case 'datethisweek':
                        $phFilters[] = 'YEAR(transmit_dte)=YEAR(NOW()) AND WEEK(transmit_dte)=WEEK(NOW())';
                    break;
                    break;
                    case 'datethismonth':
                        $phFilters[] = 'YEAR(transmit_dte)=YEAR(NOW()) AND MONTH(transmit_dte)=MONTH(NOW())';
                    break;
                    case 'date':
                        $phFilters[] = "DATE(transmit_dte)='$v'";
                    break;
                    case 'datebetween':
//                                              $phFilters[] = "DATE(transmit_dte)>='".$v[0]."' AND DATE(transmit_dte)<='".$v[1]."'";
                                                $phFilters[] = "DATE(transmit_dte) BETWEEN '".$v[0]."' AND '".$v[1]."'";
                    break;
                    case 'name':
//                                              $phFilters[] = "concat(cp.name_last, (case when isnull(cp.name_first) or cp.name_first = '' then (case when isnull(cp.name_middle) or cp.name_middle = '' then '' else ', ' end) else ', ' + cp.name_first end), (case when isnull(cp.name_middle) or cp.name_middle = '' then '' else ' ' + cp.name_middle end)) REGEXP '[[:<:]]".substr($db->qstr($v),1);
                                            if (strpos($v, ",") === false)
                                                $phFilters[] = "cp.name_last like '".trim($v)."%'";
                                            else {
                                                $tmp = explode(",", $v);
                                                $phFilters[] = "cp.name_last like '".trim($tmp[0])."%'";
                                                $phFilters[] = "cp.name_first like '".trim($tmp[1])."%'";
                                            }
                        break;
                    case 'case_no':
                        $phFilters[] = "ce.encounter_nr REGEXP ".$db->qstr($v);
                    break;
                    case 'trans_no':
                        $phFilters[] = "h.transmit_no = ".$db->qstr($v);
                    break;
                    case 'insurance':
                        $phFilters[] = "h.hcare_id = ".$v;
                        $hcare_id = $v;
                    break;
                    case 'er':
                        $phFilters[] = " ce.encounter_type = '1'";
                    break;
                    case 'opd':
                        $phFilters[] = " ce.encounter_type = '2'";
                    break;
                    case 'ipd':
                        $phFilters[] = " (ce.encounter_type = '3' OR ce.encounter_type ='4')";
                    break;
                }
            }
        }

        $phWhere=implode(") AND (",$phFilters);
        if ($phWhere) $phWhere = "($phWhere)";
        else $phWhere = "1";

        //modified by julz to remove slow query
        $sFilterForClaim = '';
        if ($bForClaim) {
            $sFilterForClaim = "AND NOT EXISTS (SELECT * 
                                        FROM
                                          `seg_claim_posting` a 
                                          LEFT JOIN `seg_claim_pay_hosp` b 
                                            ON a.`ref_no` = b.`ref_no` 
                                          LEFT JOIN `seg_claim_pay_patient` c 
                                            ON c.`ref_no` = a.`ref_no` 
                                          LEFT JOIN `seg_claim_pay_pf` e 
                                            ON e.`ref_no` = a.`ref_no` 
                                        WHERE (b.`encounter_nr` = d.encounter_nr)
                                          AND a.`hcare_id` ='$hcare_id' )";
        }
        
        if ($encrs != '') {
            $sFilterForClaim .= " and d.encounter_nr NOT IN ($encrs) \n";
        }                                    

        $this->sql = "select ce.pid, h.hcare_id, h.transmit_no, transmit_dte, cp.name_last, cp.name_first, cp.name_middle, d.encounter_nr, \n
                         (select sum(total_acc_coverage + total_med_coverage + total_sup_coverage + total_srv_coverage + total_ops_coverage + total_d1_coverage + total_d2_coverage + total_d3_coverage + total_d4_coverage + total_msc_coverage) as tclaim \n
                             from seg_billing_coverage as sbc inner join seg_billing_encounter as sbe on \n
                                sbc.bill_nr = sbe.bill_nr where sbe.encounter_nr = d.encounter_nr and sbc.hcare_id = h.hcare_id and sbe.is_deleted IS NULL) as claim, \n
                         (select insurance_nr from care_person_insurance as cpi where cpi.pid = ce.pid and cpi.hcare_id = h.hcare_id) as policy_no, \n
                         concat(date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p'), ' to ', (case when ce.discharge_date is null or ce.discharge_date = '' then 'present' else date_format(str_to_date(ce.modify_time, '%Y-%m-%d %H:%i:%s'), '%b %e, %Y %l:%i%p') end)) as confine_period, \n
                         d.is_rejected \n
                         from ((seg_transmittal as h inner join seg_transmittal_details as d on h.transmit_no = d.transmit_no) \n
                            inner join care_encounter as ce on d.encounter_nr = ce.encounter_nr) inner join care_person as cp on ce.pid = cp.pid \n".
                     "   where ($phWhere) $sFilterForClaim".
                     "   order by h.transmit_dte asc ".
                     "   limit $offset, $rowcount";


         
        if ($this->result = $db->Execute($this->sql))
            return $this->result;
        else
            return false;
    }

   /**
    * @internal     Mark a particular claim in the transmittal as rejected.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    *
    * @param        transmit_no, enc_nr
    * @return       boolean TRUE if successful, FALSE otherwise.
    */
    function toggleReject($transmit_no, $enc_nr, $breject = false) {
        $this->sql = "update $this->tb_details set is_rejected = ".($breject ? 1 : 0)." \n
                         where transmit_no = '$transmit_no' and encounter_nr = '$enc_nr'";
        return $this->Transact($this->sql);
    }

   /**
    * @internal     Queries if transmittal is rejected or not.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    *
    * @param        transmit_no, enc_nr
    * @return       boolean TRUE if rejected, FALSE otherwise.
    */
    function isRejected($transmit_no, $enc_nr) {
        global $db;

        $is_rejected = false;

        $this->sql = "select is_rejected \n
                         from $this->tb_details \n
                         where transmit_no = '$transmit_no' and encounter_nr = '$enc_nr'";
        if ($result = $db->Execute($this->sql)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    $is_rejected = ($row['is_rejected'] != 0);
                }
            }
        }

        return $is_rejected;
    }

   /**
    * @internal     Indicates whether patient is principal member or not.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    *
    * @param        pid of patient, health care insurance id
    * @return       boolean -- true if principal member.
    */
    function isPersonPrincipal($s_pid, $n_hcareid) {
        global $db;

        $bPrincipal = false;

        $strSQL = "select is_principal ".
                  "   from care_person_insurance as cpi ".
                  "   where pid = '$s_pid' and hcare_id = $n_hcareid";

        if ($result = $db->Execute($strSQL))
            if ($result->RecordCount())
                while ($row = $result->FetchRow()) {
                    if ($row['is_principal'])
                        $bPrincipal = true;
                    else
                        $bPrincipal = false;
                }

        return($bPrincipal);
    }

   /**
    * @internal     Returns the pid of principal member of insurance.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    *
    * @param        pid of patient, health care insurance id
    * @return       pid of principal member.
    */
    function getPrincipalPIDofHCare($s_pid, $nhcareid) {
        global $db;

        $sprincipal_pid = "";

        $strSQL = "select pid ".
                  "   from care_person_insurance as cpi0 ".
                  "   where exists (select * from care_person_insurance as cpi1 ".
                  "                    where cpi1.pid = '". $s_pid ."' and cpi1.hcare_id = ". $nhcareid ." ".
                  "                       and cpi1.pid <> cpi0.pid and cpi1.hcare_id = cpi0.hcare_id ".
                  "      and cpi1.insurance_nr = cpi0.insurance_nr) ".
                                    "      and cpi0.is_principal <> 0 ".
                                    "      AND LENGTH(cpi0.insurance_nr) >= ".INSURANCE_NO_LEN;

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow())
                    $sprincipal_pid = $row['pid'];
            }
        }

        return($sprincipal_pid);
    }

    //added by Nick, 2/24/2014
    /**
     * Gets the patient trasmittal info
     * @param  string $enc Encounter Number
     * @return array       Returns array of details
     */
    var $data;
    function getPatientTrasmittalInfo($enc){
        global $db;
        $sql = "SELECT * FROM `seg_transmittal_details` WHERE encounter_nr = ?";
        $rs = $db->Execute($sql,$enc);
        if($rs){
            if($rs->RecordCount() > 0){
                $rows = $rs->GetRows();
                return $rows;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    //end nick

   /**
    * @internal     Returns the full name of principal member of insurance.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    * 
    * @param        pid of patient, health care insurance id
    * @return       full name of principal member.
    */     
    function getFullNameOfMember($s_pid, $nhcareid) {
        global $db;
        
        $sname = "";        
        $strSQL = "select fn_get_pid_lastfirstmi(pid) as full_name ".
                  "   from care_person_insurance as cpi0 ".
                  "   where exists (select * from care_person_insurance as cpi1 ".
                  "                    where cpi1.pid = '". $s_pid ."' and cpi1.hcare_id = ". $nhcareid ." ".
                  "                       and cpi1.pid <> cpi0.pid and cpi1.hcare_id = cpi0.hcare_id ".
                  "      and cpi1.insurance_nr = cpi0.insurance_nr) ".
                  "      and cpi0.is_principal <> 0";        
    
        if ($result = $db->Execute($strSQL)) {                
            if ($result->RecordCount()) {            
                if ($row = $result->FetchRow()) {
                    $sname = $row['full_name'];
                }
            }
        }
    
        return($sname);                    
    }
    
   /**
    * @internal     Returns the resultset of claims due.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    * 
    * @param        encounter_nr of patient, health care insurance id
    * @return       resultset of claims due.
    */      
    function getClaimsDue($enc_nr, $hcare_id) {
        global $db;
        
        // $this->sql = "select 2 as priority, sbe.encounter_nr, 0 as pid, '(HOSPITAL)' as full_name, sum(total_acc_coverage) as acc_claim, sum(total_med_coverage) as med_claim, sum(total_sup_coverage) as sup_claim, sum(total_srv_coverage) as srv_claim,  \n
        //                  sum(total_ops_coverage) as ops_claim, sum(total_msc_coverage) as msc_claim, 0 as d1_claim, 0 as d2_claim, 0 as d3_claim, 0 as d4_claim     \n
        //                  from seg_billing_encounter as sbe inner join seg_billing_coverage as sbc on sbe.bill_nr = sbc.bill_nr                                      \n
        //                  and sbe.is_deleted is null \n
        //                  where sbe.encounter_nr = '$enc_nr' and sbc.hcare_id = $hcare_id \n
        //                  group by sbe.encounter_nr, sbc.hcare_id                                                                                                    \n
                                                                                                   
        //               union    \n
        //               select 1 as priority, sbe.encounter_nr, sbp.dr_nr, ucase(fn_get_personellname_lastfirstmi(sbp.dr_nr)) as full_name, 0 as acc_claim, 0 as med_claim, 0 as sup_claim, 0 as srv_claim, 0 as ops_claim, 0 as msc_claim, (case when role_area = 'D1' then sum(dr_claim) + IFNULL(spe.amount,0) else 0 end) as d1_claim, \n
        //                  (case when role_area = 'D2' then sum(dr_claim) + IFNULL(spe.amount,0) else 0 end) as d2_claim,      \n
        //                  (case when role_area = 'D3' then sum(dr_claim) + IFNULL(spe.amount,0) else 0 end) as d3_claim,      \n
        //                  (case when role_area = 'D4' then sum(dr_claim) + IFNULL(spe.amount,0) else 0 end) as d4_claim       \n
        //                  from seg_billing_encounter as sbe inner join seg_billing_pf as sbp on sbe.bill_nr = sbp.bill_nr     \n
        //                  and sbe.is_deleted is null  \n
        //                  LEFT JOIN seg_pf_excess spe 
        //                   ON (
        //                     spe.encounter_nr = sbe.`encounter_nr` 
        //                     AND spe.dr_nr = sbp.dr_nr
        //                   )
        //                  where sbe.encounter_nr = '$enc_nr' and sbp.hcare_id = $hcare_id \n
        //                  group by sbe.encounter_nr, sbp.dr_nr, role_area, sbp.hcare_id                                   \n
                          
        //               union    \n
        //               select 3 as priority, td.encounter_nr, ce.pid, ucase(fn_get_pid_lastfirstmi(ce.pid)) as full_name, 0 as acc_claim, total_meds as med_claim, 0 as sup_claim, total_xlo as srv_claim, 0 as ops_claim, 0 as msc_claim, 0 as d1_claim, 0 as d2_claim, 0 as d3_claim, 0 as d4_claim    \n
        //                  from seg_encounter_reimbursed td  \n
        //                     inner join care_encounter as ce on td.encounter_nr = ce.encounter_nr                                   \n
        //                  where td.encounter_nr = '$enc_nr' and td.hcare_id = $hcare_id                                             \n
        //               order by priority";

        $this->sql = "select 2 as priority, sbe.encounter_nr, 0 as pid, '(HOSPITAL)' as full_name, sum(total_acc_coverage) as acc_claim, sum(total_med_coverage) as med_claim, sum(total_sup_coverage) as sup_claim, sum(total_srv_coverage) as srv_claim,  \n
                         sum(total_ops_coverage) as ops_claim, sum(total_msc_coverage) as msc_claim, 0 as d1_claim, 0 as d2_claim, 0 as d3_claim, 0 as d4_claim     \n
                         from seg_billing_encounter as sbe inner join seg_billing_coverage as sbc on sbe.bill_nr = sbc.bill_nr                                      \n
                         and sbe.is_deleted is null \n
                         where sbe.encounter_nr = '$enc_nr' and sbc.hcare_id = $hcare_id \n
                         group by sbe.encounter_nr, sbc.hcare_id                                                                                                    \n
                                                                                                   
                      union    \n
                      select 1 as priority, sbe.encounter_nr, sbp.dr_nr, ucase(fn_get_personellname_lastfirstmi(sbp.dr_nr)) as full_name, 0 as acc_claim, 0 as med_claim, 0 as sup_claim, 0 as srv_claim, 0 as ops_claim, 0 as msc_claim, (case when role_area = 'D1' then sum(dr_claim) else 0 end) as d1_claim, \n
                         (case when role_area = 'D2' then sum(dr_claim)  else 0 end) as d2_claim,      \n
                         (case when role_area = 'D3' then sum(dr_claim)  else 0 end) as d3_claim,      \n
                         (case when role_area = 'D4' then sum(dr_claim)  else 0 end) as d4_claim       \n
                         from seg_billing_encounter as sbe inner join seg_billing_pf as sbp on sbe.bill_nr = sbp.bill_nr     \n
                         and sbe.is_deleted is null  \n
                         LEFT JOIN seg_pf_excess spe 
                          ON (
                            spe.encounter_nr = sbe.`encounter_nr` 
                            AND spe.dr_nr = sbp.dr_nr
                          )
                         where sbe.encounter_nr = '$enc_nr' and sbp.hcare_id = $hcare_id \n
                         group by sbe.encounter_nr, sbp.dr_nr, role_area, sbp.hcare_id                                   \n
                          
                      union    \n
                      select 3 as priority, td.encounter_nr, ce.pid, ucase(fn_get_pid_lastfirstmi(ce.pid)) as full_name, 0 as acc_claim, total_meds as med_claim, 0 as sup_claim, total_xlo as srv_claim, 0 as ops_claim, 0 as msc_claim, 0 as d1_claim, 0 as d2_claim, 0 as d3_claim, 0 as d4_claim    \n
                         from seg_encounter_reimbursed td  \n
                            inner join care_encounter as ce on td.encounter_nr = ce.encounter_nr                                   \n
                         where td.encounter_nr = '$enc_nr' and td.hcare_id = $hcare_id                                             \n
                      order by priority";

        if ($this->result = $db->Execute($this->sql))
            return $this->result;
        else
            return false;        
    }
    
   /**
    * @internal     Returns the resultset of claims due for the given date and health care id.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    * 
    * @param        health care insurance id , date today, (optional) category id
    * @return       resultset of claims due.
    */      
    function getTransmittalsOfDay($hcare_id, $curdte, $category_id = 0) {
        global $db;
        
        $sfilter = '';
        if ($category_id != 0) {
            $sfilter = " and sem.memcategory_id = $category_id";
        }
        $this->sql = "select count(distinct encounter_nr) as claim_count, sum(hosp_claim) as hosp_claim, sum(pf_claim) as pf_claim
                        from
                        (select 2 as priority, td.encounter_nr, sum(total_acc_coverage + total_med_coverage + total_sup_coverage + total_srv_coverage + total_ops_coverage + total_msc_coverage) as hosp_claim, 0 as pf_claim                       
                             from ((seg_transmittal as th inner join seg_transmittal_details as td on th.transmit_no = td.transmit_no) 
                                left join seg_encounter_memcategory as sem on td.encounter_nr = sem.encounter_nr) 
                                left join (seg_billing_encounter as sbe inner join seg_billing_coverage as sbc on sbe.bill_nr = sbc.bill_nr) 
                             on td.encounter_nr = sbe.encounter_nr and th.hcare_id = sbc.hcare_id 
                             group by th.hcare_id, th.transmit_dte, sem.memcategory_id, td.encounter_nr
                             having date(th.transmit_dte) = '$curdte' and th.hcare_id = $hcare_id".$sfilter."
                        union
                        select 1 as priority, td.encounter_nr, 0 as hosp_claim, sum(dr_claim) as pf_claim                       
                             from ((seg_transmittal as th inner join seg_transmittal_details as td on th.transmit_no = td.transmit_no) 
                                left join seg_encounter_memcategory as sem on td.encounter_nr = sem.encounter_nr) 
                                left join (seg_billing_encounter as sbe inner join seg_billing_pf as sbp on sbe.bill_nr = sbp.bill_nr) 
                             on td.encounter_nr = sbe.encounter_nr and th.hcare_id = sbp.hcare_id 
                             group by th.hcare_id, th.transmit_dte, sem.memcategory_id, td.encounter_nr 
                             having date(th.transmit_dte) = '$curdte' and th.hcare_id = $hcare_id".$sfilter.") as t";
                                                             
        if ($this->result = $db->Execute($this->sql))
            return $this->result;
        else
            return false;        
    }    
    
   /**
    * @internal     Delete the transmittal identified by 'transmit_no'.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    * @global       db - database object
    * 
    * @param        trnsmit_no (string)
    * @return       boolean TRUE if successful, FALSE otherwise.
    */  
    function delTransmittal($transmit_no) {
        $this->sql = "delete from {$this->tb_hdr} where transmit_no = '{$transmit_no}'";
        return $this->Transact($this->sql);    
    }    

    //Added by EJ 10/07/2014
    function getLastTransmitNo() {
        global $db;
        $this->sql="SELECT 
                    IFNULL(
                    MAX(CAST(transmit_no AS UNSIGNED) + 1),
                    CONCAT(EXTRACT(YEAR FROM NOW()), '000001')
                    ) 
                    FROM
                    $this->tb_hdr 
                    WHERE SUBSTRING(transmit_no, 1, 4) = EXTRACT(YEAR FROM NOW())";

        return $db->GetOne($this->sql);
    } 

    function getTransmittals($from='', $to='', $date='', $firm_id='', $ptype='', $searchby='', $keyword=''){
        global $db, $sql_LIKE;

        //conditions
        $cond= array();
        $where="";

        if($date){
            switch($date){
                case 'between':
                    if($from && $to){
                        $cond[] = " DATE(transmit_dte) BETWEEN ".$db->qstr($from)." AND ".$db->qstr($to);
                    }  
                break;
                case 'specificdate':
                    if($from){
                        $cond[] = " DATE(transmit_dte) = ".$db->qstr($from);
                    }
                break;
                case 'today':
                    $cond[] = " DATE(transmit_dte) = DATE(NOW())";
                break;
                case 'thisweek':
                    $cond[] = " WEEK(transmit_dte) = WEEK(NOW())";
                break;
                case 'thismonth':
                    $cond[] = " MONTH(transmit_dte) = MONTH(NOW())";
                break;

            }
        }

        if($firm_id){
            $cond[] = " cif.firm_id = ".$db->qstr($firm_id);
        }
        
        if($ptype){
            switch ($ptype) {
                case 1:
                    $cond[] = " ce.encounter_type = '1'";    
                break;
                case 2:
                    $cond[] = " ce.encounter_type = '2'";    
                break;
                case 3:
                    $cond[] = " (ce.encounter_type = '3' OR ce.encounter_type = '4')";    
                break;
            }
        }

        if($searchby){
            switch($searchby){
                case 'name':
                    $cond[] = " cp.name_last LIKE ".$db->qstr("%".$keyword."%");
                break;
                case 'case_no':
                    $cond[] = " ce.encounter_nr = ".$db->qstr($keyword);
                break;
                case 'transmittal_no':
                    $cond[] = " h.transmit_no = ".$db->qstr($keyword);
                break;

            }
        }

        $where=implode(" AND ", $cond);
        
        if($where) {
            $where ="WHERE".$where;
        }

        //query
        $this->sql = "SELECT 
                      ce.pid,
                      cif.`firm_id`,
                      h.transmit_no,
                      DATE_FORMAT(
                          STR_TO_DATE(
                            transmit_dte,
                            '%Y-%m-%d'
                          ),
                          '%b %e, %Y'
                        )  transmit_dte,
                      fn_get_person_name(cp.`pid`) patient,
                      d.encounter_nr,
                      (SELECT 
                        SUM(
                          total_acc_coverage + total_med_coverage + total_sup_coverage + total_srv_coverage + total_ops_coverage + total_d1_coverage + total_d2_coverage + total_d3_coverage + total_d4_coverage + total_msc_coverage
                        ) AS tclaim 
                      FROM
                        seg_billing_coverage AS sbc 
                        INNER JOIN seg_billing_encounter AS sbe 
                          ON sbc.bill_nr = sbe.bill_nr 
                      WHERE sbe.encounter_nr = d.encounter_nr 
                        AND sbc.hcare_id = h.hcare_id 
                        AND sbe.is_deleted IS NULL) AS claim,
                      CONCAT(
                        DATE_FORMAT(
                          (
                            CASE
                              WHEN admission_dt IS NULL 
                              OR admission_dt = '' 
                              THEN encounter_date 
                              ELSE admission_dt 
                            END
                          ),
                          '%b %e, %Y'
                        ),
                        ' to ',
                        (
                          CASE
                            WHEN ce.discharge_date IS NULL 
                            OR ce.discharge_date = '' 
                            THEN 'present' 
                            ELSE DATE_FORMAT(
                              STR_TO_DATE(
                                ce.discharge_date,
                                '%Y-%m-%d'
                              ),
                              '%b %e, %Y'
                            ) 
                          END
                        )
                      ) AS confine_period,
                      d.is_rejected 
                    FROM
                      (
                        (
                          seg_transmittal AS h 
                          INNER JOIN seg_transmittal_details AS d 
                            ON h.transmit_no = d.transmit_no
                        ) 
                        INNER JOIN care_encounter AS ce 
                          ON d.encounter_nr = ce.encounter_nr
                      ) 
                      INNER JOIN care_person AS cp 
                        ON ce.pid = cp.pid 
                      LEFT JOIN care_insurance_firm cif 
                        ON cif.`hcare_id` = h.`hcare_id` 
                     $where 
                    ORDER BY cif.`firm_id`, h.transmit_dte, cp.`name_last` ASC ";
                    //echo $this->sql; exit();
        if($this->result=$db->Execute($this->sql)){
            return $this->result;
        }

        return false;
    }
}
?>
