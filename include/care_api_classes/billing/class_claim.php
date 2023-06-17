<?php
/**
* @package SegHIS_api
*/                                         

/******
*
*   Class containing all properties and methods related to Claims.
*
*   Note this class should be instantiated only after a "$db" adodb  connector object 
*   has been established by an adodb instance.
*
*   @author      :    Lemuel 'Bong' S. Trazo
*   @version     :    1.0
*   @date created:    June 18, 2009
*   @date updated:    June 18, 2009
*
*****/    
require_once('roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');

class Claim extends Core {
    
    var $tb_claim_h       = 'seg_claim_posting';          // Claim posting header table.
    var $tb_claim_hosp    = 'seg_claim_pay_hosp';         // Claim payments for hospital
    var $tb_claim_pf      = 'seg_claim_pay_pf';           // Claim payments for doctors
    var $tb_claim_denied  = 'seg_claim_denied';           // Claims denied
    var $tb_claim_ret     = 'seg_claim_returned';         // Claims returned to hospital
    var $tb_claim_patient = 'seg_claim_pay_patient';      // Claim payments for patient
    
    /**
    * Array of fields in Claim posting header table
    * @var array
    */   
    var $claim_h = array('ref_no',
                         'post_dte',
                         'category_id',
                         'apv_no',
                         'check_no',
                         'apv_date',
                         'hcare_id',
                         'history',
                         'modify_id',
                         'modify_dt',
                         'create_id',
                         'create_dt'); 
                         
    /**
    * Array of fields in claim for hospital
    * @var array
    */   
    var $claim_hosp = array('ref_no',
                            'encounter_nr',
                            'acc_pay',
                            'med_pay',
                            'sup_pay',
                            'srv_pay',
                            'ops_pay',
                            'd1_pay',
                            'd2_pay',
                            'd3_pay',
                            'd4_pay',
                            'msc_pay',
                            'tax_wheld');     
                            
    /**
    * Array of fields in claim for doctors
    * @var array
    */   
    var $claim_pf = array('ref_no',
                          'encounter_nr',
                          'dr_nr',
                          'role_area',
                          'dr_pay',
                          'tax_wheld');  
                          
    /**
    * Array of fields in claim for patient
    * @var array
    */   
    var $claim_patient = array('ref_no',
                               'encounter_nr',
                               'patient_pay',
                               'tax_wheld');                            
                            
    /**
    * Array of fields in denied claim
    * @var array
    */   
    var $claim_denied = array('ref_no',
                              'encounter_nr',
                              'remarks');     
                            
    /**
    * Array of fields in returned claim
    * @var array
    */   
    var $claim_ret = array('ref_no',
                           'encounter_nr',
                           'remarks');  
    
    
    function Claim() {
        $this->useClaimHdr();
    }
    
    function useClaimHdr() {
        $this->ref_array=$this->claim_h;
        $this->coretable=$this->tb_claim_h;    
    }
    
    function useClaimDenied() {
        $this->ref_array=$this->claim_denied;
        $this->coretable=$this->tb_claim_denied;       
    }   
    
    function useClaimReturned() {
        $this->ref_array=$this->claim_ret;
        $this->coretable=$this->tb_claim_ret;       
    }    
    
    function useClaimPayPF() {
        $this->ref_array=$this->claim_pf;
        $this->coretable=$this->tb_claim_pf;       
    }     
    
    function useClaimPayHosp() {
        $this->ref_array=$this->claim_hosp;
        $this->coretable=$this->tb_claim_hosp;       
    }     
    
    function useClaimPayPatient() {
        $this->ref_array=$this->claim_patient;
        $this->coretable=$this->tb_claim_patient;       
    }     
    
    function getLastNr($today) {
        global $db;
        $today = $db->qstr($today);
        $this->sql="SELECT IFNULL(MAX(CAST(ref_no AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),'000001')) FROM $this->tb_claim_h WHERE SUBSTRING(ref_no,1,4)=EXTRACT(YEAR FROM NOW())";
        return $db->GetOne($this->sql);
    }     
    
