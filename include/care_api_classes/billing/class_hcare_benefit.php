<?php
require_once($root_path.'include/care_api_classes/class_core.php');

class HealthCareBenefit extends Core {
	var $result;
	var $coretable_hdr    = "seg_used_coverage";
	var $coretable_detail = "seg_used_coverage_details";
	var $hdr_flds;
	
	var $error_msg;
	
	function getErrorMsg() {
		return($this->error_msg);
	}
	
	function HealthCareBenefit() {		
		$this->hdr_flds = array('disclose_dte',
								'encounter_nr',
								'modify_id',
								'create_id');													
	}	
	
	function useHeaderTable() {
		$this->coretable = $this->coretable_hdr;
		$this->setRefArray($this->hdr_flds);
	}
	
	function useDetailsTable() {
		$this->coretable = $this->coretable_detail;
	}	
	
	function getIDofUsedCoverageLogged($enc_nr, $frm_dte) {
		global $db;
	
		$sdisclose_id = '';
		$strSQL = "select disclose_id ".
				  "   from seg_used_coverage ".
				  "   where str_to_date(disclose_dte, '%Y-%m-%d %H:%i:%s') >= '".$frm_dte."' ".
				  "      and encounter_nr = '".$enc_nr."' ".
				  "   order by disclose_dte limit 1";			  
		if ($result = $db->Execute($strSQL)) {				
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) 
					$sdisclose_id = $row['disclose_id'];
			}
		}
		
		return($sdisclose_id);	
	}	
	
	function getMinEntry($sdisclose_id, $nhcare_id) {
		global $db;	
	
		$n = 0;
		$strSQL = "select min(entry_no) as no ".
   				  "   from seg_used_coverage_details ".
  				  "   where disclose_id = '".$sdisclose_id."' ".
      			  "      and hcare_id = ".$nhcare_id;	
				  			    
		if ($result = $db->Execute($strSQL)) {				
			if ($result->RecordCount()) {
				$row = $result->FetchRow();
				$n = $row['no'];
			}
		}
		
		return($n);		
	}
	
	function getMaxEntry($sdisclose_id, $nhcare_id) {
		global $db;	
	
		$n = 0;
		$strSQL = "select max(entry_no) as no ".
   				  "   from seg_used_coverage_details ".
  				  "   where disclose_id = '".$sdisclose_id."' ".
      			  "      and hcare_id = ".$nhcare_id;	
				  			    
		if ($result = $db->Execute($strSQL)) {				
			if ($result->RecordCount()) {
				$row = $result->FetchRow();
				$n = $row['no'];
			}
		}
		
		return($n);		
	}	
	
	function delThisEntry($sdisclose_id, $nhcare_id, $nentry_no) {
		global $db;	
		
		$bSuccess = FALSE;
		$this->error_msg = '';
		
		$db->StartTrans();
	
		$strSQL = "delete from ".$this->coretable_detail." ".
  				  "   where disclose_id = '".$sdisclose_id."' ".
      			  "      and hcare_id = ".$nhcare_id." ".
				  "      and entry_no = ".$nentry_no;				  
		$bSuccess = $db->Execute($strSQL);
		
		if ($bSuccess) {
			$strSQL = "delete from seg_used_coverage ".
   					  "   where not exists ".
					  "      (select * from seg_used_coverage_details as sucd ".
                      "         where sucd.disclose_id = seg_used_coverage.disclose_id)";
			$bSuccess = $db->Execute($strSQL);		
		}
						  			    		
		if (!$bSuccess) {
			$db->FailTrans();
			$this->error_msg = $db->ErrorMsg();
		}
				
		$db->CompleteTrans();		
		
		return($bSuccess);			
	} 

	function getHealthCareBenefits() {
		global $db;
		
		$strSQL = "select area_code, (select group_concat(distinct benefit_desc order by benefit_desc separator ', ') ".
				  "                     from (select shb.bill_area, benefit_desc, ".
				  "                             (case when shb.bill_area = 'MS' and instr(ucase(shb.benefit_desc), 'MEDICINE') > 0 then 'MD' else ".
     			  "                                (case when shb.bill_area = 'MS' then 'SP' else shb.bill_area end) end) as area_code ".
				  "                           from seg_hcare_benefits as shb) as t2 where t2.area_code = t1.area_code) as particulars ".
				  "   from (select shb.bill_area, benefit_desc, ".
				  "           (case when shb.bill_area = 'MS' and instr(ucase(shb.benefit_desc), 'MEDICINE') > 0 then 'MD' else ".
                  "              (case when shb.bill_area = 'MS' then 'SP' else shb.bill_area end) end) as area_code ".
				  "           from seg_hcare_benefits as shb) as t1 ".
				  "   group by area_code ".
				  " union ".
 				  "select 'days' as area_code, 'Previous Days Covered' as particulars ".
				  "   order by particulars";				  				  
		if ($this->result = $db->Execute($strSQL)) {
			return $this->result;	 
		} else { return false; }		
	}		
}
?>
