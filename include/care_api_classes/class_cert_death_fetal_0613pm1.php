<?php
# patterned from class_cert_birth.php & class_cert_death.php --- pet --- created on jun3,2008

require_once($root_path.'include/care_api_classes/class_core.php');

class FetalDeathCertificate extends Core{
	// database table for the Fetal Death Certificate info
	var $tb_seg_cert_death_fetal = 'seg_cert_death_fetal';
	// fieldnames of seg_cert_birth_fetal table. Primary key "pid".
	var $fld_seg_cert_death_fetal = array(
				'pid',
				'registry_nr',
				'birth_place_basic',
				'birth_place_mun',
				'birth_place_prov',
				'birth_type',
				'birth_rank',
				'delivery_method',
				'birth_order',
				'birth_weight',
				'm_name_first',
				'm_name_middle',
				'm_name_last',
				'm_citizenship',
				'm_religion',
				'm_occupation',
				'm_age',
				'm_total_alive',
				'm_still_living',
				'm_now_dead',
				'm_residence_basic',
				'm_residence_brgy',
				'm_residence_mun',
				'm_residence_prov',
				'f_name_first',
				'f_name_middle',
				'f_name_last',
				'f_citizenship',
				'f_religion',
				'f_occupation',
				'f_age',
				'parent_marriage_date',
				'parent_marriage_place',
				'death_cause',
				'death_occurrence',				// if fetus died before labor or during labor/delivery (or unknown)
				'pregnancy_length',
				'attendant_type',
				'death_time',				// specific time of death
				'attendant_name',
				'attendant_title',
				'attendant_address',
				'attendant_date_sign',
				'corpse_disposal',
				'burial_permit',
				'burial_date_issued',
				'is_autopsy',
				'cemetery_name_address',
				'informant_name',
				'informant_relation',
				'informant_address',
				'informant_date_sign',
				'encoder_name',
				'encoder_title',
				'encoder_date_sign',
				'history',
				'create_id',
				'create_dt',
				'modify_id',
				'modify_dt'
				);
				
	var $refCode;
	var $result;
	
	/*    
	 * Constructor
	 * @param string primary key refCode
	 */
	function FetalDeathCertificate($refCode){
		if(!empty($refCode)) $this->refCode = $refCode;
		$this->setTable($this->tb_seg_cert_death_fetal);
		$this->setRefArray($this->fld_seg_cert_death_fetal);
	}
	
	/**
	 * Sets the core object point to seg_cert_death_fetal and corresponding field names.
	 * @access private 
	 */
	function _useFetalDeathCertificate(){
		$this->coretable = $this->tb_seg_cert_death_fetal;
		$this->ref_array = $this->fld_seg_cert_death_fetal;
	}
	
	/**
	 * Check if fetal death certificate info exists based on PID
	 * @param string ref_code - PID
	 * @return array of fetal death certificate info else boolean
	 */
	function getFetalDeathCertRecord($refCode=''){
		global $db;
		
		if(empty($refCode) || (!$refCode)){
			$refCode = $this->refCode;
			if(empty($refCode) || (!$refCode))
				return FALSE;		
		}

		if (intval($refCode)){
			$pid_format = " (pid='".$refCode."' OR pid=".$refCode.") ";
		}else{
			$pid_format = " pid='".$refCode."' ";
		}

		$this->sql = "SELECT * FROM $this->tb_seg_cert_death_fetal WHERE $pid_format";
		if($buf = $db->Execute($this->sql)){
			if($buf->RecordCount()){
				return $buf->FetchRow();
			}else { return FALSE; }
		}else { return FALSE; }
	} // end function getFetalDeathCertRecord
		
	/**
	 * Insert new fetal death certificate record into table seg_cert_death_fetal
	 * @param Array Data to by reference
	 * @return boolean
	 */
	function saveFetalDeathCertInfoFromArray(&$data){
		$this->_useFetalDeathCertificate();
		$this->data_array = $data;
		
		return $this->insertDataFromInternalArray();
	}// end function saveFetalDeathCertificateInfoFromArray();
	