    /**
    * @internal     Get posted claim header information.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        ref_no - reference no. of posted claim
    * @return       array of extracted field values, FALSE otherwise.
    */     
    function getPostedClaim($ref_no) {
        global $db;        
        
        $this->sql = "select h.*, memcategory_desc, i.name                                               \n
                         from ($this->tb_claim_h as h left join seg_memcategory as mc                   \n
                            on h.category_id = mc.memcategory_id) inner join care_insurance_firm as i    \n
                            on h.hcare_id = i.hcare_id                                                   \n
                         where ref_no = '$ref_no'";
        if($this->result=$db->Execute($this->sql)) {
            if ($this->result->RecordCount())
                return $this->result->FetchRow();
            else
                return false;
        } else { return false; }    
    }
    
    /**
    * @internal     Get encounter no.s included in threis claim with reference no. 'ref_no'.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        ref_no - reference no. of posted claim
    * @return       resultset of encounter no.s, FALSE otherwise.
    */     
    function getEncountersInClaim($ref_no) {
        global $db;
        
        $this->sql = "select encounter_nr 
                         from seg_claim_denied as scd inner join seg_claim_posting as scp 
                            on scd.ref_no = scp.ref_no 
                         where scp.ref_no = '$ref_no' 
                      union 
                      select encounter_nr 
                         from seg_claim_pay_hosp as scph inner join seg_claim_posting as scp 
                            on scph.ref_no = scp.ref_no 
                         where scp.ref_no = '$ref_no'
                       union 
                       select encounter_nr 
                         from seg_claim_pay_patient as scpp inner join seg_claim_posting as scp 
                            on scpp.ref_no = scp.ref_no 
                         where scp.ref_no = '$ref_no'
                       union 
                       select encounter_nr 
                         from seg_claim_pay_pf as scpf inner join seg_claim_posting as scp 
                            on scpf.ref_no = scp.ref_no
                         where scp.ref_no = '$ref_no'
                       union 
                       select encounter_nr 
                         from seg_claim_returned as scr inner join seg_claim_posting as scp 
                            on scr.ref_no = scp.ref_no 
                         where scp.ref_no = '$ref_no'";
                         
        if($this->result=$db->Execute($this->sql)) {
            if ($this->result->RecordCount())
                return $this->result;
            else {
//                echo "SQL = ".$this->sql;    
                return false;
            }
        } else { 
//            echo "ERROR = ".$db->ErrorMsg();    
            return false; 
        }        
    }
            
   /**
    * @internal     Returns the resultset of claims payment.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    * 
    * @param        encounter_nr of patient, health care insurance id, pid, priority no.
    * @return       resultset of claims payment.
    */     
    function getClaimsPay($enc_nr, $hcare_id, $pid = '', $priority_nr = 0) {
        global $db;
        
        $sfilter = '';
        if ($pid != '') $sfilter = "(pid = '$pid')";
        if ($priority_nr != 0) {
            if ($sfilter != '') $sfilter .= " and ";
            $sfilter .= "(priority = $priority_nr)";
        }
        
        if ($sfilter != '') $sfilter = "where $sfilter";  
        
        $this->sql = "select priority, encounter_nr, pid, acc_pay, med_pay, sup_pay, srv_pay, ops_pay, msc_pay, d1_pay, d2_pay, d3_pay, d4_pay, tax_wheld    \n
                         from
                      (select 2 as priority, scph.encounter_nr, 0 as pid, acc_pay, med_pay, sup_pay, srv_pay, ops_pay, msc_pay, 0 as d1_pay, 0 as d2_pay, 0 as d3_pay, 0 as d4_pay, tax_wheld \n
                         from seg_claim_pay_hosp as scph inner join seg_claim_posting as scp on scph.ref_no = scp.ref_no     \n
                         where scph.encounter_nr = '$enc_nr' and scp.hcare_id = $hcare_id                                    \n
                      union                                                                                                  \n
                      select 1 as priority, scpf.encounter_nr, dr_nr as pid, 0 as acc_pay, 0 as med_pay, 0 as sup_pay, 0 as srv_pay, 0 as ops_pay, 0 as msc_pay, (case when role_area = 'D1' then dr_pay else 0 end) as d1_pay,   \n
                            (case when role_area = 'D2' then dr_pay else 0 end) as d2_pay,     \n
                            (case when role_area = 'D3' then dr_pay else 0 end) as d3_pay,     \n
                            (case when role_area = 'D4' then dr_pay else 0 end) as d4_pay, tax_wheld                         \n
                         from seg_claim_pay_pf as scpf inner join seg_claim_posting as scp on scpf.ref_no = scp.ref_no       \n
                         where scpf.encounter_nr = '$enc_nr' and scp.hcare_id = $hcare_id                                    \n
                      union                                                                                                  \n         
                      select 3 as priority, scpp.encounter_nr, ce.pid, 0 as acc_pay, patient_pay as med_pay, 0 as sup_pay, 0 as srv_pay, 0 as ops_pay, 0 as msc_pay, 0 as d1_pay, 0 as d2_pay, 0 as d3_pay, 0 as d4_pay, tax_wheld  \n
                         from (seg_claim_pay_patient as scpp inner join seg_claim_posting as scp on scpp.ref_no = scp.ref_no) \n
                            inner join care_encounter as ce on scpp.encounter_nr = ce.encounter_nr                            \n
                         where scpp.encounter_nr = '$enc_nr' and scp.hcare_id = $hcare_id) as t                               \n
                       $sfilter
                      order by priority";                      

        if ($this->result = $db->Execute($this->sql))
            return $this->result;
        else {            
            return false;        
        }
    }
    
    /**
    * @internal     Delete the claim details in the five (5) tables.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    * 
    * @param        reference no of claim
    * @return       true if successful deletion, false otherwise
    */     
    function delClaimDetails($sref_no) {       
        $this->sql = "delete from $this->tb_claim_denied where ref_no = '$sref_no'";
        if ($this->Transact($this->sql)) {
            $this->sql = "delete from $this->tb_claim_hosp where ref_no = '$sref_no'";
            if ($this->Transact($this->sql)) { 
                $this->sql = "delete from $this->tb_claim_pf where ref_no = '$sref_no'";
                if ($this->Transact($this->sql)) { 
                    $this->sql = "delete from $this->tb_claim_patient where ref_no = '$sref_no'";  
                    if ($this->Transact($this->sql)) {  
                        $this->sql = "delete from $this->tb_claim_ret where ref_no = '$sref_no'";
                        return($this->Transact($this->sql));
                    }  
                }
            }
        }
        return false;        
    }
    
   /**
    * @internal     Return the recordset of claims posted given the filter.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    * 
    * @param        filters, offset, rowcount
    * @return       recordset if successful, FALSE otherwise.
    */     
    function getClaimsPosted($filters, $offset=0, $rowcount=15) {
        global $db;

        if (!$offset) $offset = 0;
        if (!$rowcount) $rowcount = 15;
        
        if (is_array($filters)) {
            foreach ($filters as $i=>$v) {
                switch (strtolower($i)) {
                    case 'datetoday':
                        $phFilters[] = 'DATE(post_dte)=DATE(NOW())';
                    break;
                    case 'datethisweek':
                        $phFilters[] = 'YEAR(post_dte)=YEAR(NOW()) AND WEEK(post_dte)=WEEK(NOW())';
                    break;
                    break;
                    case 'datethismonth':
                        $phFilters[] = 'YEAR(post_dte)=YEAR(NOW()) AND MONTH(post_dte)=MONTH(NOW())';
                    break;
                    case 'date':
                        $phFilters[] = "DATE(post_dte)='$v'";
                    break;
                    case 'datebetween':
                        $phFilters[] = "DATE(post_dte)>='".$v[0]."' AND DATE(post_dte)<='".$v[1]."'";
                    break;
                    case 'name':
                        $phFilters[] = "fn_get_pid_lastfirstmi(ce.pid) REGEXP '[[:<:]]".substr($db->qstr($v),1);
                    break;
                    case 'member':
                        $phFilters[] = "fn_get_principalmembername(ce.pid, scp.hcare_id) REGEXP '[[:<:]]".substr($db->qstr($v),1);
                    break;                    
                    case 'case_no':
                        $phFilters[] = "ce.encounter_nr REGEXP ".$db->qstr($v);
                    break;
                    case 'insurance':
                        $phFilters[] = "scp.hcare_id = ".$v;
                    break;
                }
            }
        }
        
        $phWhere=implode(") AND (",$phFilters);
        if ($phWhere) $phWhere = "($phWhere)";
        else $phWhere = "1";                                           

        $this->sql = "select scd.ref_no, scd.encounter_nr, ce.pid, fn_get_pid_lastfirstmi(ce.pid) as full_name, 'DENIED' as status, 
                       concat(date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p'), ' to ', (case when ce.discharge_date is null or ce.discharge_date = '' then 'present' else date_format(str_to_date(ce.discharge_date, '%Y-%m-%d %H:%i:%s'), '%b %e, %Y %l:%i%p') end)) as confine_period,    
                       (select insurance_nr from care_person_insurance as cpi where cpi.pid = ce.pid and cpi.hcare_id = scp.hcare_id) as policy_no, 
                       scp.post_dte, scp.hcare_id, fn_get_principalmembername(ce.pid, scp.hcare_id) as member_name  
                             from (seg_claim_denied as scd inner join seg_claim_posting as scp 
                                on scd.ref_no = scp.ref_no) inner join care_encounter as ce on scd.encounter_nr = ce.encounter_nr 
                             where ($phWhere) 
                    union 
                    select scph.ref_no, scph.encounter_nr, ce.pid, fn_get_pid_lastfirstmi(ce.pid) as full_name, 'PAID' as status, 
                       concat(date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p'), ' to ', (case when ce.discharge_date is null or ce.discharge_date = '' then 'present' else date_format(str_to_date(ce.discharge_date, '%Y-%m-%d %H:%i:%s'), '%b %e, %Y %l:%i%p') end)) as confine_period,    
                       (select insurance_nr from care_person_insurance as cpi where cpi.pid = ce.pid and cpi.hcare_id = scp.hcare_id) as policy_no, 
                       scp.post_dte, scp.hcare_id, fn_get_principalmembername(ce.pid, scp.hcare_id) as member_name  
                             from (seg_claim_pay_hosp as scph inner join seg_claim_posting as scp 
                                on scph.ref_no = scp.ref_no) inner join care_encounter as ce on scph.encounter_nr = ce.encounter_nr 
                             where ($phWhere) 
                    union 
                    select scpp.ref_no, scpp.encounter_nr, ce.pid, fn_get_pid_lastfirstmi(ce.pid) as full_name, 'PAID' as status, 
                       concat(date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p'), ' to ', (case when ce.discharge_date is null or ce.discharge_date = '' then 'present' else date_format(str_to_date(ce.discharge_date, '%Y-%m-%d %H:%i:%s'), '%b %e, %Y %l:%i%p') end)) as confine_period,    
                       (select insurance_nr from care_person_insurance as cpi where cpi.pid = ce.pid and cpi.hcare_id = scp.hcare_id) as policy_no, 
                       scp.post_dte, scp.hcare_id, fn_get_principalmembername(ce.pid, scp.hcare_id) as member_name  
                             from (seg_claim_pay_patient as scpp inner join seg_claim_posting as scp 
                                on scpp.ref_no = scp.ref_no) inner join care_encounter as ce on scpp.encounter_nr = ce.encounter_nr 
                             where ($phWhere) 
                    union 
                    select scpf.ref_no, scpf.encounter_nr, ce.pid, fn_get_pid_lastfirstmi(ce.pid) as full_name, 'PAID' as status, 
                       concat(date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p'), ' to ', (case when ce.discharge_date is null or ce.discharge_date = '' then 'present' else date_format(str_to_date(ce.discharge_date, '%Y-%m-%d %H:%i:%s'), '%b %e, %Y %l:%i%p') end)) as confine_period,    
                       (select insurance_nr from care_person_insurance as cpi where cpi.pid = ce.pid and cpi.hcare_id = scp.hcare_id) as policy_no, 
                       scp.post_dte, scp.hcare_id, fn_get_principalmembername(ce.pid, scp.hcare_id) as member_name  
                             from (seg_claim_pay_pf as scpf inner join seg_claim_posting as scp 
                                on scpf.ref_no = scp.ref_no) inner join care_encounter as ce on scpf.encounter_nr = ce.encounter_nr 
                             where ($phWhere) 
                    union 
                    select scr.ref_no, scr.encounter_nr, ce.pid, fn_get_pid_lastfirstmi(ce.pid) as full_name, 'RETURNED' as status, 
                       concat(date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p'), ' to ', (case when ce.discharge_date is null or ce.discharge_date = '' then 'present' else date_format(str_to_date(ce.discharge_date, '%Y-%m-%d %H:%i:%s'), '%b %e, %Y %l:%i%p') end)) as confine_period,    
                       (select insurance_nr from care_person_insurance as cpi where cpi.pid = ce.pid and cpi.hcare_id = scp.hcare_id) as policy_no, 
                       scp.post_dte, scp.hcare_id, fn_get_principalmembername(ce.pid, scp.hcare_id) as member_name  
                             from (seg_claim_returned as scr inner join seg_claim_posting as scp 
                                on scr.ref_no = scp.ref_no) inner join care_encounter as ce on scr.encounter_nr = ce.encounter_nr
                             where ($phWhere)
                       order by post_dte, fn_get_pid_lastfirstmi(pid) limit $offset, $rowcount";                       
                                       
        if ($this->result = $db->Execute($this->sql))
            return $this->result;
        else { 
//            echo "SQL = ".$this->sql;
            return false;
        }
    }
    
   /**
    * @internal     Delete the posted claim identified by 'ref_no'.
    * @access       public
    * @author       Bong S. Trazo
    * @package      modules
    * @subpackage   billing
    * @global       db - database object
    * 
    * @param        ref_no (string)
    * @return       boolean TRUE if successful, FALSE otherwise.
    */  
    function delPostedClaim($ref_no) {
        $this->sql = "delete from {$this->tb_claim_h} where ref_no = '{$ref_no}'";
        return $this->Transact($this->sql);    
    }
    

    /**
    * @access       public
    * @author       Genesis D. Ortiz
    * @package      modules
    * @subpackage   billing
    * @global       db - database object
    * @return       list of transmit_no.
    */  
    function getListOfPaidTransmitNo($separator_version=1){
      global $db;
        //$db->debug = true;
        $trans_list = array();

        if($separator_version == 1)
          $separator = '"'."','".'"';
        else if($separator_version == 2)
          $separator = "','";

        // $this->sql = "SELECT 
        //               GROUP_CONCAT(
        //                 DISTINCT TRIM(
        //                   TRAILING ',' FROM IF(
        //                     strd.transmit_no IS NULL 
        //                     OR strd.transmit_no = '',
        //                     strd.transmit_no,
        //                     strd.transmit_no
        //                   )
        //                 ) SEPARATOR $separator
        //               ) AS trans_list 
        //             FROM
        //               seg_transmittal_details AS strd 
        //               INNER JOIN seg_claim_pay_hosp AS scph 
        //                 ON scph.`encounter_nr` = strd.`encounter_nr`";

        $this->sql = "SELECT 
                      strd.transmit_no
                    FROM
                      seg_transmittal_details AS strd 
                      INNER JOIN seg_claim_pay_hosp AS scph 
                        ON scph.`encounter_nr` = strd.`encounter_nr`
                      GROUP BY transmit_no";

      if($result=$db->Execute($this->sql)) {
            if ($result->RecordCount()) {
        while ($row = $result->FetchRow()){
            $trans_list[] = $row['transmit_no'];
        }

        $trans_list = implode($separator, $trans_list);
        return $trans_list;

      }
        }
        else
        return '';
    }

     //added by maimai 01-14-2014
    function getTransmittalNoEnc($encounter_nr){
      global $db;

      $this->sql = "SELECT transmit_no FROM seg_transmittal_details WHERE encounter_nr = ".$db->qstr($encounter_nr);

      if($this->result = $db->Execute($this->sql)){
          return $this->result->FetchRow();
      }

      return false;
    }

}
?>