	/**
	 * Update death certificate info in table 'seg_cert_death_fetal'
	 * @param Array Data to by reference
	 * @return boolean
	 */
	function updateFetalDeathCertInfoFromArray(&$data){
		global $HTTP_SESSION_VARS, $dbtype;

		$this->_useFetalDeathCertificate();
		$this->data_array=$data;
		// remove probable existing array data to avoid replacing the stored data
		unset($this->data_array['create_id']);
		unset($this->data_array['create_dt']);
		unset($this->data_array['modify_dt']);
		$this->data_array['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];

		if($dbtype=='postgres7'||$dbtype=='postgres') $concatfx='||';
			else $concatfx='concat';

		if (intval($data['pid'])){
			$pid_format = " (pid='".$data['pid']."' OR pid=".$data['pid'].") ";
		}else{
			$pid_format = " pid='".$data['pid']."' ";
		}

		#	Only the keys of data to be updated must be present in the passed array.
		$x='';
		$v='';
		while(list($x,$v)=each($this->ref_array)) {
			$this->buffer_array[$v]=trim($this->data_array[$v]);
		  }

		$elems='';
		while(list($x,$v)=each($this->buffer_array)) {
			# use backquoting for mysql and no-quoting for other dbs. 
			if ($dbtype=='mysql') $elems.="`$x`=";
				else $elems.="$x=";
			
			if(stristr($v,$concatfx)||stristr($v,'null')) $elems.=" $v,";
				else $elems.="'$v',";
		}
		# Bug fix. Reset array.
		reset($this->data_array);
		reset($this->buffer_array);
		$elems=substr_replace($elems,'',(strlen($elems))-1);
        $this->sql="UPDATE $this->coretable SET $elems, modify_dt=NOW() WHERE $pid_format";

		return $this->Transact();
	}// end function updateFetalDeathCertInfoFromArray

	/**
	 * Update death certificate info in table 'seg_cert_death_fetal'
	 * @param Array Data to by reference
	 * @return boolean
	 */
	function updateFetalDeathCertInfoFromArray2(&$data){
		$this->_useFetalDeathCertificate();
		$this->data_array = $data;
		if(isset($this->data_array['pid']))
			unset($this->data_array['pid']);
		
		$this->where="pid='".$data['pid']."'";
		return $this->updateDataFromInternalArray($data['pid'],FALSE);
	}// end function updateFetalDeathCertInfoFromArray2
	
	function getMReligion($religion_num){
		global $db;
		
		$this->sql = "SELECT DISTINCT df.*, r.* FROM seg_cert_death_fetal AS df 
						  LEFT JOIN seg_religion AS r ON df.m_religion=r.religion_nr
						  WHERE r.religion_nr='$religion_num'";			  
		
		if ($this->result=$db->Execute($this->sql)) {
         $this->count=$this->result->RecordCount();
         return $this->result->FetchRow();
      	} else{
         return FALSE;
      	}					  
	}// end function getMReligion	
	
	function getFReligion($religion_num){
		global $db;
		
		$this->sql = "SELECT DISTINCT df.*, r.* FROM seg_cert_death_fetal AS df 
						  LEFT JOIN seg_religion AS r ON df.f_religion=r.religion_nr
						  WHERE r.religion_nr='$religion_num'";			  
		
		if ($this->result=$db->Execute($this->sql)) {
         $this->count=$this->result->RecordCount();
         return $this->result->FetchRow();
      	} else{
         return FALSE;
      	}					  
	}// end function getFReligion	
	
	function getMOccupation($occup){
		global $db;
		
		$this->sql = "SELECT DISTINCT df.*, o.* FROM seg_cert_death_fetal AS df 
						  LEFT JOIN seg_occupation AS o ON df.m_occupation=o.occupation_nr
						  WHERE o.occupation_nr='$occup'";			  
		
		if ($this->result=$db->Execute($this->sql)) {
         $this->count=$this->result->RecordCount();
         return $this->result->FetchRow();
      	} else{
         return FALSE;
      	}					  
	}// end function getMOccupation
	
	function getFOccupation($occup){
		global $db;
		
		$this->sql = "SELECT DISTINCT df.*, o.* FROM seg_cert_death_fetal AS df 
						  LEFT JOIN seg_occupation AS o ON df.f_occupation=o.occupation_nr
						  WHERE o.occupation_nr='$occup'";			  
		
		if ($this->result=$db->Execute($this->sql)) {
         $this->count=$this->result->RecordCount();
         return $this->result->FetchRow();
      	} else{
         return FALSE;
      	}					  
	}// end function getFOccupation	
	
	function getCitizenship(){
	    global $db;
		$this->sql="SELECT * FROM seg_country ORDER BY citizenship ASC";
						 
	    if ($this->result=$db->Execute($this->sql)) {
		    if ($this->result->RecordCount()) {
		        return $this->result;
			} else {
				return FALSE;
			}
		} else {
		    return FALSE;
		}
	}// end function getCitizenship	
		
}// end class FetalDeathCertificate

?>